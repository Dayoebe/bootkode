<?php

namespace Tests\Feature;

use Tests\TestCase;

class PwaInstallabilityTest extends TestCase
{
    public function test_manifest_has_installable_identity_and_real_icons(): void
    {
        $manifest = json_decode(file_get_contents(public_path('manifest.webmanifest')), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('/', $manifest['id']);
        $this->assertSame('/', $manifest['start_url']);
        $this->assertSame('/', $manifest['scope']);
        $this->assertSame('standalone', $manifest['display']);
        $this->assertSame('en-NG', $manifest['lang']);
        $this->assertFalse($manifest['prefer_related_applications']);

        foreach ([192, 512] as $size) {
            $icon = public_path("icons/icon-{$size}.png");
            $this->assertFileExists($icon);
            [$width, $height] = getimagesize($icon);
            $this->assertSame([$size, $size], [$width, $height]);
        }
    }

    public function test_service_worker_uses_versioned_static_cache_and_explicit_private_pack_rules(): void
    {
        $worker = file_get_contents(public_path('service-worker.js'));

        $this->assertStringContainsString("const CACHE_VERSION = 'bootkode-pwa-", $worker);
        $this->assertStringContainsString("request.mode === 'navigate'", $worker);
        $this->assertStringContainsString("requestUrl.pathname.startsWith('/storage/')", $worker);
        $this->assertStringContainsString("request.method !== 'GET'", $worker);
        $this->assertStringContainsString("event.data?.type === 'SKIP_WAITING'", $worker);
        $this->assertStringNotContainsString('cache.put(request, fetch(request))', $worker);
    }

    public function test_public_and_authenticated_shells_include_one_shared_install_prompt(): void
    {
        $publicLayout = file_get_contents(resource_path('views/layouts/app.blade.php'));
        $dashboardLayout = file_get_contents(resource_path('views/layouts/dashboard.blade.php'));

        $this->assertSame(1, substr_count($publicLayout, '<x-pwa-install-prompt />'));
        $this->assertSame(1, substr_count($dashboardLayout, '<x-pwa-install-prompt />'));
        $this->assertStringNotContainsString("navigator.serviceWorker.register('/service-worker.js')", $publicLayout);
        $this->assertStringNotContainsString("navigator.serviceWorker.register('/service-worker.js')", $dashboardLayout);
    }
}
