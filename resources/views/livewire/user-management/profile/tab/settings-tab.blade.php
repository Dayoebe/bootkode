<div
    id="profile-panel-account"
    role="tabpanel"
    aria-labelledby="profile-tab-account"
    x-show="profileTab === 'account'"
    x-cloak
>
    <header class="bk-profile-section-header">
        <div><span>Account controls</span><h2>Preferences, privacy and security</h2><p>Every control below changes your own authenticated account or opens a dedicated settings surface.</p></div>
    </header>

    <div class="bk-profile-card-grid">
        <section class="bk-profile-card">
            <div class="bk-profile-card-heading"><span><i class="fas fa-palette" aria-hidden="true"></i></span><div><h3>Appearance</h3><p>The selected theme applies across the dashboard.</p></div></div>
            <div class="bk-profile-theme-grid" role="group" aria-label="Dashboard theme">
                @foreach([
                    'light' => ['Light', 'fa-sun'],
                    'dark' => ['Dark', 'fa-moon'],
                    'sepia' => ['Sepia', 'fa-book-open'],
                    'ocean' => ['Ocean', 'fa-water'],
                    'forest' => ['Forest', 'fa-tree'],
                ] as $themeKey => [$themeLabel, $themeIcon])
                    <button type="button" @click="setTheme('{{ $themeKey }}')" :aria-pressed="(theme === '{{ $themeKey }}').toString()" :class="{ 'is-active': theme === '{{ $themeKey }}' }">
                        <i class="fas {{ $themeIcon }}" aria-hidden="true"></i><span>{{ $themeLabel }}</span><i class="fas fa-check" aria-hidden="true" x-show="theme === '{{ $themeKey }}'"></i>
                    </button>
                @endforeach
            </div>
        </section>

        <section class="bk-profile-card">
            <div class="bk-profile-card-heading"><span><i class="fas fa-shield-halved" aria-hidden="true"></i></span><div><h3>Privacy and notifications</h3><p>Fast controls for this account.</p></div></div>
            <div class="bk-profile-setting-list">
                <label>
                    <span><strong>Course update emails</strong><small>Announcements and updates for your learning.</small></span>
                    <input type="checkbox" wire:model.live="emailNotifications" wire:change="updateEmailNotifications" class="bk-profile-switch" aria-label="Course update emails">
                </label>
                <label>
                    <span><strong>Certificate notifications</strong><small>Recorded certificate review updates.</small></span>
                    <input type="checkbox" wire:model.live="certificateNotifications" wire:change="updateCertificateNotifications" class="bk-profile-switch" aria-label="Certificate notifications">
                </label>
                <label>
                    <span><strong>Show email publicly</strong><small>Allow public profile surfaces to display your email.</small></span>
                    <input type="checkbox" wire:model.live="showEmail" wire:change="updateShowEmail" class="bk-profile-switch" aria-label="Show email publicly">
                </label>
                <label for="profile-visibility">
                    <span><strong>Profile visibility</strong><small>Controls who may see supported public profile information.</small></span>
                    <select id="profile-visibility" wire:model.live="profileVisibility" wire:change="updateProfileVisibility" class="bk-input">
                        <option value="public">Public</option>
                        <option value="private">Private</option>
                        <option value="students">Students only</option>
                    </select>
                </label>
                <x-input-error :messages="$errors->get('profileVisibility')" />
            </div>
            <a class="bk-profile-inline-link mt-4" href="{{ route('privacy.settings') }}" wire:navigate>Open detailed privacy controls <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
        </section>

        <section class="bk-profile-card">
            <div class="bk-profile-card-heading"><span><i class="fas fa-key" aria-hidden="true"></i></span><div><h3>Security</h3><p>Password and browser-session controls.</p></div></div>
            <div class="bk-profile-action-list">
                <button type="button" wire:click="$set('showPasswordModal', true)"><span><i class="fas fa-lock" aria-hidden="true"></i><span><strong>Change password</strong><small>Verify your current password first.</small></span></span><i class="fas fa-chevron-right" aria-hidden="true"></i></button>
                <button type="button" wire:click="$set('showSessionsModal', true)"><span><i class="fas fa-right-from-bracket" aria-hidden="true"></i><span><strong>Sign out other browsers</strong><small>Keep this browser signed in.</small></span></span><i class="fas fa-chevron-right" aria-hidden="true"></i></button>
            </div>
        </section>

        <section class="bk-profile-card">
            <div class="bk-profile-card-heading"><span><i class="fas fa-database" aria-hidden="true"></i></span><div><h3>Your data</h3><p>Download a JSON copy of core profile and learning counts.</p></div></div>
            <p class="bk-profile-card-copy">The export is generated for the signed-in account only. It does not change or delete any record.</p>
            <button type="button" class="bk-button bk-button-secondary mt-4" wire:click="exportData" wire:loading.attr="disabled" wire:target="exportData"><i class="fas fa-download" aria-hidden="true"></i><span wire:loading.remove wire:target="exportData">Download my data</span><span wire:loading wire:target="exportData">Preparing…</span></button>
        </section>
    </div>

    <section class="bk-profile-danger-zone" aria-labelledby="profile-danger-title">
        <div><span><i class="fas fa-triangle-exclamation" aria-hidden="true"></i></span><div><h3 id="profile-danger-title">Account danger zone</h3><p>These actions affect access to your account. Historical learning, financial, or credential records may prevent deletion.</p></div></div>
        <div>
            @if($user->isSuperAdmin())
                <p class="bk-profile-danger-note"><i class="fas fa-lock" aria-hidden="true"></i> Super-admin accounts cannot be self-deactivated.</p>
            @else
                <button type="button" class="bk-button bk-button-danger-outline" wire:click="$set('showDeactivateModal', true)">Deactivate account</button>
            @endif
            <button type="button" class="bk-button bk-button-danger" wire:click="$set('showDeleteModal', true)">Delete account</button>
        </div>
    </section>
