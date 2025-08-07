<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Validator;
use App\Notifications\SocialLoginDataCollectionNotification;

class SocialAuthController extends Controller
{

    // Google Login

    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->scopes([
                'profile',
                'email',
                'https://www.googleapis.com/auth/user.birthday.read',
                'https://www.googleapis.com/auth/user.addresses.read'
            ])
            ->with(['access_type' => 'offline', 'prompt' => 'consent select_account'])
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            $user = $this->createOrUpdateUserFromGoogle($googleUser);

            // Notify user about data collection
            $user->notify(new SocialLoginDataCollectionNotification('google'));

            Auth::login($user, true);

            return redirect()->intended($user->getDashboardRouteName());

        } catch (\Exception $e) {
            \Log::error('Google Auth Error: ' . $e->getMessage());
            return redirect()->route('login')->withErrors([
                'google' => 'We couldn\'t complete your login with Google. Please try another method or register.'
            ]);
        }
    }

    protected function createOrUpdateUserFromGoogle($googleUser)
    {
        $peopleData = $this->getGooglePeopleData($googleUser->token);

        $userData = [
            'name' => $this->validateName($googleUser->getName()),
            'email' => $this->validateEmail($googleUser->getEmail()),
            'provider_id' => $googleUser->getId(),
            'provider' => 'google',
            'password' => bcrypt(Str::random(32)),
            'email_verified_at' => now(),
            'profile_picture' => $this->handleProfilePicture($googleUser->getAvatar()),
            'date_of_birth' => $this->parseAndValidateBirthday($peopleData),
            'address_street' => $this->parseAndValidateAddress($peopleData),
            'phone_number' => $this->parseAndValidatePhone($peopleData),
            'bio' => $this->sanitizeBio($googleUser->user['bio'] ?? null),
            'role' => User::ROLE_STUDENT,
        ];

        // Validate all data before saving
        $validator = Validator::make($userData, [
            'email' => 'required|email|max:255',
            'name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date|before:-13 years',
            'phone_number' => 'nullable|string|max:20',
            'address_street' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            \Log::warning('Google data validation failed', ['errors' => $validator->errors()]);
            // Fallback to minimal data if validation fails
            $userData = array_intersect_key($userData, array_flip(['name', 'email', 'provider_id', 'provider', 'password', 'email_verified_at']));
        }

        return User::updateOrCreate(
            ['email' => $userData['email']],
            $userData
        );
    }

    protected function getGooglePeopleData($accessToken)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->get('https://people.googleapis.com/v1/people/me?personFields=addresses,birthdays,phoneNumbers,biographies', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json',
                ],
                'timeout' => 10
            ]);

            return json_decode($response->getBody(), true);

        } catch (\Exception $e) {
            \Log::error('Google People API Error: ' . $e->getMessage());
            return []; // Return empty array if API fails
        }
    }

    // Field-specific validation methods
    protected function validateName($name)
    {
        return Str::limit(strip_tags($name ?? 'User'), 255);
    }

    protected function validateEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Invalid email address from Google");
        }
        return $email;
    }

    protected function parseAndValidateBirthday($peopleData)
    {
        try {
            if (!empty($peopleData['birthdays'])) {
                foreach ($peopleData['birthdays'] as $birthday) {
                    if (isset($birthday['date'])) {
                        $date = $birthday['date'];
                        $birthDate = Carbon::create(
                            $date['year'] ?? 1900,
                            $date['month'],
                            $date['day']
                        );

                        // Validate age is at least 13
                        if ($birthDate->age < 13) {
                            return null;
                        }

                        return $birthDate->toDateString();
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to parse Google birthday', ['error' => $e->getMessage()]);
        }

        return null;
    }

    protected function parseAndValidateAddress($peopleData)
    {
        try {
            if (!empty($peopleData['addresses'])) {
                $primaryAddress = collect($peopleData['addresses'])
                    ->firstWhere('metadata.primary', true);

                return $primaryAddress ? Str::limit($primaryAddress['formattedValue'], 255) : null;
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to parse Google address', ['error' => $e->getMessage()]);
        }

        return null;
    }

    protected function parseAndValidatePhone($peopleData)
    {
        try {
            if (!empty($peopleData['phoneNumbers'])) {
                $primaryPhone = collect($peopleData['phoneNumbers'])
                    ->firstWhere('metadata.primary', true);

                return $primaryPhone ? substr(preg_replace('/[^0-9]/', '', $primaryPhone['value']), 0, 20) : null;
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to parse Google phone', ['error' => $e->getMessage()]);
        }

        return null;
    }

    protected function sanitizeBio($bio)
    {
        return $bio ? Str::limit(strip_tags($bio), 500) : null;
    }

    protected function handleProfilePicture($avatarUrl)
    {
        if (empty($avatarUrl))
            return null;

        try {
            $contents = file_get_contents($avatarUrl);
            if ($contents === false)
                return null;

            $filename = 'profile-pictures/' . Str::uuid() . '.jpg';
            Storage::disk('public')->put($filename, $contents);
            return $filename;
        } catch (\Exception $e) {
            \Log::warning('Failed to save Google profile picture', ['error' => $e->getMessage()]);
            return null;
        }
    }

    protected function parseGoogleBirthday($peopleData)
    {
        if (!empty($peopleData['birthdays'])) {
            foreach ($peopleData['birthdays'] as $birthday) {
                if (isset($birthday['date'])) {
                    $date = $birthday['date'];
                    return Carbon::create(
                        $date['year'] ?? 1900,
                        $date['month'],
                        $date['day']
                    )->toDateString();
                }
            }
        }
        return null;
    }

    protected function parseGoogleAddress($peopleData)
    {
        if (!empty($peopleData['addresses'])) {
            $primaryAddress = collect($peopleData['addresses'])
                ->firstWhere('metadata.primary', true);

            return $primaryAddress ? $primaryAddress['formattedValue'] : null;
        }
        return null;
    }

    protected function saveGoogleProfilePicture($avatarUrl)
    {
        if (!$avatarUrl)
            return null;

        try {
            $contents = file_get_contents($avatarUrl);
            $filename = 'profile-pictures/' . Str::uuid() . '.jpg';
            Storage::disk('public')->put($filename, $contents);
            return $filename;
        } catch (\Exception $e) {
            \Log::error('Failed to save Google profile picture: ' . $e->getMessage());
            return null;
        }
    }

    // Facebook Login
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')
            ->scopes(['email', 'public_profile', 'user_birthday', 'user_location'])
            ->with(['auth_type' => 'rerequest']) // Forces re-request of declined permissions
            ->redirect();
    }
    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->fields([
                'name',
                'email',
                'birthday',
                'location',
                'hometown',
                'picture.type(large)',
                'gender'
            ])->stateless()->user();

            $user = $this->createOrUpdateUserFromFacebook($facebookUser);

            $user->notify(new SocialLoginDataCollectionNotification('facebook'));

            Auth::login($user, true);

            return redirect()->intended($user->getDashboardRouteName());

        } catch (\Exception $e) {
            \Log::error('Facebook Auth Error: ' . $e->getMessage());
            return redirect()->route('login')->withErrors([
                'facebook' => 'We couldn\'t complete your login with Facebook. Please try another method.'
            ]);
        }
    }

    protected function createOrUpdateUserFromFacebook($facebookUser)
    {
        $userData = [
            'name' => $this->validateName($facebookUser->getName()),
            'email' => $this->validateEmail($facebookUser->getEmail()),
            'provider_id' => $facebookUser->getId(),
            'provider' => 'facebook',
            'password' => bcrypt(Str::random(32)),
            'email_verified_at' => now(),
            'profile_picture' => $this->handleFacebookProfilePicture($facebookUser),
            'date_of_birth' => $this->parseFacebookBirthday($facebookUser),
            'address_street' => $this->parseFacebookAddress($facebookUser),
            'gender' => $this->parseFacebookGender($facebookUser),
            'bio' => $this->sanitizeBio($facebookUser->user['bio'] ?? null),
        ];

        $validator = Validator::make($userData, [
            'email' => 'required|email|max:255',
            'name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date|before:-13 years',
            'gender' => 'nullable|string|in:male,female,other',
            'bio' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            \Log::warning('Facebook data validation failed', ['errors' => $validator->errors()]);
            $userData = array_intersect_key($userData, array_flip(['name', 'email', 'provider_id', 'provider', 'password', 'email_verified_at']));
        }

        return User::updateOrCreate(
            ['email' => $userData['email']],
            $userData
        );
    }



    protected function parseFacebookBirthday($facebookUser)
    {
        try {
            if (!empty($facebookUser->user['birthday'])) {
                $birthDate = Carbon::createFromFormat('m/d/Y', $facebookUser->user['birthday']);

                if ($birthDate->age < 13) {
                    return null;
                }

                return $birthDate->toDateString();
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to parse Facebook birthday', ['error' => $e->getMessage()]);
        }

        return null;
    }

    protected function parseFacebookAddress($facebookUser)
    {
        try {
            if (!empty($facebookUser->user['location']['name'])) {
                return Str::limit($facebookUser->user['location']['name'], 255);
            }

            if (!empty($facebookUser->user['hometown']['name'])) {
                return Str::limit($facebookUser->user['hometown']['name'], 255);
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to parse Facebook address', ['error' => $e->getMessage()]);
        }

        return null;
    }

    protected function parseFacebookGender($facebookUser)
    {
        try {
            if (!empty($facebookUser->user['gender'])) {
                return strtolower($facebookUser->user['gender']) === 'male' ? 'male' :
                    (strtolower($facebookUser->user['gender']) === 'female' ? 'female' : 'other');
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to parse Facebook gender', ['error' => $e->getMessage()]);
        }

        return null;
    }

    protected function handleFacebookProfilePicture($facebookUser)
    {
        try {
            if (!empty($facebookUser->getAvatar())) {
                $contents = file_get_contents($facebookUser->getAvatar());
                if ($contents === false)
                    return null;

                $filename = 'profile-pictures/' . Str::uuid() . '.jpg';
                Storage::disk('public')->put($filename, $contents);
                return $filename;
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to save Facebook profile picture', ['error' => $e->getMessage()]);
        }

        return null;
    }


    // Twitter Login
    public function redirectToTwitter()
    {
        return Socialite::driver('twitter')->redirect();
    }

    public function handleTwitterCallback()
    {
        return $this->handleSocialCallback('twitter');
    }

    // Common Callback Handler
    protected function handleSocialCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();

            // Find or create user
            $user = User::firstOrCreate(
                ['email' => $socialUser->getEmail()],
                [
                    'name' => $socialUser->getName(),
                    'provider_id' => $socialUser->getId(),
                    'provider' => $provider,
                    'password' => bcrypt(uniqid()), // Random password
                    'email_verified_at' => now(), // Mark as verified
                ]
            );

            Auth::login($user);

            return redirect()->intended($user->getDashboardRouteName());

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'social' => 'Failed to authenticate with ' . ucfirst($provider)
            ]);
        }
    }
}