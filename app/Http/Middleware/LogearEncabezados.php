<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Log; // Importa la clase Log


class LogearEncabezados
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        //Log::info('Encabezados de la solicitud:', $request->headers->all());
        return $next($request);
    }
}
