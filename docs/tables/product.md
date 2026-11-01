# 🏷️ Tabla `product` — Catálogo de Productos Inventariables

La tabla `product` almacena la ficha maestra de todos los artículos físicos controlados por inventario. Para conocer la cantidad disponible (`q`) de un producto, se debe cruzar con la tabla [`operation`](./operation.md).

---

## 📋 Estructura de Columnas

| Campo | Tipo | Nulo | Por Defecto | Descripción y Uso |
| :--- | :--- | :--- | :--- | :--- |
| **`id`** | `INT` (PK, AI) | No | `AUTO_INCREMENT` | Identificador único del producto. |
| **`category_id`** | `INT` | No | — | ID de la categoría a la que pertenece (`category.id`). |
| **`barcode`** | `VARCHAR(100)` | No | — | Código de barras, SKU o referencia única del artículo. |
| **`name`** | `VARCHAR(255)` | No | — | Nombre comercial del producto. |
| **`description`** | `TEXT` | Sí | `''` | Descripción detallada o especificaciones técnicas. |
| **`price_in`** | `DECIMAL(12,2)` | No | `0.00` | Precio de costo unitario (compra). |
| **`min_price`** | `DECIMAL(12,2)` | No | `0.00` | Precio mínimo de venta autorizado para cajeros/vendedores. |
| **`max_price`** | `DECIMAL(12,2)` | No | `0.00` | Precio regular (máximo) de venta al público. |
| **`itbis`** | `DECIMAL(5,2)` | No | `0.00` | Impuesto aplicable al producto (ej. `18` o `0`). |
| **`plusIva`** | `TINYINT(1)` | No | `0` | Modo de cálculo del impuesto:<br>• `1` = Más ITBIS (el impuesto se suma sobre `max_price`).<br>• `0` = ITBIS incluido. |
| **`sell_negative`** | `TINYINT(1)` | No | `0` | • `1` = Permite facturar el producto aunque su stock sea `0` o negativo.<br>• `0` = Impide vender si no hay stock disponible. |
| **`quantityPerPackage`**| `DECIMAL(12,2)` | Sí | `0` | Cantidad de unidades que contiene un paquete/caja (si > 1). |
| **`warranty_at`** | `VARCHAR(100)` | Sí | `''` | Tiempo o fecha de cobertura de garantía (ej. `'12 meses'`). |
| **`warranty_file`** | `VARCHAR(255)` | Sí | `''` | Ruta de documento/plantilla de garantía. |
| **`observations`** | `TEXT` | Sí | `''` | Notas internas sobre el producto. |
| **`image`** | `VARCHAR(255)` | Sí | `''` | Nombre o URL de la imagen del producto. |
| **`dataJSON`** | `JSON / TEXT` | Sí | `NULL` | Atributos adicionales personalizados en formato JSON. |
| **`status`** | `INT` | No | `1` | Estado del producto:<br>• `1` = Activo<br>• `2` = Inactivo / Eliminado en papelera |
| **`user_id`** | `INT` | No | `user_id` | ID del usuario que creó o editó el producto. |
| **`created_at`** | `DATETIME` | No | `NOW()` | Fecha de registro del producto. |

---

## 🛠️ Ejemplos de Uso con `Executor::doit($sql)`

### 1. Crear un Nuevo Producto
```php
$uid = intval(user_id);
$sql = "INSERT INTO product (
            category_id, barcode, name, description,
            price_in, min_price, max_price, itbis,
            warranty_at, observations, status, user_id,
            created_at, sell_negative, plusIva, dataJSON, quantityPerPackage
        ) VALUES (
            1, '74600001', 'Monitor LED 24 Pulgadas', 'Full HD HDMI/VGA',
            5200.00, 6800.00, 7500.00, 18,
            '1 Año', '', 1, $uid,
            NOW(), 0, 1, NULL, 1
        )";
$res = Executor::doit($sql);
$newProductId = intval($res[1]);
```

### 2. Buscar Productos y Servicios Unificados (Usando `UNION ALL SELECT`)
> **Nota:** En `Executor::doit($sql)` siempre debe usarse `UNION ALL SELECT` (nunca `UNION SELECT` sin `ALL`).

```php
$q = addslashes($terminoBusqueda);
$sql = "SELECT * FROM (
            SELECT 'Producto' AS tipo, p.id, p.barcode, p.name, p.description,
                   p.price_in, p.min_price, p.max_price, p.itbis, p.plusIva,
                   p.category_id, p.sell_negative, c.description AS category_name
            FROM product p
            LEFT JOIN category c ON c.id = p.category_id
            WHERE p.status = 1 AND (p.name LIKE '%$q%' OR p.barcode LIKE '%$q%')

            UNION ALL

            SELECT 'Servicio' AS tipo, s.id, s.barcode, s.name, s.description,
                   0 AS price_in, s.price AS min_price, s.price AS max_price, s.itbis, s.plusIva,
                   s.category_id, 1 AS sell_negative, c.description AS category_name
            FROM services s
            LEFT JOIN category c ON c.id = s.category_id
            WHERE s.status = 1 AND (s.name LIKE '%$q%' OR s.barcode LIKE '%$q%')
        ) AS catalogo
        ORDER BY (barcode = '$q') DESC, name ASC
        LIMIT 50";

$res = Executor::doit($sql);
```

### 3. Detectar Productos con Bajo Stock (Alerta de Reabastecimiento)
```php
$minStock = 5;
$idStore = 0;

$sql = "SELECT 
            p.id,
            p.name,
            p.barcode,
            COALESCE(SUM(
                CASE 
                    WHEN o.operation_type_id = 1 THEN o.q 
                    WHEN o.operation_type_id = 2 THEN -o.q 
                    ELSE 0 
                END
            ), 0) AS quantity
        FROM product p
        LEFT JOIN operation o ON p.id = o.product_id AND o.idStore = $idStore AND o.status = 1 AND o.is_draft = 0
        WHERE p.status = 1
        GROUP BY p.id
        HAVING quantity <= $minStock
        ORDER BY quantity ASC";

$res = Executor::doit($sql);
```
