<?php

declare(strict_types=1);

/**
 * Clase Stub para CashDeskData.
 * Gestiona la apertura, cierre y transacciones de la caja.
 */
class CashDeskData
{
    /** @var string Nombre de la tabla. */
    public static $tablename = "cashdesk";

    // --- Propiedades de la Tabla ---

    /** @var int|string ID del corte de caja. */
    public $id;

    /** @var int|string ID del usuario responsable. */
    public $user_id;

    /** @var string Fecha y hora de apertura. */
    public $opening_time;

    /** @var string|null Fecha y hora de cierre. */
    public $close_time;

    /** @var float Monto inicial en caja. */
    public $start_amount;

    /** @var float Monto final al cierre. */
    public $end_amount;

    /** @var float Monto desbalanceado (diferencia). */
    public $unbalanced;

    /** @var string Nota de cierre. */
    public $note;

    // --- Propiedades Relacionales y Dinámicas ---

    /** @var UserData|array|null Objeto del usuario (llenado por fill). */
    public $user;

    /** * Transacciones del turno (Ventas + Abonos).
     * Contiene objetos SellData modificados con campos extra:
     * 'tipo' (CREDITO/CONTADO/ABONO), 'pagado', 'facturado', 'transaction_id'.
     * * @var array<int, SellData|object{tipo: string, pagado: float, facturado: float, transaction_id: int}> 
     */
    public $transactions;

    /** @var float Propiedad dinámica: Monto acumulado en el turno (calculado en SQL). */
    public $monto_acumulado;

    /** @var int Propiedad dinámica: Total de ventas/tickets (calculado en SQL). */
    public $total_ventas;

    public function __construct() {}

    /**
     * Obtiene reportes de caja con cálculos de acumulados.
     * * @param int|string|null $user_id
     * @param int|string|null $cashdesk_id
     * @param string|null $max Fecha fin (Y-m-d).
     * @param string|null $min Fecha inicio (Y-m-d).
     * @return array<int, object{
     * id: int, 
     * opening_time: string, 
     * close_time: ?string, 
     * start_amount: float, 
     * end_amount: float, 
     * monto_acumulado: float, 
     * total_ventas: int
     * }>
     */
    public static function getCashDesk($user_id = null, $cashdesk_id = null, $max = null, $min = null)
    {
        return [];
    }

    /**
     * Abre una nueva caja (Insertar).
     * @return array|bool
     */
    public function add()
    {
        return false;
    }

    /**
     * Elimina una caja por ID estático.
     * @param int|string $id
     * @return void
     */
    public static function delById($id) {}

    /**
     * Elimina la caja actual (Instancia).
     * @return void
     */
    public function del() {}

    /**
     * Cierra la caja actual calculando totales.
     * @param string $note Nota de cierre.
     * @param float $unbalanced Monto de diferencia (sobrante/faltante).
     * @return void
     */
    public function close($note = '', $unbalanced = 0) {}

    /**
     * Obtiene y mezcla las transacciones (Ventas y Pagos) del turno.
     * Modifica el objeto pasado por referencia agregando la propiedad ->transactions.
     * * @param self $cashDesk
     * @return self
     */
    public static function getTransaction($cashDesk)
    {
        return $cashDesk;
    }

    /**
     * Calcula el monto final esperado (Ventas Efectivo + Abonos).
     * @param self $cashDesk
     * @return float
     */
    public static function getEndAmount($cashDesk)
    {
        return 0.00;
    }

    /**
     * Rellena el objeto con User, Transactions y EndAmount.
     * @param self|null $cashDesk
     * @return self|null
     */
    public static function fill($cashDesk)
    {
        return null;
    }

    /**
     * Obtiene una caja por ID (completamente llena).
     * @param int|string $id
     * @return self|null
     */
    public static function getById($id)
    {
        return null;
    }

    /**
     * Obtiene todas las cajas.
     * @return self[]
     */
    public static function getAll()
    {
        return [];
    }

    /**
     * Obtiene las últimas cajas abiertas (JOIN con User).
     * Si end_amount es <= 0, lo calcula al vuelo.
     * * @param int $limitRegister
     * @return self[] Retorna objetos con datos de usuario inyectados.
     */
    public static function getCashDeskLimitNoTransaction($limitRegister = 10)
    {
        return [];
    }

    /**
     * Busca si el usuario tiene una caja abierta actualmente.
     * @param int|string $user_id
     * @return self|null
     */
    public static function getOpenCashDeskByUserId($user_id)
    {
        return null;
    }

    /**
     * Obtiene histórico de una caja por ID (Alias de getById pero semántico).
     * @param int|string $id
     * @return self|null
     */
    public static function getHistoryCashDeskById($id)
    {
        return null;
    }

    /**
     * Abre una caja para un usuario si no tiene una abierta.
     * @param int|string $user_id
     * @param float $start_amount
     * @return self|null Retorna la caja abierta.
     */
    public static function openUserCashDesk($user_id, $start_amount)
    {
        return null;
    }
}
