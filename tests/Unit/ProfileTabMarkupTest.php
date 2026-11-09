<?php

namespace Tests\Unit;

use Tests\TestCase;

class ProfileTabMarkupTest extends TestCase
{
    public function test_profile_tabs_are_local_controls_with_matching_panels(): void
    {
        $profile = file_get_contents(resource_path('views/livewire/user-management/profile.blade.php'));
        $viewPanels = file_get_contents(resource_path('views/livewire/user-management/profile/view-profile.blade.php'))
            .file_get_contents(resource_path('views/livewire/user-management/profile/tab/settings-tab.blade.php'));
        $editPanels = file_get_contents(resource_path('views/livewire/user-management/profile/edit-profile.blade.php'));

        $this->assertStringNotContainsString('@entangle', $profile);
        $this->assertStringContainsString("x-data=\"{ profileTab:", $profile);
        $this->assertStringContainsString("@click=\"profileTab = '", $profile);
        $this->assertStringContainsString('$wire.switchTab', $profile);
        $this->assertStringContainsString('@keydown.arrow-down.prevent', $profile);
        $this->assertStringContainsString('@keydown.home.prevent', $profile);

        foreach (['overview', 'learning', 'saved', 'account'] as $tab) {
            $this->assertStringContainsString("x-show=\"profileTab === '{$tab}'\"", $viewPanels, "Missing panel for {$tab}");
            $this->assertStringContainsString("id=\"profile-panel-{$tab}\"", $viewPanels, "Missing panel id for {$tab}");
        }

        foreach (['identity', 'details', 'links'] as $tab) {
            $this->assertStringContainsString("x-show=\"profileTab === '{$tab}'\"", $editPanels, "Missing edit panel for {$tab}");
            $this->assertStringContainsString("id=\"profile-panel-{$tab}\"", $editPanels, "Missing edit panel id for {$tab}");
        }

        $this->assertStringNotContainsString('<style>', $profile);
        $this->assertStringNotContainsString('v-if=', $viewPanels);
        $this->assertStringNotContainsString('wire:click="toggle2FA"', $viewPanels);
        $this->assertStringNotContainsString('wire:click="clearCache"', $viewPanels);
        $this->assertStringContainsString('wire:submit="changePassword"', $viewPanels);
        $this->assertStringContainsString('wire:submit="logoutOtherSessions"', $viewPanels);
    }

    public function test_sidebar_navigation_rows_are_not_hidden_by_x_cloak(): void
    {
        $sidebar = file_get_contents(resource_path('views/livewire/dashboard/layout/dashboard-sidebar.blade.php'));

        $this->assertDoesNotMatchRegularExpression(
            '/x-show="menuMatches\([^\n]+\)"\s+x-cloak\s+class="menu-item"/',
            $sidebar,
        );
        $this->assertStringContainsString('x-show="menuMatches(', $sidebar);
        $this->assertStringContainsString('class="menu-item"', $sidebar);
    }
}
