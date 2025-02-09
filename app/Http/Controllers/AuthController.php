<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationTokenMail;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthController extends Controller
{

    public function register(Request $request)
    {
        // Validar los datos del formulario

        $request->merge([
            'name' => trim($request->name),
            'email' => trim($request->email),
            'password' => trim($request->password),
        ]);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string'
        ]);

        // Generar token de verificación
        $verificationToken = Str::random(20);

        // Crear el usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'verification_token' => $verificationToken,
        ]);

        // Verificar que el usuario se haya guardado
        if (!$user) {
            return response()->json(['message' => 'Error al registrar el usuario'], 500);
        }

        // Enviar email con el token de verificación
        Mail::to($user->email)->send(new VerificationTokenMail($verificationToken));

        // Generar token JWT para autenticación inmediata
        $jwtToken = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Registro exitoso. Se ha enviado un correo con tu token de verificación.',
            'verification_token' => $verificationToken,
            'jwt_token' => $jwtToken
        ], 201);
    }
    // Verificación del token recibido por correo
    public function verifyEmail($token)
    {
        $user = User::where('verification_token', $token)->first();

        if (!$user) {
            return response()->json(['message' => 'Token inválido'], 400);
        }

        // Confirmar email
        if ($user->email_verified_at) {
            return response()->json(['message' => 'El correo ya fue verificado.'], 400);
        }
        
        $user->email_verified_at = now();
        $user->save();
        return response()->json(['message' => 'Correo verificado con éxito. Ya puedes iniciar sesión.']);
    }


    // Mostrar formulario de inicio de sesión
    public function showLogin()
    {
        return view('auth.login');
    }
    // Inicio de sesión (solo si el correo está verificado)

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }
        $user = JWTAuth::user();


        if (!$user->email_verified_at) {
            return response()->json(['message' => 'Debes verificar tu correo antes de iniciar sesión.'], 403);
        }

            return redirect()->route('dashboard'); // Redirige si el login es correcto
        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'token' => $token,
        ]);
    }

    // Cerrar sesión
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Sesión cerrada exitosamente']);
    }
}
