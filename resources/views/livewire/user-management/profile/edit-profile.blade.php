<div
    id="profile-panel-identity"
    role="tabpanel"
    aria-labelledby="profile-tab-identity"
    x-show="profileTab === 'identity'"
    x-cloak
>
    <header class="bk-profile-section-header">
        <div><span>Edit identity</span><h2>Personal information</h2><p>Keep your name and contact details accurate. Changing your email requires verification again.</p></div>
    </header>

    <section class="bk-profile-card">
        <div class="bk-profile-form-grid">
            <div>
                <x-input-label for="profile-name" value="Full name" />
                <x-text-input id="profile-name" type="text" class="mt-2 block w-full" wire:model="name" autocomplete="name" required />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="profile-email" value="Email address" />
                <x-text-input id="profile-email" type="email" class="mt-2 block w-full" wire:model="email" autocomplete="email" required />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="profile-phone" value="Phone number" />
                <x-text-input id="profile-phone" type="tel" class="mt-2 block w-full" wire:model="phone_number" autocomplete="tel" placeholder="+234…" />
                <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="profile-birth-date" value="Date of birth" />
                <x-text-input id="profile-birth-date" type="date" class="mt-2 block w-full" wire:model="date_of_birth" autocomplete="bday" />
                <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                <p class="bk-field-help">Accounts are limited to people aged 13 or older.</p>
            </div>
            <div class="sm:col-span-2">
                <x-input-label for="profile-bio" value="Short introduction" />
                <textarea id="profile-bio" class="bk-input mt-2 block min-h-32 w-full resize-y" wire:model="bio" maxlength="500" aria-describedby="profile-bio-help"></textarea>
                <p id="profile-bio-help" class="bk-field-help">Up to 500 characters. Plain text only.</p>
                <x-input-error :messages="$errors->get('bio')" class="mt-2" />
            </div>
        </div>
    </section>
</div>

<div
    id="profile-panel-details"
    role="tabpanel"
    aria-labelledby="profile-tab-details"
    x-show="profileTab === 'details'"
    x-cloak
>
    <header class="bk-profile-section-header">
        <div><span>Edit background</span><h2>Location, education and skills</h2><p>Add only details you are comfortable storing on your account.</p></div>
    </header>

    <div class="space-y-5">
        <section class="bk-profile-card">
            <div class="bk-profile-card-heading"><span><i class="fas fa-location-dot" aria-hidden="true"></i></span><div><h3>Location</h3><p>Your address remains private unless another feature explicitly says otherwise.</p></div></div>
            <div class="bk-profile-form-grid mt-5">
                <div class="sm:col-span-2">
                    <x-input-label for="profile-street" value="Street address" />
                    <x-text-input id="profile-street" type="text" class="mt-2 block w-full" wire:model="address_street" autocomplete="street-address" />
                    <x-input-error :messages="$errors->get('address_street')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="profile-city" value="City" />
                    <x-text-input id="profile-city" type="text" class="mt-2 block w-full" wire:model="address_city" autocomplete="address-level2" />
                    <x-input-error :messages="$errors->get('address_city')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="profile-state" value="State or province" />
                    <x-text-input id="profile-state" type="text" class="mt-2 block w-full" wire:model="address_state" autocomplete="address-level1" />
                    <x-input-error :messages="$errors->get('address_state')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="profile-country" value="Country" />
                    <x-text-input id="profile-country" type="text" class="mt-2 block w-full" wire:model="address_country" autocomplete="country-name" />
                    <x-input-error :messages="$errors->get('address_country')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="profile-postal-code" value="Postal code" />
                    <x-text-input id="profile-postal-code" type="text" class="mt-2 block w-full" wire:model="address_postal_code" autocomplete="postal-code" />
                    <x-input-error :messages="$errors->get('address_postal_code')" class="mt-2" />
                </div>
            </div>
        </section>

        <section class="bk-profile-card">
            <div class="bk-profile-card-heading"><span><i class="fas fa-graduation-cap" aria-hidden="true"></i></span><div><h3>Professional background</h3><p>Self-reported context, not a verified credential or assessment result.</p></div></div>
            <div class="bk-profile-form-grid mt-5">
                <div>
                    <x-input-label for="profile-occupation" value="Occupation" />
                    <x-text-input id="profile-occupation" type="text" class="mt-2 block w-full" wire:model="occupation" autocomplete="organization-title" />
                    <x-input-error :messages="$errors->get('occupation')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="profile-education" value="Education level" />
                    <select id="profile-education" wire:model="education_level" class="bk-input mt-2 block w-full">
                        <option value="">Select a level</option>
                        <option value="High School">High School</option>
                        <option value="Diploma">Diploma</option>
                        <option value="Bachelor's Degree">Bachelor's Degree</option>
                        <option value="Master's Degree">Master's Degree</option>
                        <option value="PhD">PhD</option>
                        <option value="Other">Other</option>
                    </select>
                    <x-input-error :messages="$errors->get('education_level')" class="mt-2" />
                </div>
                <div class="sm:col-span-2">
                    <x-input-label for="profile-skills" value="Skills and interests" />
                    <textarea id="profile-skills" class="bk-input mt-2 block min-h-28 w-full resize-y" wire:model="skills" maxlength="255" aria-describedby="profile-skills-help"></textarea>
                    <p id="profile-skills-help" class="bk-field-help">Separate items with commas, for example: Laravel, UI design, data analysis.</p>
                    <x-input-error :messages="$errors->get('skills')" class="mt-2" />
                </div>
            </div>
        </section>
    </div>
