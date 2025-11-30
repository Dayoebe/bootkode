<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Core\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Notifications\CustomVerifyEmail;
use App\Services\AffiliateService;
use App\Traits\HasCloudinaryUpload;

class AuthController extends Controller
{
    use HasCloudinaryUpload; // Add the trait here
    
    protected $affiliateService;

    public function __construct(AffiliateService $affiliateService)
    {
        $this->affiliateService = $affiliateService;
    }

    // Login
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        try {
            // Authenticate
            $request->authenticate();
            
            // Regenerate session
            $request->session()->regenerate();
            
            // Get authenticated user
            $user = Auth::user();
            
            // Check account active status
            if (!$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return back()->withErrors([
                    'email' => 'Your account has been deactivated. Please contact support for assistance.',
                ])->withInput($request->only('email'));
            }
            
            // Update last login
            $user->update(['last_login_at' => now()]);
            
            // Get dashboard route
            $dashboardRoute = $user->getDashboardRouteName();
            
            // Check email verification - warn but allow login
            if (!$user->hasVerifiedEmail()) {
                session()->flash('email_not_verified', true);
                session()->flash('verification_email', $user->email);
                session()->flash('warning', 'Please verify your email address to unlock all features.');
            }
            
            try {
                activity()
                    ->causedBy($user)
                    ->event('login')
                    ->withProperties([
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'email_verified' => $user->hasVerifiedEmail(),
                    ])
                    ->log('User logged in');
            } catch (\Exception $e) {
                // Silently fail activity logging
            }
            
            // Redirect to dashboard
            return redirect()
                ->intended(route($dashboardRoute))
                ->with('success', 'Welcome back, ' . $user->name . '!');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
            
        } catch (\Exception $e) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return back()->withErrors([
                'email' => 'An error occurred during login. Please try again or contact support.',
            ])->withInput($request->only('email'));
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    // Registration
    public function showRegistrationForm(): View
    {
        $referralCode = request('ref');
        $validation = null;

        if ($referralCode) {
            $validation = $this->affiliateService->validateReferralCode($referralCode);
        }

        return view('auth.register', compact('referralCode', 'validation'));
    }

    public function register(Request $request): RedirectResponse
    {
        // UPDATED VALIDATION - Made fields nullable except name and email
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'role' => ['nullable', ...$this->getRoleValidationRules()], // Made nullable
            'date_of_birth' => ['nullable', 'date'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:500'],
            'address_street' => ['nullable', 'string', 'max:255'],
            'address_city' => ['nullable', 'string', 'max:255'],
            'address_state' => ['nullable', 'string', 'max:255'],
            'address_country' => ['nullable', 'string', 'max:255'],
            'address_postal_code' => ['nullable', 'string', 'max:20'],
            'skills' => ['nullable', 'string', 'max:255'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'education_level' => ['nullable', 'string', 'max:255'],
            'terms' => ['nullable', 'accepted'],
        ]);
    
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? User::ROLE_STUDENT, // Default to student if not provided
            'date_of_birth' => $request->date_of_birth,
            'phone_number' => $request->phone_number,
            'bio' => $request->bio,
            'address_street' => $request->address_street,
            'address_city' => $request->address_city,
            'address_state' => $request->address_state,
            'address_country' => $request->address_country,
            'address_postal_code' => $request->address_postal_code,
            'skills' => $request->skills,
            'occupation' => $request->occupation,
            'education_level' => $request->education_level,
            'is_active' => true,
        ];
    
        // Handle referral registration
        $referralCode = $request->input('referral_code');
    
        try {
            $user = $this->affiliateService->registerWithReferral($userData, $referralCode);
        } catch (\Exception $e) {
            $user = User::create($userData);
        }

        
    
        // Handle profile picture upload to Cloudinary
        if ($request->hasFile('profile_picture')) {
            try {
                $file = $request->file('profile_picture');
                
                // FIXED: Use the trait method instead of cloudinary() helper
                $uploadResult = $this->uploadProfilePicture($file, $user->id);
                
                if ($uploadResult && isset($uploadResult['secure_url'])) {
                    $user->profile_picture = $uploadResult['secure_url'];
                    $user->save();
                }



            } catch (\Exception $e) {
                \Log::error('Profile picture upload error during registration', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
                // Continue registration even if image upload fails
            }
        }
    
        // Send verification email
        $user->notify(new CustomVerifyEmail());
    
        Auth::login($user);
    
        // Redirect to their dashboard with email verification warning
        return redirect()->route($user->getDashboardRouteName())->with([
            'email_not_verified' => true,
            'verification_email' => $user->email,
            'message' => 'Welcome! Please verify your email address to unlock all features.'
        ]);
    }

    protected function getRoleValidationRules(): array
    {
        return [
            'string',
            'in:' . implode(',', [
                User::ROLE_STUDENT,
                User::ROLE_INSTRUCTOR,
                User::ROLE_MENTOR,
                User::ROLE_CONTENT_EDITOR,
                User::ROLE_AFFILIATE_AMBASSADOR,
                User::ROLE_ACADEMY_ADMIN,
                User::ROLE_SUPER_ADMIN,
            ])
        ];
    }

    // Password Confirmation
    public function showConfirmPasswordForm(): View
    {
        return view('auth.confirm-password');
    }

    public function confirmPassword(Request $request): RedirectResponse
    {
        if (
            !Auth::guard('web')->validate([
                'email' => $request->user()->email,
                'password' => $request->password,
            ])
        ) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());
        return redirect()->intended(route('dashboard', absolute: false));
    }
}