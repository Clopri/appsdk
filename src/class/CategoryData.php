<?php

declare(strict_types=1);

/**
 * Clase Stub para CategoryData.
 * Gestiona las categorías de productos y servicios.
 */
class CategoryData
{
    /** @var string Nombre de la tabla. */
    public static $tablename = "category";

    // --- Propiedades de la Base de Datos ---

    /** @var int|string ID de la categoría. */
    public $id;

    /** @var string|int Estado de la categoría (1=Activo, etc). */
    public $status;

    /** @var int|string ID del usuario que creó/modificó. */
    public $user_id;

    /** @var string Fecha de creación. */
    public $created_at;

    /** @var string Info del cliente (User Agent). */
    public $client_info;

    /** @var int|string Tipo de categoría. */
    public $type;

    /** @var string Prefijo de la categoría. */
    public $prefix;

    /** @var string Descripción o nombre de la categoría. */
    public $description;

    /** @var string|null JSON para datos adicionales. */
    public $dataJSON;

    // --- Propiedades Dinámicas (Inyectadas por JOINs) ---

    /** @var string Descripción del estado (de tabla status). */
    public $status_dsc;


    public function __construct() {}

    /**
     * Agrega una nueva categoría.
     * @return void
     */
    public function add() {}

    /**
     * Cambia el estado de una categoría (Borrado lógico o cambio de estatus).
     * @param int|string $status Nuevo estado.
     * @return void
     */
    public function del($status) {}

    /**
     * Actualiza la información de la categoría.
     * @return void
     */
    public function update() {}

    /**
     * Obtiene una categoría por ID.
     * @param int|string $id
     * @return self|null
     */
    public static function getById($id)
    {
        return null;
    }

    /**
     * Obtiene todas las categorías (incluye descripción de estado).
     * @return self[]
     */
    public static function getAll()
    {
        return [];
    }

    /**
     * Obtiene solo las categorías activas (status = 1).
     * @return self[]
     */
    public static function getAllActive()
    {
        return [];
    }

    /**
     * Obtiene categorías activas filtradas por tipo.
     * @param int|string $type
     * @return self[]
     */
    public static function getByType($type)
    {
        return [];
    }

    /**
     * Busca categorías por descripción (LIKE).
     * @param string $q Término de búsqueda.
     * @return self[]
     */
    public static function getLike($q)
    {
        return [];
    }

    /**
     * Verifica si existe un prefijo repetido.
     * @param string $prefix
     * @return self[]
     */
    public static function getRepeated($prefix)
    {
        return [];
    }

    /**
     * Verifica si existe un prefijo repetido excluyendo un ID específico (para updates).
     * @param string $prefix
     * @param int|string $id
     * @return self[]
     */
    public static function getRepeatedById($prefix, $id)
    {
        return [];
    }
}
