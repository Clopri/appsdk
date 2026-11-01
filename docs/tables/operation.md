# 📦 Tabla `operation` — Movimientos de Inventario y Detalle de Transacciones

La tabla `operation` es el corazón transaccional del inventario en Clopri. Cumple una doble función:
1. **Kardex / Libro Mayor de Inventario**: El stock actual de cualquier producto no se guarda como un número fijo en `product`, sino que se calcula sumando las entradas (`operation_type_id = 1`) y restando las salidas (`operation_type_id = 2`) registradas en `operation`.
2. **Detalle de Líneas (Ítems)**: Almacena los renglones de cada factura de venta (`sell`), compra a proveedor (`re`) y cotización (`cotizations`).

---

## 📋 Estructura de Columnas

| Campo | Tipo | Nulo | Por Defecto | Descripción y Uso |
| :--- | :--- | :--- | :--- | :--- |
| **`id`** | `INT` (PK, AI) | No | `AUTO_INCREMENT` | Identificador único del movimiento. |
| **`product_id`** | `INT` | No | — | ID del producto (`product.id`) o servicio (`services.id`). |
| **`name`** | `VARCHAR(255)` | Sí | `NULL` | Nombre descriptivo del producto o servicio al momento de la operación (permite conservar el histórico aunque el producto cambie de nombre). |
| **`q`** | `DECIMAL(12,2)` | No | `0.00` | Cantidad de unidades involucradas en el movimiento. |
| **`price_in`** | `DECIMAL(12,2)` | No | `0.00` | Precio de costo unitario del producto. Se usa para calcular la ganancia (`wealth`) de cada venta. |
| **`min_price`** | `DECIMAL(12,2)` | Sí | `0.00` | Precio mínimo de venta registrado (usado en entradas de almacén; `0` en cotizaciones). |
| **`max_price`** | `DECIMAL(12,2)` | Sí | `0.00` | Precio máximo/regular de venta registrado (usado en entradas de almacén; `0` en cotizaciones). |
| **`price_out`** | `DECIMAL(12,2)` | No | `0.00` | Precio unitario de venta al que se facturó o cotizó el artículo. |
| **`itbis`** | `VARCHAR(20)` | Sí | `''` | Indicador de impuesto en el ítem:<br>• `'on'` = El artículo grava ITBIS en la venta.<br>• `''` o `'0'` = Exento o sin ITBIS. |
| **`operation_type_id`** | `INT` | No | — | Tipo de movimiento de inventario:<br>• `1` = **ENTRADA** (Compra, reabastecimiento o ajuste positivo — **suma** al stock)<br>• `2` = **SALIDA** (Venta o ajuste negativo — **resta** al stock)<br>• `3` = ENTRADA-PENDIENTE<br>• `4` = SALIDA-PENDIENTE<br>• `5` = DEVOLUCIÓN<br>• `6` = TRASPASO ENTRE ALMACENES |
| **`sell_id`** | `VARCHAR(64)` | Sí | — | ID o referencia del documento padre:<br>• En ventas: `sell.id`<br>• En compras: `re.ref_id`<br>• En cotizaciones: `cotizations.ref_id` |
| **`is_sell`** | `TINYINT(1)` | Sí | `0` | `1` cuando la línea pertenece a una factura de venta (`sell`), `0` en compras, cotizaciones o ajustes. |
| **`is_draft`** | `TINYINT(1)` | No | `0` | • `0` = Operación confirmada (afecta el cálculo de stock real).<br>• `1` = Borrador / Cotización (**no** afecta el inventario). |
| **`stock_id`** | `INT` | No | `0` | ID de lote/stock (por defecto `0`). |
| **`idStore`** | `INT` | No | `0` | ID del almacén o sucursal donde ocurre el movimiento (`0` = Almacén Principal por defecto). |
| **`note`** | `TEXT` | Sí | `NULL` | Observación específica de la línea o movimiento. |
| **`packageCondition`** | `VARCHAR(100)` | Sí | `NULL` | Condición del empaque (recepción/logística). |
| **`expirationDate`** | `DATE` | Sí | `NULL` | Fecha de vencimiento del lote del producto. |
| **`destination`** | `VARCHAR(150)` | Sí | `NULL` | Destino del movimiento o despacho. |
| **`idStorefrom`** | `INT` | Sí | `NULL` | ID del almacén de origen (en traspasos `operation_type_id = 6`). |
| **`idStoreTo`** | `INT` | Sí | `NULL` | ID del almacén de destino (en traspasos `operation_type_id = 6`). |
| **`storageConditions`** | `VARCHAR(150)` | Sí | `NULL` | Condiciones de almacenamiento requeridas. |
| **`identifier`** | `VARCHAR(100)` | Sí | `NULL` | Código de lote, serie o identificador logístico. |
| **`storageTemperature`**| `VARCHAR(50)` | Sí | `NULL` | Temperatura de almacenamiento. |
| **`idStoreMovement`** | `INT` | Sí | `NULL` | ID de agrupación de movimiento de almacén. |
| **`UUID`** | `VARCHAR(64)` | Sí | `NULL` | Identificador único universal del movimiento. |
| **`dataJSON`** | `JSON / TEXT` | Sí | `NULL` | Metadatos adicionales en formato JSON. |
| **`status`** | `INT` | No | `1` | `1` = Activo (cuenta en el inventario), `0` = Cancelado/Anulado. |
| **`user_id`** | `INT` | No | `user_id` | ID del usuario que registró la operación. |
| **`created_at`** | `DATETIME` | No | `NOW()` | Fecha y hora del movimiento. |

