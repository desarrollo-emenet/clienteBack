<?php

namespace App\Http\Requests\users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Override;

class updateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $id = $this->route('usuario');
        return [
            'email' => [
                'nullable',
                'email',
                'max:50',
                'unique:users,email,' . $id,
            ],
            'old_password' => 'required|string',
            'password' => 'nullable|string|min:8',
        ];
    }

    public function attributes()
    {
        return [
            'email' => "Correo",
            'old_password' => 'Contraseña actual',
            'password' => 'Nueva contraseña',
        ];
    }
}
