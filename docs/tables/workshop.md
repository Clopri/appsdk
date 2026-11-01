# 🔧 Tabla `workshop` — Órdenes de Taller y Servicio Técnico

La tabla `workshop` gestiona la recepción, diagnóstico, reparación y entrega de equipos en centros de servicio técnico o talleres.

---

## 📋 Estructura de Columnas

| Campo | Tipo | Nulo | Por Defecto | Descripción y Uso |
| :--- | :--- | :--- | :--- | :--- |
| **`id`** | `INT` (PK, AI) | No | `AUTO_INCREMENT` | Número único de orden de taller. |
| **`client_id`** | `INT` | No | — | ID del cliente propietario del equipo (`person.id`). |
| **`description`** | `TEXT` | No | — | Descripción del equipo o falla reportada por el cliente. |
| **`brand`** | `VARCHAR(100)` | Sí | `''` | Marca del equipo (ej. `'Apple'`, `'Samsung'`, `'HP'`). |
| **`model`** | `VARCHAR(100)` | Sí | `''` | Modelo del equipo (ej. `'MacBook Pro M3'`, `'LaserJet 400'`). |
| **`serie`** | `VARCHAR(100)` | Sí | `''` | Número de serie o IMEI del equipo. |
| **`date_in`** | `DATETIME / DATE` | No | — | Fecha de ingreso al taller. |
| **`date_out`** | `DATETIME / DATE` | Sí | `NULL` | Fecha estimada o real de salida/entrega. |
| **`returned`** | `VARCHAR / INT` | Sí | `'0'` | Indicador de si el equipo ya fue entregado/devuelto al cliente. |
| **`observation`** | `TEXT` | Sí | `''` | Diagnóstico técnico, repuestos requeridos y notas internas. |
| **`dataJSON`** | `JSON / TEXT` | Sí | `NULL` | Metadatos adicionales (accesorios dejados, contraseña de desbloqueo, fotos, costo estimado). |
| **`status`** | `INT` | No | `1` | Estado de la orden (enlaza con `status.id`; `1` = Activo). |
| **`user_id`** | `INT` | No | `user_id` | Técnico o usuario que recibió/actualizó la orden. |
| **`created_at`** | `DATETIME` | No | `NOW()` | Fecha y hora de creación del registro. |

---

## 🛠️ Ejemplos de Uso con `Executor::doit($sql)`

### 1. Crear una Nueva Orden de Taller
```php
$uid = intval(user_id);
$sql = "INSERT INTO workshop (
            description, brand, model, serie, date_in, date_out,
            returned, observation, status, user_id, created_at, client_id, dataJSON
        ) VALUES (
            'No enciende tras caída de tensión', 'Dell', 'OptiPlex 7090', 'SN-99881122',
            CURDATE(), DATE_ADD(CURDATE(), INTERVAL 3 DAY),
            '0', 'Revisar fuente de poder y placa madre', 1, $uid, NOW(), 5, NULL
        )";
$res = Executor::doit($sql);
$ordenId = intval($res[1]);
```

### 2. Listar Órdenes de Taller con Nombre y Teléfono del Cliente
```php
$sql = "SELECT workshop.*, p.name AS client_name, p.lastname AS client_lastname, p.phone, status.description AS status_dsc
        FROM workshop
        LEFT JOIN status ON workshop.status = status.id
        LEFT JOIN person p ON p.id = workshop.client_id
        WHERE workshop.status = 1
        ORDER BY workshop.id DESC";
$res = Executor::doit($sql);
```
