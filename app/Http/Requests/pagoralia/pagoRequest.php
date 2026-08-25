<?php

namespace App\Http\Requests\pagoralia;

use Illuminate\Foundation\Http\FormRequest;

class pagoRequest extends FormRequest
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
            'isUnique' => ['required', 'boolean'],
            'invoice'  => ['required', 'string'],
            'cliente'  => ['required', 'string'],
            'nombre'   => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'monto'    => ['required', 'numeric', 'min:1'],
            'moneda'   => ['required', 'string', 'size:3'],
        ];
    }
}
