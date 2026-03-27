<?php

use App\Http\Controllers\Auth\authController;
use App\Http\Controllers\Auth\RecoveryPasswordController;
use App\Http\Controllers\Auth\VerifyMailController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\servicios\serviciosController;
use App\Http\Controllers\UserController;
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

Route::post('auth/login', [authController::class, 'login']);
// Route::apiResource('servicios', serviciosController::class)->only("index");

// Route::get('/servicios', [ServiceController::class, 'index2']);



route::middleware(['auth:sanctum'])->group(function (){
    Route::get('auth/logout', [authController::class, 'logout']);


    Route::apiResource('servicios', serviciosController::class)->only("index", "store", "destroy");
    Route::post('servicios/verificar', [serviciosController::class, 'confirmarServicio']);
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

Route::get('verify-access-service/{numero}', [ServiceController::class, 'verificarAcceso'])->middleware('auth:sanctum');


//rutas de recuperacion de contraseña
Route::post('auth/recoverPassword', [RecoveryPasswordController::class,  'sendEmail']);
Route::put('auth/updatePassword', [RecoveryPasswordController::class,  'updatePassword']);
Route::post('/verify-token', [VerifyMailController::class, 'validarToken']);

//rutas login y logout


//Route::get('/ruta-con-log', function () {return 'Esta ruta registrará sus encabezados';})->middleware('logear.encabezados');


//ruta formulario de correo
//Route::post('/send-email', [FormController::class, 'send']);
