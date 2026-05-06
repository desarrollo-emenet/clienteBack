<?php

namespace App\Service\Invoice;

use Psy\Readline\Hoa\Console;

class desencriptarInvoiceService{
    // ===== CONSTANTES =====
const BASE62 = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
const LETTERS = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';


public static function desencriptarInvoice(string $invoiceRaw){
    $t = trim($invoiceRaw);

    //validar formato
    if (!preg_match('/^\s*(\d+)\s*-\s*([0-9a-zA-Z]+)\s*$/', $t, $m)) {
        throw new \Exception('Formato inválido. Usa CLIENTE-CODIGO (ej. 02122-6RhWgQwM)');
    }

    $cliente = trim($m[1] ?? '');
    $codigoCompleto = trim($m[2] ?? '');
    if (strlen($codigoCompleto) < 2) {
        throw new \Exception('Código demasiado corto');
    }

    //separar letra y código
    $letra = substr($codigoCompleto, -1);
    $codigo = substr($codigoCompleto, 0, -1);
    $letterIndex = strpos(self::LETTERS, $letra);
    if ($letterIndex === false) {
        throw new \Exception('El último carácter debe ser una letra (a-z o A-Z)');
    }

    //convertir código base62 a número
    $baseNum = 0;
    foreach (str_split($codigo) as $ch) {
        $idx = strpos(self::BASE62, $ch);
        if ($idx === false) {
            throw new \Exception('Código contiene caracteres inválidos');
        }
        $baseNum = bcadd(bcmul($baseNum, "62"), (string)$idx);
    }

    //reconstruir número original
    $num = bcadd(bcmul($baseNum,"52"), (string)$letterIndex);
    $fecha = str_pad($num, 14, '0', STR_PAD_LEFT);

    if (!preg_match('/^\d{14}$/', $fecha)) {
        throw new \Exception('No se pudo reconstruir la fecha');
    }

    return [
        'cliente' => $cliente,
        /*'referencia' => "$cliente-$fecha",
        'fecha' => $fecha,
        'año' => substr($fecha, 0, 4),
        'mes' => substr($fecha, 4, 2),
        'dia' => substr($fecha, 6, 2),
        'hora' => substr($fecha, 8, 2),
        'minuto' => substr($fecha, 10, 2),
        'segundo' => substr($fecha, 12, 2),*/

        'fecha' => substr($fecha, 0, 4) . '-' . substr($fecha, 4, 2) . '-' . substr($fecha, 6, 2) . ' ' .
                            substr($fecha, 8, 2) . ':' . substr($fecha, 10, 2) . ':' . substr($fecha, 12, 2)
    ];
    
}

}