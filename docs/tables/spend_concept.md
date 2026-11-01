# 💸 Tablas `spend` y `concept` — Gastos Operativos y Conceptos

Las tablas `spend` y `concept` permiten gestionar los gastos operativos o gastos menores del negocio (pago de electricidad, alquiler, combustible, papelería, dieta, etc.) junto con su comprobante fiscal (NCF) para reportes de compras y gastos (606).

---

## 📋 1. Tabla `concept` (Catálogo de Conceptos de Gasto)

| Campo | Tipo | Nulo | Por Defecto | Descripción y Uso |
| :--- | :--- | :--- | :--- | :--- |
| **`id`** | `INT` (PK, AI) | No | `AUTO_INCREMENT` | Identificador único del concepto. |
| **`description`** | `VARCHAR(150)` | No | — | Nombre del concepto (ej. `'Energía Eléctrica'`, `'Combustible'`, `'Mantenimiento'`). |
| **`status`** | `INT` | No | `1` | `1` = Activo, `2` = Inactivo / Eliminado. |
| **`user_id`** | `INT` | No | `user_id` | Usuario que creó el concepto. |
| **`created_at`** | `DATETIME` | No | `NOW()` | Fecha de creación. |

---

## 📋 2. Tabla `spend` (Registro de Gastos)

| Campo | Tipo | Nulo | Por Defecto | Descripción y Uso |
| :--- | :--- | :--- | :--- | :--- |
| **`id`** | `INT` (PK, AI) | No | `AUTO_INCREMENT` | Identificador único del gasto. |
| **`concept_id`** | `INT` | No | — | ID del concepto de gasto (`concept.id`). |
| **`amount`** | `DECIMAL(12,2)` | No | `0.00` | Monto total del gasto. |
| **`subtotal`** | `DECIMAL(12,2)` | Sí | `NULL` | Subtotal antes de impuestos (opcional). |
| **`itbis`** | `DECIMAL(12,2)` | Sí | `NULL` | Monto de ITBIS facturado en el gasto (`NULL` si no aplica). |
| **`bill_id`** | `INT` | Sí | `0` | Número de factura o recibo del proveedor. |
| **`ncf`** | `VARCHAR(30)` | Sí | `''` | NCF del comprobante fiscal del gasto. |
| **`rnc`** | `VARCHAR(30)` | Sí | `''` | RNC o Cédula del comercio/proveedor donde se realizó el gasto. |
| **`provider`** | `VARCHAR(150)` | Sí | `''` | Nombre del comercio o proveedor del gasto. |
| **`date_at`** | `DATETIME / DATE` | No | — | Fecha en que se incurrió en el gasto. |
| **`observations`** | `TEXT` | Sí | `''` | Detalle o justificación del gasto. |
| **`box_id`** | `INT` | Sí | `NULL` | ID del cierre de caja asociado (`NULL` si no está asignado). |
| **`category_id`** | `INT` | Sí | `NULL` | Categoría secundaria opcional. |
| **`image`** | `VARCHAR(255)` | Sí | `NULL` | Foto o escaneo del recibo/factura de gasto. |
| **`dataJSON`** | `JSON / TEXT` | Sí | `NULL` | Metadatos adicionales en formato JSON. |
| **`status`** | `INT` | No | `1` | `1` = Activo, `2` = Anulado/Eliminado. |
| **`user_id`** | `INT` | No | `user_id` | Usuario que registró el gasto. |
| **`created_at`** | `DATETIME` | No | `NOW()` | Fecha y hora de creación del registro. |

---

## 🛠️ Ejemplos de Uso con `Executor::doit($sql)`

### 1. Crear un Concepto de Gasto y Registrar un Gasto
```php
$uid = intval(user_id);

// Crear concepto
$resConcept = Executor::doit("INSERT INTO concept (description, status, user_id, created_at) VALUES ('Combustible y Transporte', 1, $uid, NOW())");
$conceptId = intval($resConcept[1]);

// Registrar gasto bajo ese concepto
$sqlSpend = "INSERT INTO spend (
                 concept_id, amount, bill_id, ncf, date_at,
                 observations, status, user_id, created_at,
                 itbis, rnc, provider
             ) VALUES (
                 '$conceptId', 3500.00, '4021', 'B0100008812', CURDATE(),
                 'Combustible flota de entrega', 1, $uid, NOW(),
                 0.00, '101000555', 'Estación Shell Central'
             )";
Executor::doit($sqlSpend);
```

### 2. Consultar Gastos por Rango de Fechas con Descripción del Concepto
```php
$desde = '2026-10-01';
$hasta = '2026-10-31';

$sql = "SELECT spend.*, c.description AS concept_name
        FROM spend
        LEFT JOIN concept c ON spend.concept_id = c.id
        WHERE spend.status = 1
          AND DATE(spend.date_at) BETWEEN '$desde' AND '$hasta'
        ORDER BY spend.date_at DESC";
$res = Executor::doit($sql);
```
