<?php

namespace App\Http\Controllers;

use App\Service\clientService;

use App\Models\Service;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

//use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public static $rules = [
        'numero_cliente'   => 'required|string|max:6|unique:services,numero_cliente',
        'password'  => 'required|string|min:8',
    ];

    public static $rulesUpdate = [
        'password'  => 'sometimes|string|min:8',

    ];

    public function index()
    {
        //return User::all();
        return response()->json([
            'message' => 'Lista de usuarios',
            'data' => User::all(),
            'servicios' => Service::all()
        ], 200);
    }


    public function store(Request $request)
    {
        // Validar el cliente con el servicio
        $validacion = clientService::validarClienteCompleto($request->numero_cliente);

        // Si la validación devuelve un error, retornar esa respuesta
        if ($validacion instanceof \Illuminate\Http\JsonResponse) {
            return $validacion;
        }

        $clienteData = $validacion['clienteData']; // Extraer los datos del cliente validado
        $email = $validacion['email']; // Extraer el email del cliente validado

        $data = $request->validate(self::$rules); // Validar los datos de entrada 
        

         // Verificar si el email ya existe en la tabla users


        // Extraer datos validados
        $numCliente = $data['numero_cliente'];

        try {
            $data['password'] = Hash::make($data['password']);

            // Transacción completa
            $user = DB::transaction(function () use ($clienteData, $data, $numCliente, $email) {

                //guardar en tabla users email y password
                $user = User::create([
                    'email'    => $email,
                    'password' => $data['password'],
                ]);
                //guardar en tabla services numero de cliente
                Service::create([
                    'numero_cliente' => $numCliente,
                    'user_id' => $user->id,
                ]);

                //Evento de registro 
                event(new Registered($user));

                return $user;
            });

            return response()->json([
                'mensaje' => 'Registro creado correctamente',
                'user'    => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear la cuenta',
                'error'   => $e->getMessage(),
            ]);
        }
    }


    public function clientePorNumero(Request $request, $numero)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        try {
            // Verificar que el numero_cliente pertenece al usuario
            $servicio = Service::where('numero_cliente', $numero)
                ->where('user_id', $user->id)
                ->first();

            if (!$servicio) {
                return response()->json(['message' => 'Servicio no encontrado o no pertenece al usuario'], 404);
            }

            $datosCliente = clientService::obtenerDatosCliente($numero);

            if ($datosCliente instanceof \Illuminate\Http\JsonResponse) {
                return $datosCliente; // Retornar error si hubo problema al obtener datos
            }

            $clienteData = $datosCliente['clienteData'];

            // devolver info local y externa
            return response()->json([
                'servicio' => $servicio,
                'cliente' => $clienteData,
                'numero_cliente' => $numero,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener el cliente',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        try {
            // Validar datos
            $validated = $request->validate(array_merge(self::$rulesUpdate, [
                //'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            ]));

            // Si se proporciona una nueva contraseña, encriptarla
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else { //sino eliminar el campo password para no actualizarlo a null
                unset($validated['password']);
            }

            // Actualizar el usuario dentro de una transacción
            DB::transaction(fn() => $user->update($validated));

            // Devolver el usuario actualizado
            return response()->json([
                'mensaje' => 'Registro Actualizado',
                'data'    => $user->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al encontrar el usuario',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function destroy()
    {
        //
    }
}
