<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\EmployeeAuthService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct(private readonly EmployeeAuthService $authService) {}

    public function username(): string
    {
        return 'username';
    }

    protected function validateLogin(Request $request): void
    {
        $request->validate([
            $this->username() => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            $this->username().'.required' => 'Username tidak boleh kosong',
            'password.required' => 'Password tidak boleh kosong',
        ]);
    }

    protected function attemptLogin(Request $request): bool
    {
        $employee = $this->authService->authenticate(
            (string) $request->input($this->username()),
            (string) $request->input('password'),
        );

        if (! $employee) {
            return false;
        }

        $this->guard()->login($employee);

        return true;
    }

    protected function sendFailedLoginResponse(Request $request): never
    {
        throw ValidationException::withMessages([
            $this->username() => ['Username atau password salah'],
        ]);
    }
}
