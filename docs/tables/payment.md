# 💳 Tabla `payment` — Pagos, Deudas de Crédito y Abonos

La tabla `payment` gestiona el libro auxiliar de **Cuentas por Cobrar (CxC)** de Clopri.

### Regla Contable Clave de `payment`:
1. **Deuda Inicial (`payment_type_id = 1`)**: Cuando se emite una factura a crédito (`sell.cash < sell.total`), se inserta un registro en `payment` con `payment_type_id = 1` y el campo **`val` en negativo** (`-(total - cash)`), representando el cargo/deuda del cliente.
2. **Abono o Cobro (`payment_type_id = 2`)**: Cada vez que el cliente realiza un pago o abono a esa factura, se inserta un registro con `payment_type_id = 2` y el campo **`val` en positivo** (`+monto_abonado`).
3. **Balance Pendiente**: La suma algebraica `SUM(val)` de una factura no saldada (`saldada = 0`) arroja el saldo pendiente (en negativo). Cuando `SUM(val)` llega a `0`, todos los movimientos de esa factura se actualizan a `saldada = 1`.

---

## 📋 Estructura de Columnas

| Campo | Tipo | Nulo | Por Defecto | Descripción y Uso |
| :--- | :--- | :--- | :--- | :--- |
| **`id`** | `INT` (PK, AI) | No | `AUTO_INCREMENT` | Identificador único del movimiento de pago. |
| **`person_id`** | `INT` | No | — | ID del cliente (`person.id`). |
| **`sell_id`** | `INT` | No | — | ID de la factura asociada (`sell.id`). |
| **`payment_type_id`** | `INT` | No | — | Tipo de registro:<br>• `1` = **Cargo / Deuda inicial de crédito** (`val` es negativo)<br>• `2` = **Abono / Pago recibido** (`val` es positivo) |
| **`val`** | `DECIMAL(12,2)` | No | `0.00` | Monto del movimiento:<br>• Negativo (ej. `-2500.00`) para cargos iniciales (`payment_type_id = 1`).<br>• Positivo (ej. `1000.00`) para abonos de clientes (`payment_type_id = 2`). |
| **`saldada`** | `TINYINT(1)` | No | `0` | • `0` = La factura aún mantiene balance pendiente.<br>• `1` = La deuda de la factura ya fue saldada por completo. |
| **`payment_method`** | `VARCHAR(50)` | Sí | `''` | Método de pago del abono (`'Efectivo'`, `'Transferencia'`, `'Tarjeta'`, `'Cheque'`). |
| **`UUID`** | `VARCHAR(64)` | Sí | `NULL` | Identificador único del recibo de ingreso. Si un cliente abona a 3 facturas en un solo cobro, los 3 registros en `payment` comparten el mismo `UUID` para imprimirse en un solo recibo agrupado. |
| **`dataJSON`** | `JSON / TEXT` | Sí | `NULL` | Metadatos adicionales en formato JSON. |
| **`status`** | `INT` | Sí | `1` | Estado del registro (`1` = Activo). |
| **`user_id`** | `INT` | No | `user_id` | ID del usuario que registró el cargo o cobro. |
| **`created_at`** | `DATETIME` | No | `NOW()` | Fecha y hora del registro. |

---

## 🛠️ Ejemplos de Uso con `Executor::doit($sql)`

### 1. Registrar la Deuda Inicial al Crear una Venta a Crédito
```php
$uid = intval(user_id);
$deuda = -abs($totalFactura - $efectivoRecibido); // SIEMPRE negativo

$sql = "INSERT INTO payment (person_id, sell_id, user_id, val, payment_type_id, saldada, created_at)
        VALUES ($person_id, $sell_id, $uid, $deuda, 1, 0, NOW())";
Executor::doit($sql);
```

### 2. Registrar un Abono y Saldar Factura si el Balance Llega a Cero
```php
$uid = intval(user_id);
$uuidRecibo = uniqid('rcp_', true);
$montoAbono = abs(floatval($abono)); // SIEMPRE positivo

// 1. Consultar deuda pendiente actual de la factura
$res = Executor::doit("SELECT COALESCE(SUM(val), 0) AS total FROM payment WHERE person_id = $person_id AND sell_id = $sell_id AND saldada = 0");
$row = $res[0]->fetch_assoc();
$pendienteActual = abs(floatval($row['total']));

$esSaldada = ($montoAbono >= ($pendienteActual - 0.01)) ? 1 : 0;

// 2. Insertar el abono
$sqlAbono = "INSERT INTO payment (person_id, sell_id, user_id, val, payment_type_id, saldada, created_at, UUID, payment_method)
             VALUES ($person_id, $sell_id, $uid, $montoAbono, 2, $esSaldada, NOW(), '$uuidRecibo', 'Efectivo')";
Executor::doit($sqlAbono);

// 3. Si saldó el total pendiente, marcar toda la factura como saldada = 1
if ($esSaldada === 1) {
    Executor::doit("UPDATE payment SET saldada = 1, user_id = $uid WHERE person_id = $person_id AND sell_id = $sell_id");
}
```

### 3. Obtener Recibos de Pago Agrupados por `UUID`
```php
$sql = "SELECT 
            p.UUID,
            MAX(p.id) AS id,
            SUM(p.val) AS total_val,
            MAX(p.created_at) AS created_at,
            MAX(p.payment_method) AS payment_method,
            per.id AS person_id,
            per.name AS client_name,
            per.lastname AS client_lastname
        FROM payment p
        INNER JOIN person per ON p.person_id = per.id
        WHERE p.UUID IS NOT NULL
          AND p.UUID != ''
          AND p.payment_type_id = 2
        GROUP BY p.UUID, per.id
        ORDER BY MAX(p.created_at) DESC";

$res = Executor::doit($sql);
```
