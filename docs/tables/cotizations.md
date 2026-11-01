# 📝 Tabla `cotizations` — Cotizaciones y Presupuestos

La tabla `cotizations` almacena las cotizaciones o presupuestos enviados a los clientes.

### Relación con `operation`:
Los productos o servicios de una cotización se guardan en la tabla [`operation`](./operation.md) enlazados mediante `cotizations.ref_id = operation.sell_id`, pero marcando **`operation.is_draft = 1`** para que **no descuenten stock** del inventario mientras sean solo una cotización.

---

## 📋 Estructura de Columnas

| Campo | Tipo | Nulo | Por Defecto | Descripción y Uso |
| :--- | :--- | :--- | :--- | :--- |
| **`id`** | `INT` (PK, AI) | No | `AUTO_INCREMENT` | Identificador único de la cotización. |
| **`ref_id`** | `INT / VARCHAR` | No | — | Código/ID de referencia que enlaza con `operation.sell_id` (donde `is_draft = 1`). |
| **`person_id`** | `INT` | No | — | ID del cliente (`person.id`). |
| **`subtotal`** | `DECIMAL(12,2)` | No | `0.00` | Subtotal antes de impuestos y descuentos. |
| **`discount`** | `DECIMAL(12,2)` | No | `0.00` | Monto de descuento ofrecido. |
| **`taxes`** | `DECIMAL(12,2)` | No | `0.00` | Total de impuestos (ITBIS) calculados. |
| **`total`** | `DECIMAL(12,2)` | No | `0.00` | Monto total final cotizado: `(subtotal + taxes) - discount`. |
| **`note`** | `TEXT` | Sí | `''` | Condiciones comerciales, validez de la oferta o notas al cliente. |
| **`dataJSON`** | `JSON / TEXT` | Sí | `NULL` | Metadatos adicionales en formato JSON. |
| **`status`** | `INT` | No | `1` | `1` = Activa, `0` o `2` = Inactiva/Archivada. |
| **`user_id`** | `INT` | No | `user_id` | Usuario que elaboró la cotización. |
| **`created_at`** | `DATETIME` | No | `NOW()` | Fecha y hora de creación/actualización. |

---

## 🛠️ Ejemplos de Uso con `Executor::doit($sql)`

### 1. Crear una Cotización Completa con sus Ítems
```php
$uid = intval(user_id);
$ref_id = time(); // Referencia de enlace con `operation`

// 1. Insertar cabecera en `cotizations`
$sqlCot = "INSERT INTO cotizations (ref_id, person_id, subtotal, discount, taxes, total, status, user_id, created_at, note)
           VALUES ($ref_id, 5, 10000.00, 500.00, 1710.00, 11210.00, 1, $uid, NOW(), 'Válida por 15 días')";
$resCot = Executor::doit($sqlCot);
$cotizationId = intval($resCot[1]);

// 2. Insertar ítems en `operation` con is_draft = 1
$sqlOp = "INSERT INTO operation (
              price_in, min_price, max_price, price_out, is_draft, stock_id,
              product_id, q, operation_type_id, sell_id, user_id, created_at,
              itbis, name, idStore
          ) VALUES (
              6500.00, 0, 0, 10000.00, 1, 0,
              '8', '1', 2, '$ref_id', $uid, NOW(),
              'on', 'Impresora Térmica Industrial', 0
          )";
Executor::doit($sqlOp);
```

### 2. Listar Cotizaciones Activas con Nombre del Cliente
```php
$sql = "SELECT cotizations.*, person.name, person.lastname
        FROM cotizations
        INNER JOIN person ON cotizations.person_id = person.id
        WHERE cotizations.status = 1
        ORDER BY cotizations.created_at DESC";
$res = Executor::doit($sql);
```
