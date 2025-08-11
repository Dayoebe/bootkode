<div>
    <form wire:submit.prevent="updateProfile">
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

      

        <!-- Role Selection -->
        <div>
            <x-input-label for="role" :value="__('Account Type')" />
            <select wire:model="role" id="role" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="student">Student</option>
                <option value="instructor">Instructor</option>
                <option value="mentor">Mentor</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Date of Birth -->
        <div>
            <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
            <x-text-input wire:model="date_of_birth" id="date_of_birth" class="block mt-1 w-full" type="date" />
            <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
        </div>

        <!-- Address Fields -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="address_street" :value="__('Street Address')" />
                <x-text-input wire:model="address_street" id="address_street" class="block mt-1 w-full" type="text" />
            </div>
            <div>
                <x-input-label for="address_city" :value="__('City')" />
                <x-text-input wire:model="address_city" id="address_city" class="block mt-1 w-full" type="text" />
            </div>
            <div>
                <x-input-label for="address_state" :value="__('State/Province')" />
                <x-text-input wire:model="address_state" id="address_state" class="block mt-1 w-full" type="text" />
            </div>
            <div>
                <x-input-label for="address_country" :value="__('Country')" />
                <x-text-input wire:model="address_country" id="address_country" class="block mt-1 w-full" type="text" />
            </div>
            <div>
                <x-input-label for="address_postal_code" :value="__('Postal Code')" />
                <x-text-input wire:model="address_postal_code" id="address_postal_code" class="block mt-1 w-full" type="text" />
            </div>
        </div>

        <!-- Profile Picture -->
        <div>
            <x-input-label for="profile_picture" :value="__('Profile Picture')" />
            <input wire:model="profile_picture" id="profile_picture" class="block mt-1 w-full" type="file" accept="image/*">
            <x-input-error :messages="$errors->get('profile_picture')" class="mt-2" />
        </div>

        
        <div class="flex justify-end mt-6">
            <x-primary-button>
                {{ __('Update Profile') }}
            </x-primary-button>
        </div>
    </form>
</div>