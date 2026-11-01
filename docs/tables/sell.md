# 📄 Tabla `sell` — Facturas y Ventas

La tabla `sell` almacena la cabecera de todas las facturas de venta emitidas en Clopri (tanto al contado como a crédito). Cada registro en `sell` se relaciona con:
- **`person`** (`sell.person_id = person.id`): Cliente al que se le emite la factura.
- **`operation`** (`operation.sell_id = sell.id` con `operation.is_sell = 1` y `operation.operation_type_id = 2`): Líneas de productos o servicios vendidos.
- **`payment`** (`payment.sell_id = sell.id`): Deuda inicial (cuando es a crédito) y abonos posteriores.
- **`ncf_detail`** (`ncf_detail.sell_id = sell.id`): Detalle del comprobante fiscal emitido.

---

## 📋 Estructura de Columnas

| Campo | Tipo | Nulo | Por Defecto | Descripción y Uso |
| :--- | :--- | :--- | :--- | :--- |
| **`id`** | `INT` (PK, AI) | No | `AUTO_INCREMENT` | Identificador único de la factura. Se obtiene en `$res[1]` tras ejecutar `Executor::doit($sql)`. |
| **`ref_id`** | `INT / VARCHAR` | Sí | `NULL` | Referencia opcional o enlace externo. |
| **`person_id`** | `INT` | No | — | ID del cliente en la tabla `person` (`person.tipo = 3`). |
| **`p_id`** | `INT` | No | `1` | Condición de pago de la factura:<br>• `1` = **Al Contado**<br>• `2` = **A Crédito**<br>• `3` = Abono / Mixto<br>• `4` = Crédito especial |
| **`d_id`** | `INT` | Sí | `1` | Estado de entrega de la mercancía:<br>• `1` = Entregado<br>• `2` = Pendiente de entrega<br>• `3` = Cancelado |
| **`box_id`** | `INT` | Sí | `NULL` | ID del corte de caja histórico al que fue asociada la venta (`NULL` hasta cierre). |
| **`subtotal`** | `DECIMAL(12,2)` | No | `0.00` | Suma de `(cantidad * precio_unitario)` antes de impuestos y descuentos. |
| **`taxes`** | `DECIMAL(12,2)` | No | `0.00` | Monto monetario total de impuestos (ITBIS) cobrados en la factura. |
| **`discount`** | `DECIMAL(12,2)` | No | `0.00` | Monto monetario descontado al subtotal/total de la factura. |
| **`total`** | `DECIMAL(12,2)` | No | `0.00` | Monto total final a pagar: `(subtotal + taxes) - discount`. |
| **`cash`** | `DECIMAL(12,2)` | No | `0.00` | Efectivo o monto pagado en el momento de emitir la factura:<br>• Si es **Al Contado** (`p_id = 1`): `cash >= total`.<br>• Si es **A Crédito** (`p_id = 2`): `cash < total` (normalmente `0` o el inicial abonado). |
| **`iva`** | `DECIMAL(5,2)` | No | `18.00` | Tasa porcentual de impuesto general aplicada (ej. `18` para 18% de ITBIS). |
| **`wealth`** | `DECIMAL(12,2)` | Sí | `0.00` | Ganancia bruta total de la venta: `SUM(q * (price_out - price_in))`. Si hay descuento, la utilidad neta se calcula restando `discount`. |
| **`ncf`** | `VARCHAR(30)` | Sí | `''` | Número de Comprobante Fiscal asignado (ej. `B0100000001`, `B0200000001`, `E310000000001`). |
| **`payment_method`** | `VARCHAR(50)` | Sí | `''` | Método de pago utilizado: `'Efectivo'`, `'Transferencia'`, `'Tarjeta'`, `'Cheque'`, etc. |
| **`banktransferID`** | `INT` | Sí | `0` | ID de la cuenta bancaria o referencia de transferencia (`0` si fue en efectivo). |
| **`creditExpirationDate`** | `DATETIME` | Sí | `NULL` | Fecha límite de pago cuando la venta es a crédito (`NULL` en ventas al contado). |
| **`note`** | `TEXT` | Sí | `''` | Nota, comentario u observación impresa en la factura. |
| **`stock_from_id`** | `INT` | Sí | `NULL` | ID de almacén origen (usado en traspasos). |
| **`stock_to_id`** | `INT` | Sí | `NULL` | ID de almacén destino. |
| **`UUID`** | `VARCHAR(64)` | Sí | — | Identificador único universal para prevenir facturas duplicadas por doble clic o reintentos de red. |
| **`dataJSON`** | `JSON / TEXT` | Sí | `NULL` | Objeto JSON con metadatos extendidos:<br>• `$.legalTip`: Monto de propina legal (10%).<br>• `$.ecf.legalStatus`: Estado fiscal electrónico (`ACCEPTED`, `REJECTED`, `INVALID`).<br>• `$.ecf.status`: Estado interno del e-CF. |
| **`status`** | `INT` | No | `1` | Estado del registro:<br>• `1` = Activa / Válida<br>• `0` o `2` = Anulada / Eliminada |
| **`user_id`** | `INT` | No | `user_id` | ID del usuario cajero/vendedor que emitió la factura (`user.id`). |
| **`client_info`** | `VARCHAR(255)` | Sí | `NULL` | Información del navegador/dispositivo desde donde se creó. |
| **`created_at`** | `DATETIME` | No | `NOW()` | Fecha y hora exacta de emisión de la factura. |