</div>

<div
    id="profile-panel-links"
    role="tabpanel"
    aria-labelledby="profile-tab-links"
    x-show="profileTab === 'links'"
    x-cloak
>
    <header class="bk-profile-section-header">
        <div><span>Edit links and photo</span><h2>Public identity</h2><p>Use safe HTTP(S) links and a clear account image. Saving the profile applies every edited section.</p></div>
    </header>

    <div class="bk-profile-card-grid">
        <section class="bk-profile-card">
            <div class="bk-profile-card-heading"><span><i class="fas fa-camera" aria-hidden="true"></i></span><div><h3>Profile photo</h3><p>JPG, PNG, or GIF up to 2 MB</p></div></div>
            <div class="bk-profile-photo-editor">
                <div class="bk-profile-photo-preview">
                    @if($temp_profile_picture)
                        <img src="{{ $temp_profile_picture }}" alt="New profile photo preview">
                    @elseif($user->profile_picture)
                        <img src="{{ $user->profile_picture }}" alt="Current profile photo">
                    @else
                        <span aria-hidden="true">{{ str($user->name)->substr(0, 1)->upper() }}</span>
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <x-input-label for="profile-picture" value="Choose an image" />
                    <input id="profile-picture" type="file" wire:model="profile_picture" accept="image/jpeg,image/png,image/gif" class="bk-profile-file mt-2 block w-full">
                    <x-input-error :messages="$errors->get('profile_picture')" class="mt-2" />
                    <p class="bk-field-help" wire:loading wire:target="profile_picture" role="status">Preparing image preview…</p>
                    @if($user->profile_picture)
                        <button type="button" class="bk-profile-danger-link mt-3" wire:click="deleteProfilePicture"><i class="fas fa-trash" aria-hidden="true"></i> Remove current photo</button>
                    @endif
                </div>
            </div>
        </section>

        <section class="bk-profile-card">
            <div class="bk-profile-card-heading"><span><i class="fas fa-link" aria-hidden="true"></i></span><div><h3>Connected profiles</h3><p>All links must use HTTP or HTTPS</p></div></div>
            <div class="space-y-4 mt-5">
                @foreach([
                    'website' => ['Website', 'https://example.com'],
                    'github' => ['GitHub', 'https://github.com/username'],
                    'linkedin' => ['LinkedIn', 'https://linkedin.com/in/username'],
                    'twitter' => ['X / Twitter', 'https://x.com/username'],
                    'facebook' => ['Facebook', 'https://facebook.com/username'],
                    'instagram' => ['Instagram', 'https://instagram.com/username'],
                ] as $network => [$label, $placeholder])
                    <div>
                        <x-input-label for="profile-link-{{ $network }}" :value="$label" />
                        <x-text-input id="profile-link-{{ $network }}" type="url" class="mt-2 block w-full" wire:model="social_links.{{ $network }}" :placeholder="$placeholder" inputmode="url" />
                        <x-input-error :messages="$errors->get('social_links.'.$network)" class="mt-2" />
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</div>
