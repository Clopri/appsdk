<?php

declare(strict_types=1);

/**
 * Clase Stub para LocationCache.
 * Gestiona el almacenamiento en caché de archivos JSON en el servidor.
 */
class LocationCache
{
    /** @var string Ruta del directorio de caché. */
    private $dirCache;

    public function __construct() {}

    /**
     * Obtiene datos del caché por su clave.
     *
     * @param string $key Clave del archivo (sin extensión .json).
     * @return array|null Datos decodificados o null si no existe/falla.
     */
    public static function get($key)
    {
        return null;
    }

    /**
     * Guarda datos en el caché.
     * Crea el directorio si no existe.
     *
     * @param string $key Clave del archivo.
     * @param mixed $data Datos a serializar (array, objeto, string, etc).
     * @return void
     */
    public static function set($key, $data) {}

    /**
     * Elimina un archivo del caché.
     *
     * @param string $key Clave del archivo a eliminar.
     * @return void
     */
    public static function remove($key) {}
}
