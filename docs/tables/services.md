# 🛠️ Tabla `services` — Catálogo de Servicios

La tabla `services` almacena los servicios intangibles que ofrece el negocio (ej. mano de obra, reparaciones, consultoría, instalaciones, envíos). A diferencia de `product`, los servicios no requieren existencia previa en almacén (`sell_negative` implícito en `1`).

---

## 📋 Estructura de Columnas

| Campo | Tipo | Nulo | Por Defecto | Descripción y Uso |
| :--- | :--- | :--- | :--- | :--- |
| **`id`** | `INT` (PK, AI) | No | `AUTO_INCREMENT` | Identificador único del servicio. |
| **`category_id`** | `INT` | No | — | ID de la categoría asociada (`category.id`). |
| **`barcode`** | `VARCHAR(100)` | No | — | Código interno o referencia del servicio. |
| **`name`** | `VARCHAR(255)` | No | — | Nombre del servicio. |
| **`description`** | `TEXT` | Sí | `''` | Descripción detallada de lo que incluye el servicio. |
| **`price`** | `DECIMAL(12,2)` | No | `0.00` | Precio estándar de venta del servicio. |
| **`price_in`** | `DECIMAL(12,2)` | Sí | `0.00` | Costo operativo estimado del servicio (opcional). |
| **`price_out`** | `DECIMAL(12,2)` | Sí | `0.00` | Precio de salida alternativo (opcional). |
| **`itbis`** | `DECIMAL(5,2)` | No | `0.00` | Porcentaje o indicador de ITBIS aplicable. |
| **`plusIva`** | `TINYINT(1)` | No | `0` | `1` = Sumar ITBIS al precio base, `0` = ITBIS incluido. |
| **`image`** | `VARCHAR(255)` | Sí | `''` | Imagen o ícono representativo del servicio. |
| **`dataJSON`** | `JSON / TEXT` | Sí | `NULL` | Metadatos adicionales en formato JSON. |
| **`status`** | `INT` | No | `1` | `1` = Activo, `2` = Inactivo / Eliminado. |
| **`user_id`** | `INT` | No | `user_id` | Usuario que registró el servicio. |
| **`created_at`** | `DATETIME` | No | `NOW()` | Fecha de creación. |

---

## 🛠️ Ejemplos de Uso con `Executor::doit($sql)`

### 1. Registrar un Nuevo Servicio
```php
$uid = intval(user_id);
$sql = "INSERT INTO services (category_id, barcode, name, description, price, itbis, status, user_id, created_at, plusIva)
        VALUES (2, 'SRV-001', 'Mantenimiento Preventivo', 'Limpieza física y optimización', 1500.00, 18, 1, $uid, NOW(), 1)";
$res = Executor::doit($sql);
```

### 2. Listar Todos los Servicios Activos con su Categoría
```php
$sql = "SELECT s.*, st.description AS status_dsc, c.description AS category
        FROM services s
        LEFT JOIN status st ON s.status = st.id
        INNER JOIN category c ON s.category_id = c.id
        WHERE s.status = 1
        ORDER BY s.id DESC";
$res = Executor::doit($sql);
```
