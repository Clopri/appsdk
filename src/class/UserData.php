<?php

declare(strict_types=1);

/**
 * Clase Stub para UserData.
 * Gestiona la información de los usuarios del sistema.
 */
class UserData
{
    /** @var string Nombre de la tabla. */
    public static $tablename = "user";

    // --- Propiedades ---

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

    /** @var int Estado (1=Activo, etc). */
    public $status;

    /** @var int|string Tipo de usuario (Rol/Nivel). */
    public $kind;

    /** @var string Fecha de creación (SQL Datetime). */
    public $created_at;

    /** @var string Información del navegador/cliente (User Agent). */
    public $client_info;

    /** @var string|null Nombre del archivo de imagen de perfil. */
    public $image;

    public function __construct() {}

    /**
     * Agrega un nuevo usuario.
     * @return array|bool Resultado de la operación.
     */
    public function add()
    {
        return false;
    }

    /**
     * Agrega un nuevo usuario incluyendo imagen.
     * @return array|bool Resultado de la operación.
     */
    public function add_with_image()
    {
        return false;
    }

    /**
     * Elimina un usuario por su ID (Método estático).
     * @param int|string $id
     * @return void
     */
    public static function delById($id) {}

    /**
     * Elimina el usuario actual (Instancia).
     * @return void
     */
    public function del() {}

    /**
     * Actualiza la información básica del usuario.
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
     * Obtiene un usuario por su ID.
     * @param int|string $id
     * @return self|null Objeto UserData o null si no existe.
     */
    public static function getById($id)
    {
        return null;
    }

    /**
     * Obtiene todos los usuarios.
     * @return self[] Array de objetos UserData.
     */
    public static function getAll()
    {
        return [];
    }

    /**
     * Busca usuarios por nombre (LIKE).
     * @param string $q Término de búsqueda.
     * @return self[] Array de objetos UserData.
     */
    public static function getLike($q)
    {
        return [];
    }
}
