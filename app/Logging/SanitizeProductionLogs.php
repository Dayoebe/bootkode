<?php

namespace App\Logging;

use Illuminate\Log\Logger;

class SanitizeProductionLogs
{
    public function __invoke(Logger $logger): void
    {
        $logger->pushProcessor(new ProductionLogProcessor);
    }
}
