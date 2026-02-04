<?php

declare(strict_types=1);

/**
 * Clase Stub para Utils.
 * Utilidades generales del sistema.
 */
class Utils
{
    /**
     * Formatea un valor numérico a formato de moneda.
     * Limpia caracteres no numéricos antes de formatear.
     *
     * @param string $currency Símbolo de moneda (ej: "$", "RD$").
     * @param string|float|int $value Valor a formatear.
     * @return string Cadena formateada (ej: "$ 1,500.00").
     */
    public static function moneyFormat($currency, $value)
    {
        return '';
    }

    /**
     * Imprime una pantalla HTML de "Acceso denegado".
     * Renderiza una vista con un mensaje de error y estilos CSS embebidos.
     * * @return void
     */
    public static function noPermissionPrint() {}
}
