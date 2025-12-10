<?php

declare(strict_types=1);

/**
 * Clase de Almacenamiento Seguro (Sandbox) - Versión Estática.
 * Gestiona archivos dentro de una carpeta aislada por 'packageId' automáticamente.
 * Uso: ClopriStorage::save('archivo.json', ['datos' => 1]);
 */
class clopriStorage
{
    /** @var string|null Cache de la ruta base para no recalcularla en cada llamada. */
    private static ?string $sandboxPath = null;

    /** @var string[] Extensiones permitidas (Lista Blanca). */
    private const ALLOWED_EXTENSIONS = ['json', 'txt', 'csv', 'log', 'xml', 'md'];

    /**
     * Inicializador interno (Singleton pattern para la ruta).
     * Se ejecuta automáticamente antes de cualquier operación.
     *
     * @return string Ruta absoluta del directorio sandbox.
     */
    private static function init(): string
    {
        return '';
    }

    /**
     * Guarda contenido en un archivo de forma segura.
     * Si el contenido es array, se convierte a JSON automáticamente.
     *
     * @param string $filename Nombre del archivo (ej: 'data.json').
     * @param string|array $content Contenido a guardar.
     * @return int|false Número de bytes escritos o false si hubo error.
     */
    public static function save(string $filename, string|array $content): int|false
    {
        return 0;
    }

    /**
     * Lee el contenido de un archivo.
     *
     * @param string $filename Nombre del archivo a leer.
     * @return string|null Contenido del archivo o null si no existe.
     */
    public static function read(string $filename): ?string
    {
        return null;
    }

    /**
     * Elimina un archivo.
     *
     * @param string $filename Nombre del archivo a eliminar.
     * @return bool True si se eliminó o no existía, False si falló el sistema.
     */
    public static function delete(string $filename): bool
    {
        return false;
    }

    /**
     * Lista los archivos en el directorio de la App.
     *
     * @return string[] Lista de nombres de archivos encontrados.
     */
    public static function listFiles(): array
    {
        return [];
    }

    /**
     * Verifica si un archivo existe.
     *
     * @param string $filename Nombre del archivo.
     * @return bool True si existe, False si no.
     */
    public static function exists(string $filename): bool
    {
        return false;
    }

    /**
     * Valida y construye la ruta completa.
     *
     * @param string $filename Nombre del archivo.
     * @param bool $mustExist Si es true, lanza excepción si el archivo no existe.
     * @return string Ruta absoluta validada.
     * @throws SecurityException Si la ruta es insegura o extensión no permitida.
     * @throws FileNotFoundException Si $mustExist es true y el archivo no está.
     */
    private static function resolvePath(string $filename, bool $mustExist = false): string
    {
        return '';
    }
}

// Excepciones para que el IDE las reconozca
class SecurityException extends Exception {}
class FileNotFoundException extends Exception {}
