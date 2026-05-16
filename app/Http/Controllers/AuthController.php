<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class AuthController extends Controller
{
    private function homeRouteForRole(User $user): string
    {
        return match ($user->role) {
            'admin' => 'admin.panel',
            'staff' => 'staff.panel',
            default => 'home',
        };
    }

    /**
     * @return array<int, string>
     */
    private function supportedProviders(): array
    {
        return ['google'];
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        $user = User::where('email', $credentials['email'])->first();

        if (! $user) {
            return back()
                ->withErrors(['email' => 'Email hoặc mật khẩu không đúng.'])
                ->onlyInput('email');
        }

        $authenticated = false;

        try {
            $authenticated = Hash::check($credentials['password'], $user->password);
        } catch (RuntimeException) {
            $authenticated = hash_equals((string) $user->password, (string) $credentials['password']);

            if ($authenticated) {
                $user->forceFill([
                    'password' => Hash::make($credentials['password']),
                ])->save();
            }
        }

        if (! $authenticated) {
            return back()
                ->withErrors(['email' => 'Email hoặc mật khẩu không đúng.'])
                ->onlyInput('email');
        }

        Auth::login($user, $remember);

        $request->session()->regenerate();

        $user = $request->user();

        if ($user && $user->status !== 'active') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['email' => 'Tài khoản của bạn đang chờ duyệt hoặc đã bị khóa.'])
                ->onlyInput('email');
        }

        return redirect()->intended(route($this->homeRouteForRole($user)));
    }

    public function redirectToProvider(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, $this->supportedProviders(), true), 404);

        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback(Request $request, string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, $this->supportedProviders(), true), 404);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Không thể đăng nhập bằng tài khoản ' . ucfirst($provider) . ' lúc này.'])
                ->withInput();
        }

        $email = $socialUser->getEmail();

        if (! $email && $provider === 'facebook') {
            $email = sprintf('facebook_%s@social.catbook.local', $socialUser->getId());
        }

        if (! $email) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Tài khoản ' . ucfirst($provider) . ' chưa cung cấp email.'])
                ->withInput();
        }

        $user = User::query()
            ->where('email', $email)
            ->orWhere(function ($query) use ($provider, $socialUser) {
                $query->where('provider', $provider)
                    ->where('provider_id', $socialUser->getId());
            })
            ->first();

        if (! $user) {
            $user = User::create([
                'full_name' => $socialUser->getName() ?: Str::before($email, '@'),
                'email' => $email,
                'password' => Hash::make(Str::random(32)),
                'phone' => null,
                'avatar_url' => $socialUser->getAvatar(),
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'role' => 'customer',
                'status' => 'active',
            ]);
        } else {
            $user->forceFill([
                'provider' => $user->provider ?: $provider,
                'provider_id' => $user->provider_id ?: $socialUser->getId(),
                'avatar_url' => $user->avatar_url ?: $socialUser->getAvatar(),
            ])->save();
        }

        if ($user->status !== 'active') {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Tài khoản của bạn đang chờ duyệt hoặc đã bị khóa.'])
                ->withInput();
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended(route($this->homeRouteForRole($user)));
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => 'customer',
            'status' => 'active',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
