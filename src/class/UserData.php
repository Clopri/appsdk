<?php

declare(strict_types=1);

/**
 * Clase Stub para UserData.
 * Gestiona los usuarios del sistema.
 */
class UserData
{
    /** @var string Nombre de la tabla. */
    public static $tablename = "user";

    // --- Propiedades de la Base de Datos ---

    /** @var int|string ID del usuario. */
    public $id;

    /** @var string Nombre. */
    public $name;

    /** @var string Apellido. */
    public $lastname;

    /** @var string Nombre de usuario (Login). */
    public $username;

    /** @var string Correo electrónico. */
    public $email;

    /** @var string Contraseña (Hash). */
    public $password;

    /** @var string|null Ruta de la imagen de perfil. */
    public $image;

    /** @var int|string Estado del usuario (1=Activo, etc). */
    public $status;

    /** @var int|string Tipo de usuario (Rol/Nivel). */
    public $kind;

    /** @var string Fecha de creación. */
    public $created_at;

    /** @var string Info del navegador/cliente (User Agent). */
    public $client_info;

    /** @var string|null Permisos del usuario (JSON o Serializado). */
    public $permits;

    /** @var string|null JSON para datos adicionales. */
    public $dataJSON;


    public function __construct() {}

    /**
     * Agrega un nuevo usuario.
     * @return array|bool
     */
    public function add()
    {
        return false;
    }

    /**
     * Agrega un nuevo usuario incluyendo imagen.
     * @return array|bool
     */
    public function add_with_image()
    {
        return false;
    }

    /**
     * Elimina usuario por ID (Estático).
     * @param int|string $id
     * @return void
     */
    public static function delById($id) {}

    /**
     * Elimina la instancia actual del usuario.
     * @return void
     */
    public function del() {}

    /**
     * Actualiza la información del usuario (incluyendo permisos y JSON).
     * @return void
     */
    public function update() {}

    /**
     * Actualiza solo la contraseña.
     * @return void
     */
    public function update_passwd() {}

    /**
     * Actualiza solo la imagen de perfil.
     * @return void
     */
    public function update_image() {}

    /**
     * Obtiene un usuario por ID.
     * @param int|string $id
     * @return self|null
     */
    public static function getById($id)
    {
        return null;
    }

    /**
     * Obtiene todos los usuarios.
     * @param bool $erased Si es true busca status=2 (borrados), sino status=1.
     * @return self[]
     */
    public static function getAll($erased = false)
    {
        return [];
    }

    /**
     * Busca usuarios por nombre (LIKE).
     * @param string $q Término de búsqueda.
     * @return self[]
     */
    public static function getLike($q)
    {
        return [];
    }
}
