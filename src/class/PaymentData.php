<?php

declare(strict_types=1);

/**
 * Clase Stub para PaymentData.
 * Gestiona los pagos, abonos y saldos.
 */
class PaymentData
{
    /** @var string Nombre de la tabla. */
    public static $tablename = "payment";

    // --- Propiedades de la Base de Datos ---

    /** @var int|string ID del pago. */
    public $id;

    /** @var int|string Estado (1=Activo, etc). */
    public $status;

    /** @var int|string ID del usuario que registró el pago. */
    public $user_id;

    /** @var string Fecha de creación (SQL Datetime). */
    public $created_at;

    /** @var string Info del navegador (User Agent). */
    public $client_info;

    /** @var int|string ID del cliente (Person). */
    public $person_id;

    /** @var int|string ID del tipo de pago. */
    public $payment_type_id;

    /** @var int|string ID de la venta asociada. */
    public $sell_id;

    /** @var float Valor/Monto del pago. */
    public $val;

    /** @var int|bool Indica si está saldada (1 o 0). */
    public $saldada;

    /** @var string UUID único del pago. */
    public $UUID;

    // --- Propiedades Dinámicas (Calculadas por SQL) ---

    /** @var float Total acumulado (usado en métodos sumBy...). */
    public $total;


    public function __construct() {}

    /**
     * Verifica si un UUID ya existe.
     * @param string $uuid
     * @return bool
     */
    public static function isUUID($uuid)
    {
        return false;
    }

    /**
     * Obtiene el cliente asociado (Alias de getPerson).
     * @return PersonData|null
     */
    public function getClient()
    {
        return null;
    }

    /**
     * Obtiene el tipo de pago.
     * @return PaymentTypeData|null
     */
    public function getPaymentType()
    {
        return null;
    }

    /**
     * Obtiene la persona asociada.
     * @return PersonData|null
     */
    public function getPerson()
    {
        return null;
    }

    /**
     * Obtiene el usuario responsable.
     * @return UserData|null
     */
    public function getUser()
    {
        return null;
    }

    /**
     * Agrega un pago estándar.
     * @return array|bool
     */
    public function add()
    {
        return false;
    }

    /**
     * Agrega un pago con detalles de abono (UUID, saldada).
     * @return array|bool
     */
    public function add_payment()
    {
        return false;
    }

    /**
     * Elimina un pago por ID estático.
     * @param int|string $id
     * @return void
     */
    public static function delById($id) {}

    /**
     * Elimina el pago actual.
     * @return void
     */
    public function del() {}

    /**
     * Actualiza el estado a saldado para todo un cliente.
     * @return void
     */
    public function update_saldo() {}

    /**
     * Salda una venta específica de un cliente.
     * @return array|bool
     */
    public function saldar()
    {
        return false;
    }

    /**
     * Obtiene un pago por ID.
     * @param int|string $id
     * @return self|null
     */
    public static function getById($id)
    {
        return null;
    }

    /**
     * Obtiene el pago asociado a una venta.
     * @param int|string $id Sell ID.
     * @return self|null
     */
    public static function getBySellId($id)
    {
        return null;
    }

    /**
     * Busca por nombre (si existe columna name).
     * @param string $name
     * @return self|null
     */
    public static function getByName($name)
    {
        return null;
    }

    /**
     * Obtiene todos los pagos.
     * @return self[]
     */
    public static function getAll()
    {
        return [];
    }

    /**
     * Obtiene todos los abonos (payment_type_id = 2).
     * @return self[]
     */
    public static function getAllAbono()
    {
        return [];
    }

    /**
     * Obtiene abonos realizados por un usuario específico.
     * @param int|string $id User ID.
     * @return self[]
     */
    public static function getAllAbonoByUser($id)
    {
        return [];
    }

    /**
     * Obtiene abonos por rango de fecha.
     * @param string $start Fecha inicio.
     * @param string $end Fecha fin.
     * @return self[]
     */
    public static function getAllByDate($start, $end)
    {
        return [];
    }

    /**
     * Obtiene abonos por rango de fecha y usuario.
     * @return self[]
     */
    public static function getAllByDateAndUser($start, $end, $id)
    {
        return [];
    }

    /**
     * Obtiene abonos por rango de fecha y cliente.
     * @return self[]
     */
    public static function getAllByDateAndClient($start, $end, $id)
    {
        return [];
    }

    /**
     * Obtiene todos los pagos de un cliente.
     * @param int|string $id Person ID.
     * @return self[]
     */
    public static function getAllByClientId($id)
    {
        return [];
    }

    /**
     * Suma total de pagos no saldados por cliente.
     * @param int|string $id Person ID.
     * @return self|object Retorna objeto con propiedad ->total.
     */
    public static function sumByClientId($id)
    {
        return null;
    }

    /**
     * Obtiene pagos antiguos agrupados que no suman cero.
     * @param int|string $id Person ID.
     * @return self|object Retorna objeto con ->total y ->sell_id.
     */
    public static function getOldSellId($id)
    {
        return null;
    }

    /**
     * Suma de abonos (tipo 2) para una venta específica.
     * @param int|string $id Sell ID.
     * @return self|object Retorna objeto con ->total.
     */
    public static function sumPaymentByClientId($id)
    {
        return null;
    }

    /**
     * Suma total de pagos para una venta específica (cualquier tipo).
     * @param int|string $id Sell ID.
     * @return self|object Retorna objeto con ->total.
     */
    public static function sumPaymentByPendingId($id)
    {
        return null;
    }

    /**
     * Suma pagos de un cliente en una venta específica.
     * @param int|string $id Person ID.
     * @param int|string $sell_id Sell ID.
     * @return self|object Retorna objeto con ->total.
     */
    public static function sumByClientBySellId($id, $sell_id)
    {
        return null;
    }

    /**
     * Suma pagos a crédito (no saldados) de un cliente en una venta.
     * @param int|string $id Person ID.
     * @param int|string $sell_id Sell ID.
     * @return self|object Retorna objeto con ->total.
     */
    public static function sumByClientByCreditId($id, $sell_id)
    {
        return null;
    }
}
