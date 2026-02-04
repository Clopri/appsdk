<?php

declare(strict_types=1);

/**
 * Clase Stub para ReData (Reabastecimientos / Compras / Gastos).
 * Gestiona las operaciones de entrada de inventario y gastos.
 */
class ReData
{
    /** @var string Nombre de la tabla. */
    public static $tablename = "re";

    // --- Propiedades de la Base de Datos ---

    /** @var int|string ID de la operación/reabastecimiento. */
    public $id;

    /** @var string Fecha de creación. */
    public $created_at;

    /** @var int|string|null ID de referencia (Vinculación con Operación). */
    public $ref_id;

    /** @var string Info del cliente (User Agent). */
    public $client_info;

    /** @var int|string ID del proveedor/persona. */
    public $person_id;

    /** @var int|string ID del usuario que registró. */
    public $user_id;

    /** @var int|string ID de la condición de pago (PData). */
    public $p_id;

    /** @var int|string|null ID de factura externa. */
    public $bill_id;

    /** @var float Monto total. */
    public $total;

    /** @var float|bool Monto pagado o indicador de pagado. */
    public $paid;

    /** @var float Impuesto ITBIS. */
    public $itbis;

    /** @var string Comprobante Fiscal (NCF). */
    public $ncf;

    /** @var int|string|null ID Almacén destino. */
    public $stock_to_id;

    /** @var int|string|null ID Almacén/Venta origen. */
    public $sell_from_id;

    /** @var float Efectivo/Dinero involucrado. */
    public $cash;

    /** @var int|string Estado (1=Activo). */
    public $status;

    // --- Nuevas Propiedades ---

    /** @var string|null Fecha de vencimiento. */
    public $expirationDate;

    /** @var string|null JSON para datos adicionales. */
    public $dataJSON;

    /** @var float Descuento aplicado. */
    public $discount;

    /** @var float Costo de envío/delivery. */
    public $delivery;

    /** @var string|null Fecha de la factura del proveedor. */
    public $invoiceDate;

    /** @var string|null Número de la factura del proveedor. */
    public $invoiceNumber;

    /** @var string Nota o comentario. */
    public $note;

    /** @var float Otros impuestos. */
    public $taxes;

    // --- Propiedades Dinámicas (Inyectadas por JOINs o Cálculos) ---

    /** @var float Deuda restante (Calculado en reportes). */
    public $remaining_debt;

    /** @var string Estado de la deuda ('Pagada', 'Pendiente'). */
    public $debt_status;

    /** @var string Nombre del proveedor. */
    public $name;

    /** @var string Apellido del proveedor. */
    public $lastname;

    /** @var string Compañía del proveedor. */
    public $company;

    /** @var string Descripción del método de pago (p.name). */
    public $pdesc;


    public function __construct() {}

    /**
     * Reporte de Cuentas por Pagar (Reabastecimientos).
     * @return array<int, object>
     */
    public static function getReportRe($user_id = null, $no = null, $max = null, $min = null, $provider_id = null)
    {
        return [];
    }

    /** @return PersonData|null */
    public function getPerson()
    {
        return null;
    }

    /** @return UserData|null */
    public function getUser()
    {
        return null;
    }

    /** @return PData|null */
    public function getP()
    {
        return null;
    }

    /**
     * Registra el reabastecimiento/compra.
     * @return array|bool
     */
    public function add()
    {
        return false;
    }

    /**
     * Agrega registro parcial (posiblemente para transferencias/devoluciones).
     * @return array|bool
     */
    public function add_de()
    {
        return false;
    }

    /**
     * Elimina por ID (y sus operaciones asociadas).
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
     * Actualiza el ID de referencia.
     * @return array|bool
     */
    public function update_ref_id()
    {
        return false;
    }

    /**
     * Actualiza metadatos de la factura (fechas, números, descuentos).
     * @return array|bool
     */
    public function update_invoice_info()
    {
        return false;
    }

    /**
     * Actualiza el estado de pagado.
     * @return array|bool
     */
    public function update_p()
    {
        return false;
    }

    /**
     * Actualiza tipo de pago y efectivo.
     * @return array|bool
     */
    public function update_payment()
    {
        return false;
    }

    /**
     * Obtiene por ID.
     * @param int|string $id
     * @return self|null
     */
    public static function getById($id)
    {
        return null;
    }

    /**
     * Cancela la operación (cambia estado de entrega y pago).
     * @return array|bool
     */
    public function cancel()
    {
        return false;
    }

    /**
     * Obtiene compras a crédito.
     * @return self[]
     */
    public static function getCredits()
    {
        return [];
    }

    /**
     * Obtiene compras a crédito por usuario.
     * @return self[]
     */
    public static function getCreditsByUserId($id)
    {
        return [];
    }

    /**
     * Suma total por proveedor (categoría).
     * @return self|object Retorna objeto con propiedad 's' (suma).
     */
    public static function CountByCategory($id)
    {
        return null;
    }

    /** @return self[] */
    public static function getCreditsByStockId($id)
    {
        return [];
    }

    /** @return self[] */
    public static function getSellsByClientId($id)
    {
        return [];
    }

    /** @return self[] */
    public static function getSellsUnBoxed()
    {
        return [];
    }

    /**
     * Obtiene reabastecimientos con datos del proveedor y tipo de pago.
     * @return self[]
     */
    public static function getRes()
    {
        return [];
    }

    /**
     * Obtiene reabastecimientos pendientes de pago (Credito/Abono y pagado=0).
     * @return self[]
     */
    public static function getPaymentRes()
    {
        return [];
    }

    /** @return self[] */
    public static function getResByStockId($id)
    {
        return [];
    }

    /** @return self[] */
    public static function getResToPay()
    {
        return [];
    }

    /** @return self[] */
    public static function getResToPayByStockId($id)
    {
        return [];
    }

    /** @return self[] */
    public static function getResToReceive()
    {
        return [];
    }

    /** @return self[] */
    public static function getResToReceiveByStockId($id)
    {
        return [];
    }

    /** @return self[] */
    public static function getSQL($sql)
    {
        return [];
    }

    /** @return self[] */
    public static function getAllBySQL($sqlextra)
    {
        return [];
    }

    /** @return self[] */
    public static function getAllByDateOp($start, $end, $op)
    {
        return [];
    }

    /** @return self[] */
    public static function getAllByOp($start, $end, $op)
    {
        return [];
    }

    /** @return self[] */
    public static function getAllByDateOpByUserId($user, $start, $end, $op)
    {
        return [];
    }

    /** @return self[] */
    public static function getAllByOpByUserId($user, $start, $end, $op)
    {
        return [];
    }

    /**
     * Agrupación estadística por fecha y operación.
     * @return array<int, object{id: int, tot: float, t: float, c: int}>
     */
    public static function getGroupByDateOp($start, $end, $op)
    {
        return [];
    }

    /** @return self[] */
    public static function getAllByDateBCOp($clientid, $start, $end, $op)
    {
        return [];
    }

    /** @return self[] */
    public static function getAllByBCOp($clientid, $start, $end, $op)
    {
        return [];
    }

    /** @return self[] */
    public static function getAllByDateBCOpByUserId($user, $clientid, $start, $end, $op)
    {
        return [];
    }

    /** @return self[] */
    public static function getAllByBCOpByUserId($user, $clientid, $start, $end, $op)
    {
        return [];
    }
}
