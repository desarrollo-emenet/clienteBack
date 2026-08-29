<?php

namespace App\Http\Controllers\User;

use App\Models\Service;
use App\Service\User\UserService;
use Illuminate\Http\Request;
use App\Models\User;
use App\Service\servicios\validarService;
use App\Http\Controllers\Controller;
use App\Service\User\metadataService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\users\storeRequest;
use App\Http\Requests\users\updateRequest;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Support\Facades\Auth;


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

    public static $rulesUpdate = [
        'email' => 'nullable|email|max:50|unique:users,email,',
        'old_password' => 'required|string',
        'password'  => 'nullable|string|min:8',
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

    //Crear cuenta
    public function store(storeRequest $request)
    {


        /*$correoExistente = $this->validarService->validarCorreoDisponible($email);
        if ($correoExistente instanceof \Illuminate\Http\JsonResponse) {
            Log::error('Error al validar correo: ' . $correoExistente->getContent());
            return $correoExistente;
        }*/

        //log::info('correoExistente', ['data' => $correoExistente]);

        // Extraer datos validados

        try {
            DB::beginTransaction();
            return $this->userService->existeCliente($request->numero_cliente);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Error al crear la cuenta',
                'error'   => $e->getMessage(),
            ]);
        }
    }

    //obtener datos de un cliente por su numero de cliente
    public function clientePorNumero(Request $request, String $numero)
    {

        try {
            // Verificar que el numero_cliente pertenece al usuario
            $user = Auth::user();
            $servicio = Service::where('numero_cliente', $numero)
                ->where('user_id', $user->id)
                ->first();

            if (!$servicio) {
                return response()->json(['message' => 'Servicio no encontrado o no pertenece al usuario'], 404);
            }

            //$datosCliente = $this->validarService->validarClienteAPI($numero);
            $datosCliente = $this->metadataService->getMetadataForCliente($numero, $user);

            if ($datosCliente instanceof \Illuminate\Http\JsonResponse) {
                Log::error('Error al obtener datos del cliente: ' . $datosCliente->getContent());
                return $datosCliente; // Retornar error si hubo problema al obtener datos
            }

            // devolver info local y externa
            return response()->json([
                //'servicio' => $servicio,
                'cliente' => $datosCliente["cliente"],
                'servicios' => $datosCliente["servicios"],
                //'numero_cliente' => $numero,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error al obtener el cliente', [
                'numero_cliente' => $numero,
                'user_id' => $user->id,
                //'error' => $e->getMessage(),
            ]);
            return response()->json([
                'message' => 'Error al obtener el cliente',
                //'error' => $e->getMessage()
            ], 500);
        }
    }

    //actualizar contraseña
    public function update(updateRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $user = User::findOrFail($id);
            return $this->userService->update($user, $request);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Error al actualizar la informacion',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
