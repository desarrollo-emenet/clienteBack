<?php

namespace App\Http\Controllers;

use App\Service\clientService;

use App\Models\Service;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use App\Models\User;
use App\Service\UserService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;


//use Illuminate\Support\Facades\Log;

class UserController extends Controller
{


    public static $rules = [
        'numero_cliente'   => 'required|string|max:10|unique:services,numero_cliente',
        'email'     => 'required|email|unique:users,email',
        'password'  => 'required|string|min:8',
    ];

    public static $rulesUpdate = [
        //'name'      => 'sometimes|string|max:255',
        //'last_name' => 'sometimes|string|max:255',
        //'phone'     => 'sometimes|string|max:10|regex:/^\d+$/',
        'email'     => 'sometimes|required|email|max:255',
        'password'  => 'sometimes|string|min:8',

    ];

    public function index()
    {
        //
        return User::all();
    }


    public function store(Request $request)
    {
        //validar datos
        $request->validate([
            'numero_cliente' => 'required|string',
        ]);

        $validacion = clientService::validarClienteCompleto($request->numero_cliente);

        if($validacion instanceof \Illuminate\Http\JsonResponse){
            return $validacion;
        }
        
        $data = $request->validate(self::$rules);


        // Extraer datos validados
        $numEncriptado = $data['numero_cliente'];
        $numeroCliente = $validacion['numero'];
        $clienteData = $validacion['clienteData'];

        try {
            $data['password'] = Hash::make($data['password']);

            // Transacción completa
            $user = DB::transaction(function () use ($data, $numEncriptado) {
                //guardar en tabla users email y password
                $user = User::create([
                    'email'    => $data['email'],
                    'password' => $data['password'],
                ]);
                //guardar en tabla services numero de cliente
                Service::create([
                    'numero_cliente' => $numEncriptado,
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
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al crear la cuenta',
                'error'   => $e->getMessage(),
            ], 422);
        }
    }


    public function clientePorNumero(Request $request, $numero)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        // Verificar que el numero_cliente pertenece al usuario
        $servicio = Service::where('numero_cliente', $numero)
            ->where('user_id', $user->id)
            ->first();

        if (!$servicio) {
            // 404 si no existe
            return response()->json(['message' => 'Servicio no encontrado o no pertenece al usuario'], 404);
        }

        $datosCliente = clientService::obtenerDatosCliente($numero, true);
        
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
    }



    public function getClienteData($id)
    {
        //
        $user = User::findOrFail($id);


        $servicios = [
            'estadoCuenta' => [
                ['VENTA' => '240042', 'fechaEmision' => '06-08-2025', 'importe' => '500.0', 'mensualidad' => 'AGO 2025'],
                ['VENTA' => '246117', 'fechaEmision' => '05-09-2025', 'importe' => '600.0', 'mensualidad' => 'SEP 2025'],
                ['VENTA' => '243517', 'fechaEmision' => '05-10-2025', 'importe' => '600.0', 'mensualidad' => 'OCT 2025'],
                ['VENTA' => '248513', 'fechaEmision' => '05-11-2025', 'importe' => '600.0', 'mensualidad' => 'NOV 2025'],
            ],
            'internet' => ['precio' => 400],
            'camaras'  => ['canServicios' => 1, 'precio' => 50],
            'telefonia'  => ['lineas' => 1, 'precio' => 150],

        ];

        $fecha = Carbon::now();
        $resultadoDeuda = UserService::calcularAdeudo($servicios, $fecha);

        Log::info(' recibidos:', $resultadoDeuda);


        return [
            'status' => 'success',
            'cliente' => [
                'cliente'         => '01804',
                //'nombre'          => strtoupper($user->name).' '.strtoupper($user->last_name ?? ''),
                'nombre'          => 'Marcos Cid Flores',
                'direccion'       => 'JOSE VALENTIN DAVILA #401',
                'pais'            => 'Mexico',
                'estado'          => 'MEXICO',
                'municipio'       => 'JOCOTITLAN',
                'colonia'         => 'JOCOTITLAN',
                'correo'          => 'mcid653@gmail',
                'telefono'        => '7121748293',
                'coordenadas'     => '19.569008, -99.756175',
                'planInternet'    => 'PLAN200',
                'nombrePlan'      => 'Plan 200 Megas Fibra',
                'clasificacion'   => 'IFO',
                'desClasificacion' => 'FIBRA ÓPTICA',
                'router'          => '1',
                'infoRed' => [
                    'router'      => 'IXTLAHUACA',
                    'address'     => '172.16.31.123',
                    'estado'      => 'Activo',
                    'estadoFibra' => 'bound'
                ],
                'deuda' => $resultadoDeuda,
            ],
            'servicios' => $servicios,
        ];
    }

    public function show($cliente)
    {
        $data = $this->getClienteData($cliente);

        if (!$data) return response()->json(['message' => 'Cliente no encontrado'], 404);

        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        //
        $user = User::findOrFail($id);
        //$user = $request->user();

        $validated = $request->validate(array_merge(self::$rulesUpdate, [
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ]));

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        DB::transaction(fn() => $user->update($validated));

        return response()->json([
            'mensaje' => 'Registro Actualizado',
            'data'    => $user->fresh(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        //
    }
}
