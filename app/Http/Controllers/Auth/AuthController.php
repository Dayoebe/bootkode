<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
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

class AuthController extends Controller
{
    // Login
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        
        $request->session()->regenerate();
        
        // Check if email is verified
        if (!$request->user()->hasVerifiedEmail()) {
            Auth::logout();
            return redirect()->route('verification.notice');
        }
        
        // Check if account is active
        if (!$request->user()->is_active) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Your account is not active. Please contact support.',
            ]);
        }
        
        // Redirect to the appropriate dashboard based on role
        return redirect()->intended(route($request->user()->getDashboardRouteName()));
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
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'role' => $this->getRoleValidationRules(),
            'date_of_birth' => ['required', 'date', 'before:-18 years'],
            'phone_number' => ['required', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:500'],
            'address_street' => ['required', 'string', 'max:255'],
            'address_city' => ['required', 'string', 'max:255'],
            'address_state' => ['required', 'string', 'max:255'],
            'address_country' => ['required', 'string', 'max:255'],
            'address_postal_code' => ['required', 'string', 'max:20'],
            'skills' => ['nullable', 'string', 'max:255'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'education_level' => ['nullable', 'string', 'max:255'],
            'terms' => ['required', 'accepted'],
        ]);
    
        $user = User::create([
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
        ]);
    
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $user->profile_picture = $path; // Remove 'storage/' prefix since it's already in public disk
            $user->save();
        }

        $user->notify(new CustomVerifyEmail());
        // $user->sendEmailVerificationNotification();
        
        return redirect()->route('verification.notice')->with([
            'message' => 'Please verify your email address. We sent you a verification link.',
            'email' => $user->email
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
        if (!Auth::guard('web')->validate([
            'email' => $request->user()->email,
            'password' => $request->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());
        return redirect()->intended(route('dashboard', absolute: false));
    }
}