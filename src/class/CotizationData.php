<?php

declare(strict_types=1);

/**
 * Clase Stub para CotizationData (Cotizaciones).
 * Gestiona la creación y consulta de cotizaciones.
 */
class CotizationData
{
    /** @var string Nombre de la tabla. */
    public static $tablename = "cotizations";

    // --- Propiedades de la Base de Datos ---

    /** @var int|string ID de la cotización. */
    public $id;

    /** @var int|string Estado (1=Activa, etc). */
    public $status;

    /** @var int|string ID del usuario que creó la cotización. */
    public $user_id;

    /** @var string Fecha de creación (SQL Datetime). */
    public $created_at;

    /** @var string Info del navegador (User Agent). */
    public $client_info;

    /** @var int|string ID del cliente (Person). */
    public $person_id;

    /** @var int|string ID de referencia (ej. ID de venta futura o temporal). */
    public $ref_id;

    /** @var float Subtotal. */
    public $subtotal;

    /** @var float Descuento. */
    public $discount;

    /** @var float Impuestos. */
    public $taxes;

    /** @var float Total final. */
    public $total;

    /** @var string Nota o detalle de la cotización. */
    public $note;

    // --- Propiedades Dinámicas (Inyectadas por JOINs) ---

    /** @var string Nombre del cliente (Inyectado en getAll/getActive). */
    public $name;

    /** @var string Apellido del cliente (Inyectado en getAll/getActive). */
    public $lastname;


    public function __construct() {}

    /**
     * Obtiene el objeto del cliente asociado.
     * @return PersonData|null
     */
    public function getPerson()
    {
        return null;
    }

    /**
     * Obtiene el objeto del usuario creador.
     * @return UserData|null
     */
    public function getUser()
    {
        return null;
    }

    /**
     * Crea la cotización en la base de datos.
     * @return array|bool Resultado de la operación.
     */
    public function add()
    {
        return false;
    }

    /**
     * Actualiza solo el ID de referencia.
     * @return array|bool
     */
    public function update_ref_id()
    {
        return false;
    }

    /**
     * Elimina la cotización actual (Instancia).
     * @return void
     */
    public function del() {}

    /**
     * Elimina una cotización y sus operaciones asociadas por ID.
     * @param int|string $id
     * @return void
     */
    public static function delById($id) {}

    /**
     * Obtiene una cotización por su ID.
     * @param int|string $id
     * @return self|null
     */
    public static function getById($id)
    {
        return null;
    }

    /**
     * Obtiene todas las cotizaciones con datos del cliente.
     * @return self[]
     */
    public static function getAll()
    {
        return [];
    }

    /**
     * Obtiene cotizaciones activas (status=1) con datos del cliente.
     * @return self[]
     */
    public static function getActive()
    {
        return [];
    }

    /**
     * Obtiene cotizaciones en un rango de fechas.
     * @param string $start Fecha inicio (Y-m-d).
     * @param string $end Fecha fin (Y-m-d).
     * @return self[]
     */
    public static function getAllByDate($start, $end)
    {
        return [];
    }

    /**
     * Obtiene cotizaciones por rango de fechas filtrado por usuario.
     * @param int|string $user ID del usuario.
     * @param string $start Fecha inicio.
     * @param string $end Fecha fin.
     * @return self[]
     */
    public static function getAllByDateOpByUserId($user, $start, $end)
    {
        return [];
    }
}
