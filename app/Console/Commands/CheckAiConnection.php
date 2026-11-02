<?php

namespace App\Console\Commands;

use App\Exceptions\AiProviderException;
use App\Services\Ai\AiConnectionService;
use Illuminate\Console\Command;

class CheckAiConnection extends Command
{
    protected $signature = 'bootkode:ai-check';

    protected $description = 'Send a small structured AI connection probe without sending course or learner data';

    public function handle(AiConnectionService $connection): int
    {
        try {
            $result = $connection->check();
            $this->info(ucfirst($result['provider']).': '.$result['message'].' ('.$result['duration_ms'].' ms)');

            return self::SUCCESS;
        } catch (AiProviderException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }
}
