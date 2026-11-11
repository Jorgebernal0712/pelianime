<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class PasswordResetController extends Controller
{
    /** Formulario "¿Olvidaste tu contraseña?" */
    public function showForgot()
    {
        return view('auth.forgot-password');
    }

    /** Envía el correo con el enlace (token) de restablecimiento. */
    public function sendLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('ok', 'Te enviamos un enlace para restablecer tu contraseña. Revisa tu correo.')
            : back()->withErrors(['email' => $this->mensaje($status)])->onlyInput('email');
    }

    /** Formulario para escribir la nueva contraseña (llega desde el correo). */
    public function showReset(Request $request, string $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)->letters()->numbers()],
        ], [], ['password' => 'contraseña']);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('ok', 'Tu contraseña fue restablecida. Ya puedes iniciar sesión.')
            : back()->withErrors(['email' => $this->mensaje($status)])->onlyInput('email');
    }

    private function mensaje(string $status): string
    {
        return match ($status) {
            Password::INVALID_USER => 'No encontramos un usuario con ese correo.',
            Password::INVALID_TOKEN => 'El enlace de restablecimiento no es válido o ya venció.',
            Password::RESET_THROTTLED => 'Espera un momento antes de volver a intentarlo.',
            default => 'No fue posible completar la solicitud.',
        };
    }
}
