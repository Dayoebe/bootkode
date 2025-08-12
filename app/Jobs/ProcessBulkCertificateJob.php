<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\BulkIssuance;
use App\Models\BulkIssuanceCertificate;
use App\Models\User;
use App\Models\Certificate;
use App\Services\CertificateGenerator;
use League\Csv\Reader;
use Illuminate\Support\Facades\Storage;

class ProcessBulkCertificateJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $bulkIssuanceId
    ) {}

    public function handle(CertificateGenerator $generator)
    {
        $issuance = BulkIssuance::find($this->bulkIssuanceId);
        $csv = Reader::createFromPath(Storage::path($issuance->file_path), 'r');
        $csv->setHeaderOffset(0);

        $total = count($csv);
        $processed = 0;
        $successCount = 0;
        $failureCount = 0;
        $failureReasons = [];

        foreach ($csv as $record) {
            // Check if batch cancelled
            if ($this->batch()->cancelled()) {
                break;
            }

            $email = $record['email'] ?? null;
            $userId = $record['user_id'] ?? null;
            
            // Find user
            $user = $userId 
                ? User::find($userId)
                : ($email ? User::where('email', $email)->first() : null);

            $certificateRecord = BulkIssuanceCertificate::create([
                'bulk_issuance_id' => $issuance->id,
                'user_id' => $user ? $user->id : null,
                'status' => BulkIssuanceCertificate::STATUS_PENDING
            ]);

            try {
                if (!$user) {
                    throw new \Exception("User not found");
                }

                // Check if certificate already exists
                $exists = Certificate::where('user_id', $user->id)
                    ->where('course_id', $issuance->course_id)
                    ->exists();

                if ($exists) {
                    throw new \Exception("Certificate already exists for this user and course");
                }

                // Create certificate
                $certificate = Certificate::create([
                    'user_id' => $user->id,
                    'course_id' => $issuance->course_id,
                    'template_id' => $issuance->template_id,
                    'certificate_number' => $this->generateCertificateNumber(),
                    'verification_code' => $this->generateVerificationCode(),
                    'status' => 'approved',
                    'issued_at' => now(),
                    'issuer_id' => $issuance->issuer_id,
                ]);

                // Generate PDF
                $path = $generator->generatePdf($certificate);
                $certificate->update(['pdf_path' => $path]);

                $certificateRecord->update([
                    'certificate_id' => $certificate->id,
                    'status' => BulkIssuanceCertificate::STATUS_SUCCESS
                ]);

                $successCount++;
            } catch (\Exception $e) {
                $certificateRecord->update([
                    'status' => BulkIssuanceCertificate::STATUS_FAILED,
                    'reason' => $e->getMessage()
                ]);

                $failureCount++;
                $failureReasons[] = $e->getMessage();
            }

            $processed++;
            $progress = round(($processed / $total) * 100);
            $this->batch()->progress($progress);
        }

        // Update bulk issuance record
        $issuance->update([
            'success_count' => $successCount,
            'failure_count' => $failureCount,
            'failure_reasons' => array_count_values($failureReasons)
        ]);
    }

    private function generateCertificateNumber(): string
    {
        return 'BK-BULK-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
    }

    private function generateVerificationCode(): string
    {
        return hash('sha256', Str::uuid());
    }
}