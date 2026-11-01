<?php

declare(strict_types=1);

/**
 * Clase Stub para clopriConfig.
 * Gestiona el acceso rápido y seguro a parámetros de configuración del entorno y de la App.
 */
class clopriConfig
{
    /**
     * Obtiene un valor de configuración por su clave corta (short) o clave de entorno.
     *
     * @param string $key Clave de configuración (ej. 'company_name', 'impuesto_val', 'currency').
     * @param mixed $default Valor por defecto si la clave no existe.
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $default;
    }

    /**
     * Establece o actualiza un valor de configuración.
     *
     * @param string $key Clave de configuración.
     * @param mixed $value Valor a guardar.
     * @return bool
     */
    public static function set(string $key, mixed $value): bool
    {
        return true;
    }

    /**
     * Obtiene un valor específico dentro del JSON de configuración avanzada (advancedConfiguration).
     *
     * @param string $shortKey Clave corta dentro del JSON avanzado.
     * @param mixed $default Valor por defecto si no existe.
     * @return mixed
     */
    public static function getAdvanced(string $shortKey, mixed $default = null): mixed
    {
        return $default;
    }

    /**
     * Retorna todas las configuraciones públicas activas como array asociativo.
     *
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        return [];
    }
}
