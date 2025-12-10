<?php

declare(strict_types=1);

/**
 * Clase Stub para SellData (Ventas).
 * Facilita el autocompletado en IDEs.
 */
class SellData
{
    /** @var string Nombre de la tabla. */
    public static $tablename = "sell";

    // --- Propiedades de la Base de Datos ---

    /** @var int|string ID de la venta. */
    public $id;

    /** @var string Estado de la venta ('1' activo, etc). */
    public $status;

    /** @var int|string ID del usuario vendedor. */
    public $user_id;

    /** @var string Fecha de creación (Y-m-d H:i:s). */
    public $created_at;

    /** @var string User Agent o info del cliente. */
    public $client_info;

    /** @var string Método de pago (Efectivo, Transferencia, etc). */
    public $payment_method;

    /** @var string Comprobante fiscal. */
    public $ncf;

    /** @var string Nota de la venta. */
    public $note;

    /** @var int|string ID de transacción externa. */
    public $transaction_id;

    /** @var int|string ID del cliente (Person). */
    public $person_id;

    /** @var string Nombre del cliente (Inyectado). */
    public $name;

    /** @var string Apellido del cliente (Inyectado). */
    public $lastname;

    /** @var int ID condición de pago (1=Contado, 2=Crédito, etc). */
    public $p_id;

    /** @var string Tipo de venta ('AL CONTADO', 'CREDITO'). */
    public $tipo;

    /** @var float Monto total facturado. */
    public $facturado;

    /** @var float Monto pagado. */
    public $pagado;

    /** @var float Monto pendiente (Calculado). */
    public $pendiente;

    /** @var float Total pendiente acumulado. */
    public $totalpendiente;

    /** @var int|bool Indica si está saldada. */
    public $saldada;

    /** @var float Efectivo recibido. */
    public $cash;

    /** @var float Impuestos (IVA). */
    public $iva;

    /** @var float Descuento aplicado. */
    public $discount;

    /** @var int ID Almacén origen. */
    public $stock_from_id;

    /** @var int ID Almacén destino. */
    public $stock_to_id;

    /** @var float Subtotal antes de impuestos. */
    public $subtotal;

    /** @var float Impuestos adicionales. */
    public $taxes;

    /** @var float Total final. */
    public $total;

    /** @var float Ganancia calculada de la venta. */
    public $wealth;

    /** @var int ID de estado de entrega (Delivery status). */
    public $d_id;

    /** @var int|null ID de la caja (CashDesk). */
    public $box_id;

    /** @var int|string ID de transferencia bancaria. */
    public $banktransferID;

    /** @var string|null Fecha de vencimiento del crédito. */
    public $creditExpirationDate;

    /** @var string UUID único de la venta. */
    public $UUID;


    public function __construct() {}

    /**
     * Reporte de Cuentas por Cobrar.
     * * @return array<int, object{
     * id: int,
     * total: float,
     * cash: float,
     * ncf: string,
     * total_paid: float,
     * remaining_debt: float,
     * debt_status: 'Pagada'|'Vencida'|'Pendiente'
     * }>
     */
    public static function getReportReceivable($user_id = null, $no = null, $max = null, $min = null, $client_id = null, $typedoc = null, $bankID = null, $type_payment = null, $statuPayment = null)
    {
        return [];
    }

    /**
     * Reporte para formato 607 (DGII).
     * * @return array<int, object{
     * cedula_rnc: string,
     * ncf_full_number: string,
     * type_id: string,
     * total: float,
     * iva: float
     * }>
     */
    public static function getreport607($user_id = null, $no = null, $max = null, $min = null, $client_id = null, $typedoc = null, $bankID = null, $type_payment = null, $status = 1)
    {
        return [];
    }

    /**
     * Reporte general de ventas.
     * @return object[] Lista de objetos genéricos con datos de venta y NCF.
     */
    public static function getReportSell($user_id = null, $no = null, $max = null, $min = null, $client_id = null, $typedoc = null, $bankID = null, $type_payment = null, $status)
    {
        return [];
    }

