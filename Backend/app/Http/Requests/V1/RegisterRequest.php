<?php

namespace App\Http\Requests\V1;

use App\Http\Requests\BaseRequest;

/**
 * @OA\Schema(
 *   title="Register Request",
 *   description="Dados para registrar um novo usuário",
 *   required={"name", "email", "password", "password_confirmation"},
 *   @OA\Property(property="name", type="string", description="Nome completo do usuário", example="João Silva"),
 *   @OA\Property(property="email", type="string", format="email", description="E-mail do usuário", example="joao@example.com"),
 *   @OA\Property(property="password", type="string", format="password", description="Senha do usuário", minLength=6, example="password123"),
 *   @OA\Property(property="password_confirmation", type="string", format="password", description="Confirmação da senha", minLength=6, example="password123")
 * )
 */
class RegisterRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'min:3', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório',
            'name.string' => 'O nome deve ser um texto',
            'name.min' => 'O nome deve ter no mínimo 3 caracteres',
            'name.max' => 'O nome deve ter no máximo 255 caracteres',
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
