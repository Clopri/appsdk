<?php

declare(strict_types=1);

class clopriRequest
{
    /**
     * Obtiene y sanitiza parámetros $_GET.
     *
     * @param string $key Clave del parámetro $_GET.
     * @param mixed $default Valor por defecto si no existe o falla la validación.
     * @param 'string'|'int'|'integer'|'float'|'double'|'bool'|'boolean'|'email'|'url'|'slug'|'array'|'raw' $type Tipo de sanitización requerida.
     * @return mixed
     */
    public static function get(string $key, mixed $default = null, string $type = 'string'): mixed
    {
        return null;
    }

    /**
     * Obtiene y sanitiza parámetros $_POST.
     *
     * @param string $key Clave del parámetro $_POST.
     * @param mixed $default Valor por defecto si no existe o falla la validación.
     * @param 'string'|'int'|'integer'|'float'|'double'|'bool'|'boolean'|'email'|'url'|'slug'|'array'|'raw' $type Tipo de sanitización requerida.
     * @return mixed
     */
    public static function post(string $key, mixed $default = null, string $type = 'string'): mixed
    {
        return null;
    }

    /**
     * Obtiene el cuerpo JSON de forma eficiente.
     * Compatible con PHP 8.3 (json_validate).
     *
     * @param string|null $key Clave específica del JSON a obtener (null para todo el array).
     * @param mixed $default Valor por defecto si la clave no existe.
     * @return mixed
     */
    public static function json(string $key = null, mixed $default = null): mixed
    {
        return null;
    }

    /**
     * Núcleo de sanitización usando PHP 8 Match Expressions.
     *
     * @param mixed $value Valor a sanitizar.
     * @param mixed $default Valor por defecto.
     * @param string $type Tipo de filtro.
     * @return mixed
     */
    private static function sanitize(mixed $value, mixed $default, string $type): mixed
    {
        return null;
    }

    /**
     * Limpieza recursiva de strings.
     *
     * @param mixed $value Valor a limpiar.
     * @return string|array
     */
    private static function cleanString(mixed $value): string|array
    {
        return '';
    }
}