    /**
     * Obtiene datos detallados para análisis de IA (Venta + Operación + Producto + Cliente).
     * * @param int $limitSelect Límite de registros.
     * @return array<int, object{
     * sell_id: int,
     * total_venta: float,
     * fecha_venta: string,
     * wealth: float,
     * nombre_item: string,
     * precio_item: float,
     * cantidad: float,
     * nombre_cliente: string,
     * email_cliente: string
     * }>
     */
    public function getSellIA($limitSelect = 300)
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

    /** @return StockData|null */
    public function getStockFrom()
    {
        return null;
    }

    /** @return StockData|null */
    public function getStockTo()
    {
        return null;
    }

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
     * Crea la venta en BD.
     * @return array|bool
     */
    public function add()
    {
        return false;
    }

    /**
     * Elimina venta y sus operaciones/pagos asociados.
     * @param int|string $id
     * @return void
     */
    public static function delById($id) {}

    /**
     * Elimina la instancia actual.
     */
    public function del() {}

    /**
     * Finaliza una cotización convirtiéndola en venta.
     */
    public function process_cotization() {}

    public function update_box() {}
    public function update_d() {}
    public function update_p() {}
    public function update_payment() {}

    /**
     * Obtiene una venta por ID.
     * @param int|string $id
     * @return self|null
     */
    public static function getById($id)
    {
        return null;
    }

    public function cancel() {}

    /**
     * Obtiene todas las ventas formateadas en un array personalizado.
     * NOTA: Devuelve array de arrays, no objetos.
     * * @param string|null $date_from
     * @param string|null $date_to
     * @return array<int, array{
     * id: int,
     * created_at: string,
     * facturado: float,
     * pagado: float,
     * pendiente: float,
     * wealth: float,
     * tipo: string,
     * usuario: string,
     * name: string,
     * lastname: string,
     * rnc: string,
     * ncf: string,
     * status: int,
     * payment_method: string
     * }>
     */
    public static function getAll($date_from = null, $date_to = null)
    {
        return [];
    }

    /**
     * Obtiene ventas por rango de fecha, recalculando pagos parciales.
     * @return self[]
     */
    public static function getAllSellByDate($start, $end)
    {
        return [];
    }

    /**
     * Detalles de venta unificados (Crédito/Contado/Abonos).
     * @return self|null
     */
    public static function getSellsDetails($sell_id, $transaction_id)
    {
        return null;
    }

    /**
     * Obtiene todos los pagos realizados.
     * @return self[]
     */
    public static function getPaymentAll()
    {
        return [];
    }

    /** @return self[] */
    public static function getSellsByUserId($id)
    {
        return [];
    }

    /** @return self[] */
    public static function getCredits()
    {
        return [];
    }

    /** @return self[] */
    public static function getCreditsByUserId($id)
    {
        return [];
    }

    /**
     * Suma total de créditos por cliente.
     * @return ServiceData|object Objeto con propiedad 's' (suma).
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
    public static function getSellsToDeliver()
    {
        return [];
    }

    /** @return self[] */
    public static function getSellsToDeliverByUserId($id)
    {
        return [];
    }

    /** @return self[] */
    public static function getSellsToDeliverByStockId($id)
    {
        return [];
    }

    /** @return self[] */
    public static function getSellsToDeliverByClient($id)
    {
        return [];
    }

    /** @return self[] */
    public static function getSellsToCob()
    {
        return [];
    }

    /** @return self[] */
    public static function getSellsToCobByUserId($id)
    {
        return [];
    }

    /** @return self[] */
    public static function getSellsToCobByStockId($id)
    {
        return [];
    }

    /** @return self[] */
    public static function getSellsToCobByClientId($id)
    {
        return [];
    }

    /** @return self[] */
    public static function getSellsUnBoxed()
    {
        return [];
    }

    /** @return self[] */
    public static function getByBoxId($id)
    {
        return [];
    }

    /** @return self[] */
    public static function getRes()
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

    /** * @return array<int, object{id: int, tot: float, t: float, c: int}> 
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

    /**
     * Obtiene lista de abonos/pagos de una venta.
     * @param int|string $sell_id
     * @return array<int, object{val: float, created_at: string, UUID: string, id: int}>
     */
    public static function getAbono($sell_id)
    {
        return [];
    }

    /**
     * Elimina un abono específico.
     * @param int|string $id
     * @return void
     */
    public static function delAbonoById($id) {}
}