---

## 🛠️ Ejemplos de Uso con `Executor::doit($sql)`

### 1. Insertar una Factura Al Contado
```php
$uid = intval(user_id);
$uuid = uniqid('sell_', true);

$sql = "INSERT INTO sell (
            person_id, p_id, subtotal, taxes, total, cash, iva, discount,
            ncf, payment_method, note, user_id, created_at, wealth,
            banktransferID, creditExpirationDate, UUID, dataJSON, status
        ) VALUES (
            5, 1, 1000.00, 180.00, 1180.00, 1180.00, 18, 0.00,
            'B0200000015', 'Efectivo', 'Venta mostrador', $uid, NOW(), 350.00,
            0, NULL, '$uuid', NULL, 1
        )";

$res = Executor::doit($sql);
$sell_id = intval($res[1]); // ID de la factura recién creada
```

### 2. Consultar Ventas por Rango de Fechas con Datos del Cliente y NCF
```php
$desde = '2026-10-01';
$hasta = '2026-10-31';

$sql = "SELECT 
            s.id,
            s.created_at,
            s.subtotal,
            s.taxes,
            s.discount,
            s.total,
            s.cash,
            s.payment_method,
            COALESCE(nd.ncf, s.ncf) AS ncf,
            JSON_UNQUOTE(JSON_EXTRACT(s.dataJSON, '$.legalTip')) AS legalTip,
            p.name AS client_name,
            p.lastname AS client_lastname,
            p.no AS client_rnc,
            CASE WHEN s.p_id = 1 THEN 'AL CONTADO' ELSE 'CREDITO' END AS condicion
        FROM sell s
        LEFT JOIN person p ON s.person_id = p.id
        LEFT JOIN ncf_detail nd ON s.id = nd.sell_id
        WHERE s.status = 1
          AND DATE(s.created_at) BETWEEN '$desde' AND '$hasta'
        ORDER BY s.created_at DESC";

$query = Executor::doit($sql);
```

### 3. Consultar Cuentas por Cobrar (Ventas a Crédito Pendientes o Vencidas)
```php
$sql = "SELECT 
            s.id,
            s.person_id,
            s.total,
            s.cash,
            s.created_at,
            s.creditExpirationDate,
            COALESCE(nd.ncf, s.ncf) AS ncf,
            LEAST(IFNULL(SUM(CASE WHEN pay.val > 0 THEN pay.val ELSE 0 END), 0), s.total) AS total_paid,
            (s.total - LEAST(IFNULL(SUM(CASE WHEN pay.val > 0 THEN pay.val ELSE 0 END), 0), s.total)) AS remaining_debt,
            CASE 
                WHEN (s.total - LEAST(IFNULL(SUM(CASE WHEN pay.val > 0 THEN pay.val ELSE 0 END), 0), s.total)) < 1 THEN 'Pagada'
                WHEN (s.creditExpirationDate IS NOT NULL AND s.creditExpirationDate < CURDATE()) THEN 'Vencida'
                ELSE 'Pendiente'
            END AS debt_status
        FROM sell s
        LEFT JOIN ncf_detail nd ON nd.sell_id = s.id
        LEFT JOIN payment pay ON pay.sell_id = s.id
        WHERE s.status = 1
          AND s.cash < s.total
        GROUP BY s.id
        HAVING debt_status IN ('Pendiente', 'Vencida')
        ORDER BY s.created_at DESC";

$query = Executor::doit($sql);
```
