<?php

namespace App\Service\Invoice;

use Cron\MonthField;
use DateTime;

class InvoiceService
{
    //constantes para codificar invoice
    const BASE62_INVOICE = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    const LETTERS_INVOICE = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";


    //obtener mes en español
    public static function obtenerMesEnEspanolMayus(DateTime $date)
    {
        $meses = [
            "ENERO",
            "FEBRERO",
            "MARZO",
            "ABRIL",
            "MAYO",
            "JUNIO",
            "JULIO",
            "AGOSTO",
            "SEPTIEMBRE",
            "OCTUBRE",
            "NOVIEMBRE",
            "DICIEMBRE",
        ];
        $idx = (int)$date->format('n') - 1; // 'n' devuelve el mes (1-12) y restamos 1 para obtener el índice correcto
        return $meses[$idx] || "";
    }

    public static function addMonthsToYm(String $ym, String $monthsToAdd)
    {
        if (!preg_match('/^(\d{4})-(\d{2})$/', $ym, $m)) { // Validar formato 'Y-m'
            return null;
        }

        $y = (int)$m[1]; // El año es el primer grupo de captura
        $mm = (int)$m[2]; // El mes es el segundo grupo de captura

        if (!is_int($y) || !is_int($mm)) {
            return null; // Validar que ambos sean enteros
        }

        try {
            $date = new DateTime("$y-$mm-01");
            // Agregar los meses
            $monthsToAdd = (int)$monthsToAdd;
            if ($monthsToAdd !== 0) {
                $date->modify(($monthsToAdd > 0 ? '+' : '') . $monthsToAdd . ' months');
            }

            return $date->format('Y-m');
        } catch (\Exception $e) {
            return null;
        }
    }

    //función para convertir un número a base62 para invoice
    public static function toBase62InvoiceBigInt(String $value)
    {
        $n = (string)$value;

        if ($n === 0) return "0";
        $result = "";

        //BCMath para el manejo de numeros grandes https://www.php.net/manual/es/book.bc.php
        while (bccomp($n, "0") > 0) { //bccomp — Comparar dos números de gran tamaño
            $r = bcmod($n, "62"); //bcmod — Devuelve el resto de una división entre números de gran tamaño
            $result = self::BASE62_INVOICE[(int)$r] . $result;
            $n = bcdiv($n, "62", 0); //bcdiv — Divide dos números de precisión arbitraria

        }

        return $result;
    }

    public static function obtenerFechaActualFactura14()
    {
        return (new DateTime())->format('YmdHis');
    }

    public static function generarInvoiceEncriptado(String $cliente, String $fecha14)
    {
        $num = (string)$fecha14; // Convertir a string 

        $letterIndex = (int) bcmod($num, "52"); // Obtener índice para la letra
        $baseNum = bcdiv($num, "52", 0); // Obtener el número base para codificar

        $codigo = self::toBase62InvoiceBigInt($baseNum); // Convertir a base62

        $letra = self::LETTERS_INVOICE[(int)$letterIndex]; // Obtener la letra correspondiente

        return trim((string)$cliente) . '-' . $codigo . $letra;
    }

    // Función principal para construir el invoice desde el cliente
    public static function construirInvoiceDesdeBilling(String $cliente)
    {
        $clienteStr = trim((string)$cliente);
        if ($clienteStr === "") { // Si el cliente es una cadena vacía, devolver solo el mes en español
            return self::obtenerMesEnEspanolMayus(new DateTime());
        }
        $fecha = self::obtenerFechaActualFactura14(); // Obtener la fecha actual en formato 'YmdHis'
        return self::generarInvoiceEncriptado($clienteStr, $fecha); // Generar el invoice encriptado utilizando el cliente y la fecha
    }

    public static function formatearMontoPagoralia(String $value)
    {
        $n = is_numeric($value) ? (float)$value : 0.0; // Convertir a número, si no es numérico, usar 0.0

        if (!is_finite($n)) return "0";

        $rounded = round($n); // devolver sin decimales si es un número entero
        if (abs($n - $rounded) < 0.0001) return (string)$rounded;
        // Formatear con dos decimales y eliminar ceros innecesarios
        return (string)number_format($n, 2, '.', '');
    }


    public static function separarNombreApellido(string $nombre)
    {
        $partes = preg_split('/\s+/', trim($nombre));
        $total = count($partes);

        if ($total === 0) { 
            return ['nombre' => '', 'apellido' => ''];
        }

        if ($total === 1) { 
            return ['nombre' => $partes[0], 'apellido' => ''];
        }

        if ($total === 2) { // 2  → 1 nombre + 1 apellido
            return [
                'nombre' => $partes[0],
                'apellido' => $partes[1]
            ];
        }

        if ($total === 3) { // 3  → 1 nombre + 2 apellidos
            return [
                'nombre' => $partes[0],
                'apellido' => $partes[1] . ' ' . $partes[2]
            ];
        }

        // 4 o más → 2 nombres + resto apellidos
        return [
            'nombre' => $partes[0] . ' ' . $partes[1],
            'apellido' => implode(' ', array_slice($partes, 2))
        ];
    }
}
