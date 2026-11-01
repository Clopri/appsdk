# 🗂️ Tabla `category` — Categorías de Productos y Servicios

La tabla `category` organiza los productos (`product`) y servicios (`services`) por familias o departamentos, e incluye un prefijo corto para codificación.

---

## 📋 Estructura de Columnas

| Campo | Tipo | Nulo | Por Defecto | Descripción y Uso |
| :--- | :--- | :--- | :--- | :--- |
| **`id`** | `INT` (PK, AI) | No | `AUTO_INCREMENT` | Identificador único de la categoría. |
| **`type`** | `INT` | No | `1` | Tipo de categoría:<br>• `1` = Categoría de Productos<br>• `2` = Categoría de Servicios |
| **`prefix`** | `VARCHAR(20)` | No | — | Prefijo corto único de la categoría (ej. `'ELEC'`, `'ACC'`, `'SRV'`). |
| **`description`** | `VARCHAR(150)` | No | — | Nombre visible de la categoría. |
| **`dataJSON`** | `JSON / TEXT` | Sí | `NULL` | Metadatos adicionales en formato JSON. |
| **`status`** | `INT` | No | `1` | `1` = Activa, `2` = Inactiva / Eliminada. |
| **`user_id`** | `INT` | No | `user_id` | Usuario que creó o modificó la categoría. |
| **`created_at`** | `DATETIME` | No | `NOW()` | Fecha de creación. |

---

## 🛠️ Ejemplos de Uso con `Executor::doit($sql)`

### 1. Crear una Categoría
```php
$uid = intval(user_id);
$sql = "INSERT INTO category (type, prefix, description, user_id, created_at)
        VALUES (1, 'LAP', 'Laptops y Portátiles', $uid, NOW())";
$res = Executor::doit($sql);
```

### 2. Listar Categorías Activas
```php
$sql = "SELECT category.*, status.description AS status_dsc
        FROM category
        LEFT JOIN status ON category.status = status.id
        WHERE category.status = 1
        ORDER BY category.description ASC";
$res = Executor::doit($sql);
```
