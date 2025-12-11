<?php

class clopriCache
{
    /**
     * Almacenamiento puramente en RAM.
     * En un entorno de testing, esto simula el cache sin tocar el disco.
     */
    private static $memory = [];

    /**
     * Obtiene el valor del cache simulado.
     * * @param string $key
     * @return mixed|null
     */
    public static function get($key)
    {
        // Retorna el valor si existe, o null si no existe.
        return array_key_exists($key, self::$memory) ? self::$memory[$key] : null;
    }

    /**
     * Guarda el valor en el cache simulado.
     * * @param string $key
     * @param mixed $data
     * @return void
     */
    public static function set($key, $data)
    {
        // Guarda directamente en el array estático.
        // Simulamos el éxito de la operación siempre.
        self::$memory[$key] = $data;
    }

    /**
     * Elimina el valor del cache simulado.
     * * @param string $key
     * @return void
     */
    public static function remove($key)
    {
        if (array_key_exists($key, self::$memory)) {
            unset(self::$memory[$key]);
        }
    }

    /**
     * Método auxiliar para limpiar todo el cache (útil en tests)
     * Este método no existe en la original, pero es común en Stubs.
     */
    public static function flush()
    {
        self::$memory = [];
    }
}
