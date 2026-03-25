<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RecoveryPasswordController;
use App\Http\Controllers\Auth\VerifyMailController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;


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


//Proteccion de rutas
Route::get('/users', function () {
    // Only verified users may access this route...
})->middleware(['auth', 'verified']);

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


Route::get('servicios', [ServiceController::class,  'Index'])->middleware('auth:sanctum');
Route::post('servicio', [ServiceController::class,  'AddService'])->middleware('auth:sanctum');
Route::delete('servicio/{servicio}', [ServiceController::class,  'destroy'])->middleware('auth:sanctum');
Route::get('verify-access-service/{numero}', [ServiceController::class, 'verificarAcceso'])->middleware('auth:sanctum');


//rutas de recuperacion de contraseña
Route::post('auth/recoverPassword', [RecoveryPasswordController::class,  'sendEmail']);
Route::put('auth/updatePassword', [RecoveryPasswordController::class,  'updatePassword']);
Route::post('/verify-token', [VerifyMailController::class, 'validarToken']);

//rutas login y logout
Route::post('auth/login', LoginController::class);
Route::get('auth/logout', [LogoutController::class, 'logout'])->middleware('auth:sanctum');

//Route::get('/ruta-con-log', function () {return 'Esta ruta registrará sus encabezados';})->middleware('logear.encabezados');


//ruta formulario de correo
//Route::post('/send-email', [FormController::class, 'send']);
