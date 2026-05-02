<?php

use App\Http\Controllers\Auth\authController;
use App\Http\Controllers\Auth\RecoveryPasswordController;
use App\Http\Controllers\Auth\VerifyMailController;
use App\Http\Controllers\servicios\ServiceController;
use App\Http\Controllers\servicios\serviciosController;
use App\Http\Controllers\User\PagoraliaController;
use App\Http\Controllers\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*prueba
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('auth/login', [authController::class, 'login']);

route::middleware(['auth:sanctum'])->group(function (){
    Route::get('auth/logout', [authController::class, 'logout']);


    Route::apiResource('servicios', serviciosController::class)->only("index", "store", "destroy");
    //Route::post('servicio', [ServiceController::class, 'AddService']);
    Route::post('servicios/verificar', [serviciosController::class, 'confirmarServicio']);
    Route::get('verify-access-service/{numero}', [ServiceController::class, 'verificarAcceso']);
    Route::post('/pagoralia/orden-pago', [PagoraliaController::class, 'crearOrdenPagoralia']);
});


//Controlador de verificacion
Route::get('/email/verify/{id}/{hash}', [VerifyMailController::class, 'verify'])
    ->middleware(['signed'])->name('verification.verify');

//Reenvio del correo electronico
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

//rutas controlador usuario
Route::apiResource('usuarios',UserController::class);
Route::middleware('auth:sanctum')->get('cliente/{numero}', [UserController::class, 'clientePorNumero']);




//rutas de recuperacion de contraseña
Route::post('auth/recoverPassword', [RecoveryPasswordController::class,  'sendEmail']);
Route::put('auth/updatePassword', [RecoveryPasswordController::class,  'updatePassword']);
Route::post('/verify-token', [VerifyMailController::class, 'validarToken']);


//ruta formulario de correo
//Route::post('/send-email', [FormController::class, 'send']);
