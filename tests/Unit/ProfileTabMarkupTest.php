<?php

namespace Tests\Unit;

use Tests\TestCase;

class ProfileTabMarkupTest extends TestCase
{
    public function test_profile_tabs_are_local_controls_with_matching_panels(): void
    {
        $profile = file_get_contents(resource_path('views/livewire/user-management/profile.blade.php'));
        $viewPanels = file_get_contents(resource_path('views/livewire/user-management/profile/view-profile.blade.php'))
            .file_get_contents(resource_path('views/livewire/user-management/profile/tab/wishlist-tab.blade.php'))
            .file_get_contents(resource_path('views/livewire/user-management/profile/tab/certificate-tab.blade.php'))
            .file_get_contents(resource_path('views/livewire/user-management/profile/tab/settings-tab.blade.php'));
        $editPanels = file_get_contents(resource_path('views/livewire/user-management/profile/edit-profile.blade.php'));

        $this->assertStringNotContainsString('@entangle', $profile);
        $this->assertStringContainsString('type="button" @click="activeTab =', $profile);

        foreach (['personal', 'education', 'social', 'activity', 'progress', 'resources', 'wishlist', 'certificates', 'settings'] as $tab) {
            $this->assertStringContainsString("x-show=\"activeTab === '{$tab}'\"", $viewPanels, "Missing panel for {$tab}");
        }

        foreach (['basic', 'address', 'education', 'social', 'photo'] as $tab) {
            $this->assertStringContainsString("x-show=\"activeTab === '{$tab}'\"", $editPanels, "Missing edit panel for {$tab}");
        }
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
