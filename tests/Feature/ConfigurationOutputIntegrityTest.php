<?php

namespace Tests\Feature;

use Tests\TestCase;

class ConfigurationOutputIntegrityTest extends TestCase
{
    public function test_loading_configuration_emits_no_bytes_that_could_corrupt_downloads(): void
    {
        foreach (glob(config_path('*.php')) as $file) {
            ob_start();
            try { require $file; }
            finally { $output = ob_get_clean(); }
            $this->assertSame('', $output, basename($file).' emitted output before the HTTP response.');
        }
    }
}
