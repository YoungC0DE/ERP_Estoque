<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\LoginRequest;
use App\Http\Requests\V1\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Realizar login com email e senha
     *
     * Autentica um usuário com suas credenciais (email e senha) e retorna um token de acesso
     * para ser utilizado nas requisições autenticadas.
     *
     * @tags Auth
     * @unauthenticated
     * @response 200 {
     *  "token": "1|abcd1234efgh5678ijkl9012mnop3456qrst7890uvwx",
     *  "user": {
     *    "id": 1,
     *    "name": "João Silva",
     *    "email": "joao@example.com",
     *    "email_verified_at": null,
     *    "created_at": "2026-03-29T12:00:00Z",
     *    "updated_at": "2026-03-29T12:00:00Z"
     *  }
     * }
     * @response 401 {
     *  "message": "Credenciais inválidas."
     * }
     * @response 422 {
     *  "message": "The email field is required. (and 1 more error)",
     *  "errors": {
     *    "email": ["The email field is required."],
     *    "password": ["The password field is required."]
     *  }
     * }
     */
    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->jsonResponse(['message' => 'Credenciais inválidas.'], Response::HTTP_UNAUTHORIZED);
        }

        $user  = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        return $this->jsonResponse([
            'token' => $token,
            'user'  => $user,
        ]);
    }

    /**
     * Registrar novo usuário
     *
     * Cria uma nova conta de usuário com nome, email e senha. O email deve ser único no sistema.
     *
     * @tags Auth
     * @unauthenticated
     * @response 200 {
     *  "message": "User registered successfully"
     * }
     * @response 409 {
     *  "message": "Já existe um usuário com esse email."
     * }
     * @response 422 {
     *  "message": "The name field is required. (and 3 more errors)",
     *  "errors": {
     *    "name": ["The name field is required."],
     *    "email": ["The email field is required."],
     *    "password": ["The password field is required."],
     *    "password_confirmation": ["The password confirmation field is required."]
     *  }
     * }
     */
    public function register(RegisterRequest $request)
    {
        if (User::where('email', $request->email)->exists()) {
            return $this->jsonResponse(['message' => 'Já existe um usuário com esse email.'], Response::HTTP_CONFLICT);
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return $this->jsonResponse(['message' => 'User registered successfully']);
    }

    /**
     * Fazer logout do usuário
     *
     * Revoga o token de acesso atual do usuário, efetivamente encerrando a sessão.
     * Requer autenticação com token Bearer.
     *
     * @tags Auth
     * @response 200 {
     *  "message": "Logout realizado com sucesso."
     * }
     * @response 401 {
     *  "message": "Unauthenticated."
     * }
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->jsonResponse(['message' => 'Logout realizado com sucesso.']);
    }

    /**
     * Obter informações do usuário autenticado
     *
     * Retorna os dados do usuário atualmente autenticado.
     * Requer autenticação com token Bearer.
     *
     * @tags Auth
     * @response 200 {
     *  "id": 1,
     *  "name": "João Silva",
     *  "email": "joao@example.com",
     *  "email_verified_at": null,
     *  "created_at": "2026-03-29T12:00:00Z",
     *  "updated_at": "2026-03-29T12:00:00Z"
     * }
     * @response 401 {
     *  "message": "Unauthenticated."
     * }
     */
    public function userInfo()
    {
        return $this->jsonResponse(Auth::user());
    }
}