<?php

namespace App\Http\Requests\V1;

use App\Http\Requests\BaseRequest;

/**
 * @OA\Schema(
 *   title="Login Request",
 *   description="Credenciais para autenticação do usuário",
 *   required={"email", "password"},
 *   @OA\Property(property="email", type="string", format="email", description="E-mail do usuário", example="user@example.com"),
 *   @OA\Property(property="password", type="string", description="Senha do usuário", format="password", example="password123")
 * )
 */
class LoginRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'min:3', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'O email é obrigatório',
            'email.string' => 'O email deve ser um texto',
            'email.min' => 'O email deve ter no mínimo 3 caracteres',
            'email.max' => 'O email deve ter no máximo 255 caracteres',
            'password.required' => 'A senha é obrigatória',
            'password.string' => 'A senha deve ser um texto',
            'password.min' => 'A senha deve ter no mínimo 6 caracteres',
        ];
    }
}
