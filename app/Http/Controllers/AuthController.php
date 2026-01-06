<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * @group Autenticación
 *
 * APIs para registro y login de usuarios
 */
class AuthController extends Controller
{
    /**
     * Registrar nuevo usuario
     *
     * Crea una nueva cuenta de usuario y genera token de acceso personal.
     *
     * @bodyParam name string required Nombre completo del usuario. Max: 255 caracteres. Example: Juan Pérez
     * @bodyParam email string required Email único del usuario. Debe ser formato válido. Example: juan@example.com
     * @bodyParam password string required Contraseña. Mínimo 8 caracteres. Example: miContraseña123
     *
     * @response 201 {
     *   "user": {
     *     "id": 1,
     *     "name": "Juan Pérez",
     *     "email": "juan@example.com",
     *     "email_verified_at": null,
     *     "created_at": "2026-01-06T17:00:00.000000Z"
     *   },
     *   "access_token": "1|abc123def456ghi789...",
     *   "token_type": "Bearer"
     * }
     *
     * @responseField user App\Models\User El usuario creado.
     * @responseField access_token string Token de acceso para autenticar requests futuras.
     * @responseField token_type string Siempre "Bearer".
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        // genera token personal
        $token = $user->createToken('api-token')->accessToken;

        return response()->json([
            'user'          => $user,
            'access_token'  => $token,
            'token_type'    => 'Bearer',
        ], 201);
    }

    /**
     * Iniciar sesión
     *
     * Autentica usuario y genera nuevo token de acceso.
     *
     * @bodyParam email string required Email del usuario. Example: juan@example.com
     * @bodyParam password string required Contraseña del usuario. Example: miContraseña123
     *
     * @response 200 {
     *   "user": {
     *     "id": 1,
     *     "name": "Juan Pérez",
     *     "email": "juan@example.com"
     *   },
     *   "access_token": "2|xyz789uvw456rst123...",
     *   "token_type": "Bearer"
     * }
     *
     * @response 401 {
     *   "message": "Credenciales inválidas"
     * }
     *
     * @responseField user App\Models\User Usuario autenticado.
     * @responseField access_token string Nuevo token de acceso.
     * @responseField token_type string Siempre "Bearer".
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Credenciales inválidas',
            ], 401);
        }

        /** @var \App\Models\User $user */
        $user  = Auth::user();
        $token = $user->createToken('api-token')->accessToken;

        return response()->json([
            'user'          => $user,
            'access_token'  => $token,
            'token_type'    => 'Bearer',
        ]);
    }

    /**
     * @group Token Management (Opcional)
     *
     * Revocar token actual
     *
     * @authenticated
     *
     * @response 200 {
     *   "message": "Token revocado exitosamente"
     * }
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Token revocado exitosamente'
        ]);
    }
}
