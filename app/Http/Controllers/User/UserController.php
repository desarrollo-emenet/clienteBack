<?php

namespace App\Http\Controllers\User;



use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\User;
use App\Service\metadataService;
use App\Service\servicios\validarService;
use App\Service\UserService;
use App\Http\Controllers\Controller;


use Illuminate\Support\Facades\Hash;


//use Illuminate\Support\Facades\Log;

class UserController extends Controller
{

    protected $validarService;
    protected $userService;
    protected $metadataService;

    public function __construct(validarService $validarService, UserService $UserService, metadataService $metadataService)
    {
        $this->validarService = $validarService;
        $this->userService = $UserService;
        $this->metadataService = $metadataService;
    }
    public static $rules = [
        'numero_cliente'   => 'required|string|max:6',
        //'password'  => 'required|string|min:8',
    ];

    public static $rulesUpdate = [
        'old_password' => 'required|string',
        'password'  => 'required|string|min:8',
        

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
        $validacion = $this->validarService->validarClienteCompleto($request->numero_cliente);

        // Si la validación devuelve un error, retornar esa respuesta
        if ($validacion instanceof \Illuminate\Http\JsonResponse) {
            return $validacion;
        }

        $email = $validacion['email']; // Extraer el email del cliente validado

        $data = $request->validate(self::$rules); // Validar los datos de entrada 

        // Extraer datos validados
        $numCliente = $data['numero_cliente'];

        try {
            return $this->userService->existeCliente($numCliente, $email);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear la cuenta',
                'error'   => $e->getMessage(),
            ]);
        }
    }

    public function clientePorNumero(Request $request, String $numero)
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

            //$datosCliente = $this->validarService->validarClienteAPI($numero);   ------------------------------------
            $datosCliente = $this->metadataService->getMetadataForCliente($numero, $user);


            if ($datosCliente instanceof \Illuminate\Http\JsonResponse) {
                return $datosCliente; // Retornar error si hubo problema al obtener datos
            }

            $clienteData = $datosCliente;

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
            $validated = $request->validate((self::$rulesUpdate));

            // Verificar la contraseña actual antes de permitir la actualización
            $oldPassword = $validated['old_password'];
            if (!Hash::check($oldPassword, $user->password)) {
                return response()->json([
                    'message' => 'Contraseña actual incorrecta',
                ], 403);
            }

            //encriptar nueva contraseña temporal y actualizar el usuario
            $user->password = Hash::make($validated['password']);
            $user->save();

            // Devolver el usuario actualizado
            return response()->json([
                'mensaje' => 'Registro Actualizado',
                'data'    => $user->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la contraseña',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function destroy()
    {
        //
    }
}
