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

class AuthController extends Controller
{
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
        \Log::info('=== LOGIN ATTEMPT START ===');
        \Log::info('Email: ' . $request->email);
        
        try {
            // Authenticate
            \Log::info('Attempting authentication...');
            $request->authenticate();
            \Log::info('✓ Authentication successful');
            
            // Regenerate session
            \Log::info('Regenerating session...');
            $request->session()->regenerate();
            \Log::info('✓ Session regenerated');
            
            // Get authenticated user
            $user = Auth::user();
            \Log::info('✓ User retrieved: ' . $user->id . ' (' . $user->email . ')');
            \Log::info('User role: ' . $user->role);
            \Log::info('User roles from Spatie: ' . json_encode($user->getRoleNames()));
            
            // Check account active status
            \Log::info('Checking account active status...');
            \Log::info('is_active: ' . ($user->is_active ? 'true' : 'false'));
            if (!$user->is_active) {
                \Log::warning('⚠ Account not active. Logging out.');
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account is not active. Please contact support.',
                ]);
            }
            \Log::info('✓ Account is active');
            
            // Update last login
            $user->update(['last_login_at' => now()]);
            
            // Get dashboard route
            \Log::info('Getting dashboard route name...');
            $dashboardRoute = $user->getDashboardRouteName();
            \Log::info('Dashboard route: ' . $dashboardRoute);
            
            // Check email verification - log warning but allow login
            \Log::info('Checking email verification...');
            \Log::info('Email verified at: ' . ($user->email_verified_at ?? 'NULL'));
            if (!$user->hasVerifiedEmail()) {
                \Log::warning('⚠ Email not verified. User allowed to continue.');
                // Flash a warning message to show in dashboard
                session()->flash('email_not_verified', true);
                session()->flash('verification_email', $user->email);
            } else {
                \Log::info('✓ Email verified');
            }
            
            // Redirect to dashboard
            \Log::info('✓ Redirecting to: ' . $dashboardRoute);
            \Log::info('=== LOGIN ATTEMPT SUCCESS ===');
            
            return redirect()->intended(route($dashboardRoute));
            
        } catch (\Exception $e) {
            \Log::error('❌ LOGIN ERROR: ' . $e->getMessage());
            \Log::error('Exception type: ' . get_class($e));
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            \Log::info('=== LOGIN ATTEMPT FAILED ===');
            
            return back()->withErrors([
                'email' => 'An error occurred during login. Please try again.',
            ]);
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
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'role' => $this->getRoleValidationRules(),
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
            'role' => $request->role,
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
            // If referral registration fails, create user normally
            $user = User::create($userData);
        }
    
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $user->profile_picture = $path;
            $user->save();
        }
    
        // Send verification email
        $user->notify(new CustomVerifyEmail());
    
        // Log the user in immediately
        Auth::login($user);
    
        // Redirect to their dashboard with email verification warning
        return redirect()->route($user->getDashboardRouteName())->with([
            'email_not_verified' => true,
            'verification_email' => $user->email,
            'message' => 'Welcome! Please verify your email address to unlock all features.'
        ]);
    }

    /**
     * Get validation rules for user roles
     */
    protected function getRoleValidationRules(): array
    {
        return [
            'required',
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