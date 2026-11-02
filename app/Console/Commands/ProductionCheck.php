<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class ProductionCheck extends Command
{
    protected $signature = 'bootkode:production-check {--skip-database : Report database verification as incomplete} {--json : Emit safe structured results}';

    protected $description = 'Read-only deployment checks; does not print credentials or change application data';

    public function handle(): int
    {
        $results = [];
        $check = function (string $name, bool $passed, string $detail) use (&$results): void {
            $results[] = ['check' => $name, 'passed' => $passed, 'detail' => $detail];
        };
        $check('environment', app()->environment('production'), 'Use APP_ENV=production on the release.');
        $check('debug', config('app.debug') === false, 'Debug responses must be disabled.');
        $check('https', parse_url((string) config('app.url'), PHP_URL_SCHEME) === 'https', 'Configure the real HTTPS application URL.');
        $check('application_key', filled(config('app.key')), 'Provision a stable private APP_KEY; retain it across releases and restores.');
        $check('session_cookie', config('session.secure') === true && config('session.http_only') === true, 'Require Secure and HttpOnly session cookies.');
        $check('queue', in_array(config('queue.default'), ['database', 'redis'], true), 'Background work requires an asynchronous database or Redis queue.');
        $check('course_queue', in_array(config('queue.connections.course-content.driver'), ['database', 'redis'], true), 'Course jobs must use the isolated asynchronous connection.');
        $check('bulk_queue', in_array(config('queue.connections.bulk-enrollment.driver'), ['database', 'redis'], true), 'Bulk jobs must use the isolated asynchronous connection.');
        $mode = config('production.queue_mode');
        $check('queue_runner', in_array($mode, ['cron', 'supervisor'], true), 'Declare a tested cron or supervised runner; no worker is installed by this command.');
        $check('cron_runtime', $mode !== 'cron' || config('production.cron_max_runtime') >= 660, 'Course jobs need at least 660 seconds of CLI runtime for the documented cron burst.');
        $check('pcntl', extension_loaded('pcntl'), 'CLI pcntl is needed to enforce queue timeouts.');
        $check('mail', ! in_array(config('mail.default'), ['log', 'array', 'null'], true), 'Configure and validate transactional email; log mail exposes verification/reset links.');
        $check('assets', is_file(public_path('build/manifest.json')) && ! is_file(public_path('hot')), 'Build locked assets and exclude the Vite hot file from a release.');
        foreach ([storage_path(), base_path('bootstrap/cache')] as $index => $path) {
            $check('writable_directory_'.$index, is_writable($path) && (fileperms($path) & 0002) === 0, 'Runtime directories must be writable by the application identity without world-write permissions.');
        }
        $check('cache', in_array(config('cache.default'), ['database', 'redis', 'file'], true), 'Use a persistent cache with atomic locks; arrays cannot coordinate workers.');
        $check('log_rotation', in_array(config('logging.default'), ['daily', 'stderr'], true), 'Use daily rotation or host-managed stderr retention.');
        $check('framework_support', version_compare(app()->version(), '12.0.0', '>='), 'Laravel 11 security support ended 12 March 2026. A reviewed supported-version upgrade remains required.');

        if ($this->option('skip-database')) {
            $check('database', false, 'Database checks were skipped; readiness is incomplete.');
        } else {
            try {
                DB::select('SELECT 1');
                foreach (['migrations', 'jobs', 'failed_jobs', 'cache', 'cache_locks', 'sessions'] as $table) {
                    $check('table_'.$table, Schema::hasTable($table), 'Required runtime table must exist after reviewed forward migrations.');
                }
                $ran = Schema::hasTable('migrations') ? DB::table('migrations')->pluck('migration')->all() : [];
                $files = array_map(fn ($file) => basename($file, '.php'), glob(database_path('migrations/*.php')) ?: []);
                $check('migrations', array_diff($files, $ran) === [], 'Review and rehearse pending migrations against a restored copy before using migrate --force.');
                $failed = Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 0;
                $check('failed_jobs', $failed === 0, 'Investigate failed jobs privately before launch; do not blindly retry payments or mail.');
                if (Schema::hasTable('newsletter_interactions')) {
                    $uncertain = DB::table('newsletter_interactions')->where('status', 'delivery_unknown')->exists();
                    $stale = DB::table('newsletter_interactions')->where('status', 'processing')->where('updated_at', '<', now()->subMinutes(15))->exists();
                    $check('newsletter_delivery', ! $uncertain && ! $stale, 'Uncertain or interrupted SMTP delivery requires provider reconciliation before re-sending.');
                }
            } catch (Throwable $exception) {
                $check('database', false, 'Database verification failed ('.class_basename($exception).'); connection details withheld.');
            }
        }

        if ($this->option('json')) {
            $this->line(json_encode($results, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
        } else {
            $this->table(['Check', 'Result', 'Action'], array_map(fn ($item) => [$item['check'], $item['passed'] ? 'PASS' : 'BLOCKED', $item['detail']], $results));
        }

        return collect($results)->every('passed') ? self::SUCCESS : self::FAILURE;
    }
}
