<?php

declare(strict_types=1);

/**
 * Clase Stub para ConfigurationData.
 * Gestiona las configuraciones del sistema (tabla configuration).
 */
class ConfigurationData
{
    /** @var string Nombre de la tabla. */
    public static $tablename = "configuration";

    // --- Propiedades de la Base de Datos ---

    /** @var int|string ID de la configuración. */
    public $id;

    /** @var string|null Código o prefijo corto (ej: "company_name"). */
    public $short;

    /** @var string Nombre descriptivo. */
    public $name;

    /** @var int|string Tipo de dato (si aplica). */
    public $kind;

    /** @var string|mixed Valor de la configuración. */
    public $val;

    /** @var string Nombre corto o alias. */
    public $short_name;

    /** @var int|bool Indica si está activo (1=Sí, 0=No). */
    public $is_active;

    /** @var string Estado del registro. */
    public $status;

    /** @var string Fecha de creación. */
    public $created_at;

    /** @var string Info del cliente (User Agent). */
    public $client_info;


    public function __construct() {}

    /**
     * Agrega una nueva configuración.
     * @return void
     */
    public function add() {}

    /**
     * Elimina una configuración por ID.
     * @param int|string $id
     * @return void
     */
    public static function delById($id) {}

    /**
     * Elimina la instancia actual.
     * @return void
     */
    public function del() {}

    /**
     * Actualiza la configuración (name, short_name, is_active).
     * @return void
     */
    public function update() {}

    /**
     * Actualiza el valor de una configuración buscando por su clave 'short'.
     * @param string $name Clave/Short (ej: "skin").
     * @param string|mixed $val Nuevo valor.
     * @return void
     */
    public static function updateValFromName($name, $val) {}

    /**
     * Obtiene configuración por ID.
     * @param int|string $id
     * @return self|null
     */
    public static function getById($id)
    {
        return null;
    }

    /**
     * Obtiene configuración por su prefijo/short.
     * @param string $id El 'short' o clave a buscar.
     * @return self|null
     */
    public static function getByPreffix($id)
    {
        return null;
    }

    /**
     * Obtiene todas las configuraciones.
     * @return self[]
     */
    public static function getAll()
    {
        return [];
    }

    /**
     * Obtiene solo las configuraciones activas (is_active = 1).
     * @return self[]
     */
    public static function getPublics()
    {
        return [];
    }
}
