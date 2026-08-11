<?php

namespace App\Http\Requests\users;

use Illuminate\Foundation\Http\FormRequest;

class storeRequest extends FormRequest
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
        return [
            'numero_cliente'   => 'required|string',
        ];
    }

    public function attributes()
    {
        return [
            'numero_cliente' => 'Número de cliente',
        ];
    }
}