</div>

<x-modal name="profile-password" model="showPasswordModal" maxWidth="md" label="Change account password">
    <form wire:submit="changePassword" class="bk-profile-dialog">
        <header><span><i class="fas fa-key" aria-hidden="true"></i></span><div><h2>Change password</h2><p>Confirm your current password and choose a new one.</p></div></header>
        <div class="space-y-4">
            <div><x-input-label for="profile-current-password" value="Current password" /><x-text-input id="profile-current-password" type="password" class="mt-2 block w-full" wire:model="current_password" autocomplete="current-password" required /><x-input-error :messages="$errors->get('current_password')" class="mt-2" /></div>
            <div><x-input-label for="profile-new-password" value="New password" /><x-text-input id="profile-new-password" type="password" class="mt-2 block w-full" wire:model="new_password" autocomplete="new-password" required /><x-input-error :messages="$errors->get('new_password')" class="mt-2" /></div>
            <div><x-input-label for="profile-new-password-confirmation" value="Confirm new password" /><x-text-input id="profile-new-password-confirmation" type="password" class="mt-2 block w-full" wire:model="new_password_confirmation" autocomplete="new-password" required /></div>
        </div>
        <footer><button type="button" class="bk-button bk-button-secondary" wire:click="resetModalStates">Cancel</button><button type="submit" class="bk-button bk-button-primary">Update password</button></footer>
    </form>
</x-modal>

<x-modal name="profile-sessions" model="showSessionsModal" maxWidth="md" label="Sign out other browser sessions">
    <form wire:submit="logoutOtherSessions" class="bk-profile-dialog">
        <header><span><i class="fas fa-right-from-bracket" aria-hidden="true"></i></span><div><h2>Sign out other browsers</h2><p>This browser remains signed in. Enter your password to confirm.</p></div></header>
        <div><x-input-label for="profile-session-password" value="Current password" /><x-text-input id="profile-session-password" type="password" class="mt-2 block w-full" wire:model="current_password" autocomplete="current-password" required /><x-input-error :messages="$errors->get('current_password')" class="mt-2" /></div>
        <footer><button type="button" class="bk-button bk-button-secondary" wire:click="resetModalStates">Cancel</button><button type="submit" class="bk-button bk-button-primary">Sign out others</button></footer>
    </form>
</x-modal>

<x-modal name="profile-deactivate" model="showDeactivateModal" maxWidth="md" label="Deactivate account">
    <form wire:submit="deactivateAccount" class="bk-profile-dialog">
        <header class="is-danger"><span><i class="fas fa-user-slash" aria-hidden="true"></i></span><div><h2>Deactivate account</h2><p>Your account will become inactive and this browser will be signed out.</p></div></header>
        <div><x-input-label for="profile-deactivate-password" value="Current password" /><x-text-input id="profile-deactivate-password" type="password" class="mt-2 block w-full" wire:model="deactivate_password" autocomplete="current-password" required /><x-input-error :messages="$errors->get('deactivate_password')" class="mt-2" /></div>
        <footer><button type="button" class="bk-button bk-button-secondary" wire:click="resetModalStates">Cancel</button><button type="submit" class="bk-button bk-button-danger">Deactivate account</button></footer>
    </form>
</x-modal>

<x-modal name="profile-delete" model="showDeleteModal" maxWidth="md" label="Permanently delete account">
    <form wire:submit="deleteAccount" class="bk-profile-dialog">
        <header class="is-danger"><span><i class="fas fa-trash" aria-hidden="true"></i></span><div><h2>Delete account permanently</h2><p>This cannot be undone. Protected learning, payment, or credential history will block deletion.</p></div></header>
        <div class="space-y-4">
            <div><x-input-label for="profile-delete-password" value="Current password" /><x-text-input id="profile-delete-password" type="password" class="mt-2 block w-full" wire:model="delete_password" autocomplete="current-password" required /><x-input-error :messages="$errors->get('delete_password')" class="mt-2" /></div>
            <div><x-input-label for="profile-delete-confirmation" value="Type DELETE to confirm" /><x-text-input id="profile-delete-confirmation" type="text" class="mt-2 block w-full" wire:model="delete_confirmation" autocomplete="off" required /><x-input-error :messages="$errors->get('delete_confirmation')" class="mt-2" /></div>
        </div>
        <footer><button type="button" class="bk-button bk-button-secondary" wire:click="resetModalStates">Cancel</button><button type="submit" class="bk-button bk-button-danger">Delete permanently</button></footer>
    </form>
</x-modal>