---

## 🛠️ Ejemplos de Uso con `Executor::doit($sql)`

### 1. Registrar un Ítem de Venta (Salida de Inventario)
```php
$uid = intval(user_id);
$sql = "INSERT INTO operation (
            status, price_in, price_out, stock_id, product_id, q,
            operation_type_id, sell_id, is_sell, user_id, created_at,
            itbis, name, idStore
        ) VALUES (
            1, 450.00, 750.00, 0, '12', '2',
            2, 1050, 1, $uid, NOW(),
            'on', 'Teclado Mecánico RGB', 0
        )";
Executor::doit($sql);
```

### 2. Registrar un Ítem de Cotización (Sin Afectar Stock: `is_draft = 1`)
```php
$uid = intval(user_id);
$sql = "INSERT INTO operation (
            price_in, min_price, max_price, price_out, is_draft, stock_id,
            product_id, q, operation_type_id, sell_id, user_id, created_at,
            itbis, name, idStore
        ) VALUES (
            450.00, 0, 0, 750.00, 1, 0,
            '12', '2', 2, '1730419200', $uid, NOW(),
            'on', 'Teclado Mecánico RGB', 0
        )";
Executor::doit($sql);
```

### 3. Obtener todos los Productos de una Factura
```php
$sell_id = intval($idFactura);
$sql = "SELECT * FROM operation WHERE sell_id = $sell_id AND is_sell = 1 ORDER BY id ASC";
$res = Executor::doit($sql);
```

### 4. Calcular Existencia (Stock Actual) de Todos los Productos en un Almacén
```php
$idStore = 0; // Almacén principal
$sql = "SELECT 
            p.id, 
            p.name, 
            p.barcode, 
            p.price_in, 
            p.min_price, 
            p.max_price, 
            c.description AS category,
            COALESCE(o.stock_total, 0) AS q
        FROM product p
        INNER JOIN category c ON p.category_id = c.id
        LEFT JOIN (
            SELECT 
                product_id, 
                SUM(CASE 
                    WHEN operation_type_id = 1 THEN q 
                    WHEN operation_type_id = 2 THEN -q 
                    ELSE 0 
                END) AS stock_total
            FROM operation 
            WHERE idStore = $idStore 
              AND status = 1 
              AND is_draft = 0 
              AND operation_type_id IN (1, 2)
            GROUP BY product_id
        ) o ON p.id = o.product_id
        WHERE p.status = 1
        ORDER BY p.name ASC";

$res = Executor::doit($sql);
```
