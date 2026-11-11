<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credenciales = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [], ['password' => 'contraseña']);

        if (Auth::attempt($credenciales, $request->boolean('remember'))) {
            $request->session()->regenerate(); // evita fijación de sesión

            return redirect()->intended(route('dashboard'))
                ->with('ok', '¡Bienvenido de nuevo, '.Auth::user()->name.'!');
        }

        return back()
            ->withErrors(['email' => 'Las credenciales no coinciden con nuestros registros.'])
            ->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [], ['name' => 'nombre', 'password' => 'contraseña']);

        $user = User::create($datos); // el cast 'hashed' cifra la contraseña

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('ok', 'Cuenta creada correctamente. ¡Bienvenido!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('ok', 'Sesión cerrada correctamente.');
    }
}
