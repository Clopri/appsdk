# 🚚 Tabla `re` — Compras, Reabastecimiento y Cuentas por Pagar

La tabla `re` registra las facturas de compra de mercancía a proveedores y controla las **Cuentas por Pagar (CxP)** por reabastecimiento de inventario.

### Relación con `operation`:
Cada compra en `re` se vincula con sus líneas de productos entrantes mediante `re.ref_id = operation.sell_id`, donde cada ítem en `operation` se inserta con **`operation_type_id = 1` (ENTRADA)** e `is_draft = 0`, sumando automáticamente al inventario.

---

## 📋 Estructura de Columnas

| Campo | Tipo | Nulo | Por Defecto | Descripción y Uso |
| :--- | :--- | :--- | :--- | :--- |
| **`id`** | `INT` (PK, AI) | No | `AUTO_INCREMENT` | Identificador único del registro de compra. |
| **`ref_id`** | `INT / VARCHAR` | Sí | `NULL` | Referencia que enlaza la cabecera `re` con sus renglones en `operation.sell_id`. |
| **`bill_id`** | `INT` | Sí | `0` | ID o número de orden/documento interno. |
| **`person_id`** | `INT` | No | — | ID del proveedor en la tabla `person` (`person.tipo = 2`). |
| **`p_id`** | `INT` | No | `1` | Condición de pago al proveedor:<br>• `1` = Pagado al Contado<br>• `2`, `3`, `4` = A Crédito / Pendiente de pago |
| **`total`** | `DECIMAL(12,2)` | No | `0.00` | Monto total de la factura de compra. |
| **`paid`** | `DECIMAL(12,2)` | No | `0.00` | Monto pagado al proveedor (`0` si está totalmente pendiente; `total - paid` = deuda restante). |
| **`itbis`** | `DECIMAL(12,2)` | No | `0.00` | Monto de ITBIS adelantado en compras (para reporte fiscal 606). |
| **`taxes`** | `DECIMAL(12,2)` | Sí | `NULL` | Impuestos adicionales o selectivos. |
| **`discount`** | `DECIMAL(12,2)` | Sí | `NULL` | Descuento otorgado por el proveedor. |
| **`delivery`** | `DECIMAL(12,2)` | Sí | `NULL` | Costo de flete, transporte o envío. |
| **`ncf`** | `VARCHAR(30)` | Sí | `''` | NCF emitido por el proveedor (ej. `B01...` o `E31...`). |
| **`invoiceNumber`** | `VARCHAR(100)` | Sí | `NULL` | Número de factura interna del proveedor. |
| **`invoiceDate`** | `DATE` | Sí | `NULL` | Fecha de emisión de la factura del proveedor. |
| **`expirationDate`** | `DATE` | Sí | `NULL` | Fecha de vencimiento del crédito con el proveedor. |
| **`note`** | `TEXT` | Sí | `''` | Observaciones o notas de recepción. |
| **`stock_to_id`** | `INT` | Sí | `NULL` | ID de almacén destino (usado en devoluciones/movimientos). |
| **`sell_from_id`** | `INT` | Sí | `NULL` | ID de venta origen (cuando es devolución). |
| **`dataJSON`** | `JSON / TEXT` | Sí | `NULL` | Metadatos adicionales en formato JSON. |
| **`status`** | `INT` | No | `1` | `1` = Activo, `0` = Inactivo/Anulado. |
| **`user_id`** | `INT` | No | `user_id` | Usuario que registró la compra. |
| **`created_at`** | `DATETIME` | No | `NOW()` | Fecha y hora de registro en el sistema. |

---

## 🛠️ Ejemplos de Uso con `Executor::doit($sql)`

### 1. Registrar una Compra de Mercancía y Sumar al Inventario
```php
$uid = intval(user_id);
$ref_id = time();

// 1. Insertar cabecera de compra en `re`
$sqlRe = "INSERT INTO re (
              ref_id, bill_id, person_id, p_id, total, paid, itbis, status,
              user_id, created_at, ncf, expirationDate, discount, delivery,
              invoiceDate, invoiceNumber, dataJSON, note, taxes
          ) VALUES (
              $ref_id, 0, 14, 2, 25000.00, 0.00, 3813.56, 1,
              $uid, NOW(), 'B0100004512', '2026-11-30', 0, 500.00,
              CURDATE(), 'FAC-9821', NULL, 'Compra a 30 días', 0
          )";
$resRe = Executor::doit($sqlRe);

// 2. Insertar productos comprados en `operation` (operation_type_id = 1 -> ENTRADA)
$sqlOp = "INSERT INTO operation (
              status, price_in, min_price, max_price, price_out, stock_id,
              product_id, q, operation_type_id, sell_id, user_id, created_at,
              itbis, name, idStore
          ) VALUES (
              1, 500.00, 750.00, 850.00, 850.00, 0,
              '22', '50', 1, '$ref_id', $uid, NOW(),
              '0', 'Resma de Papel Carta', 0
          )";
Executor::doit($sqlOp);
```

### 2. Reporte de Cuentas por Pagar a Proveedores
```php
$sql = "SELECT 
            re.id,
            re.ref_id,
            re.person_id,
            p.name AS provider_name,
            p.company AS provider_company,
            re.total,
            re.paid,
            (re.total - re.paid) AS remaining_debt,
            re.ncf,
            re.invoiceNumber,
            re.invoiceDate,
            re.expirationDate,
            re.created_at,
            CASE WHEN (re.total - re.paid) <= 0 THEN 'Pagada' ELSE 'Pendiente' END AS debt_status
        FROM re
        LEFT JOIN person p ON re.person_id = p.id
        WHERE re.status = 1
          AND re.paid = 0
        ORDER BY re.created_at DESC";
$res = Executor::doit($sql);
```
