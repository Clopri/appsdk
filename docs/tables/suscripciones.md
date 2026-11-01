# 🔄 Tabla `suscripciones` — Facturación Recurrente y Membresías

La tabla `suscripciones` gestiona planes de cobro recurrente, igualas, membresías o contratos periódicos asociados a clientes (`person`).

---

## 📋 Estructura de Columnas

| Campo | Tipo | Nulo | Por Defecto | Descripción y Uso |
| :--- | :--- | :--- | :--- | :--- |
| **`id`** | `INT` (PK, AI) | No | `AUTO_INCREMENT` | Identificador único de la suscripción. |
| **`client_id`** | `INT` | No | — | ID del cliente en la tabla `person` (`person.id`). |
| **`product_id`** | `INT` | Sí | `NULL` | ID del producto o servicio asociado (`product.id` o `services.id`). |
| **`product_name`** | `VARCHAR(255)` | Sí | `NULL` | Nombre descriptivo del plan o servicio suscrito. |
| **`price`** | `DECIMAL(12,2)` | No | `0.00` | Monto a cobrar en cada ciclo de facturación. |
| **`frequency`** | `VARCHAR(30)` | No | `'mensual'` | Frecuencia del ciclo:<br>• `'diario'`<br>• `'semanal'`<br>• `'quincenal'`<br>• `'mensual'`<br>• `'trimestral'`<br>• `'cuatrimestral'`<br>• `'semestral'`<br>• `'anual'` |
| **`start_date`** | `DATE` | No | `CURDATE()` | Fecha de inicio de la suscripción. |
| **`end_date`** | `DATE` | Sí | `NULL` | Fecha de finalización del contrato (`NULL` si es indefinida). |
| **`next_billing_date`** | `DATE` | Sí | `NULL` | Fecha programada del próximo cobro/emisión de factura. |
| **`last_billing_date`** | `DATE` | Sí | `NULL` | Fecha en la que se emitió el último cobro. |
| **`status`** | `INT` | No | `1` | Estado de la suscripción:<br>• `1` = **Activa**<br>• `2` = **Inactiva / Pausada**<br>• `3` = **Cancelada** |
| **`dataJSON`** | `JSON / TEXT` | Sí | `NULL` | Configuración extra en formato JSON. |
| **`UUID`** | `VARCHAR(64)` | No | — | Identificador único universal de la suscripción. |
| **`user_id`** | `INT` | No | `user_id` | Usuario que creó el registro. |
| **`created_at`** | `DATETIME` | No | `NOW()` | Fecha y hora de creación. |

---

## 🛠️ Ejemplos de Uso con `Executor::doit($sql)`

### 1. Crear una Suscripción Mensual
```php
$uid = intval(user_id);
$uuid = uniqid('sub_', true);

$sql = "INSERT INTO suscripciones (
            client_id, product_id, product_name, price, frequency,
            start_date, end_date, next_billing_date, last_billing_date,
            status, dataJSON, user_id, created_at, UUID
        ) VALUES (
            5, 1, 'Plan Soporte Empresarial Mensual', 4500.00, 'mensual',
            CURDATE(), NULL, DATE_ADD(CURDATE(), INTERVAL 1 MONTH), NULL,
            1, NULL, $uid, NOW(), '$uuid'
        )";
$res = Executor::doit($sql);
```

### 2. Obtener Suscripciones Vencidas o Pendientes de Cobro Hoy
```php
$sql = "SELECT s.*, p.name AS client_name, p.lastname AS client_lastname, p.no AS client_no
        FROM suscripciones s
        LEFT JOIN person p ON s.client_id = p.id
        WHERE s.status = 1
          AND s.next_billing_date <= CURDATE()
        ORDER BY s.next_billing_date ASC";
$res = Executor::doit($sql);
```

### 3. Renovar / Emitir Cobro Avanzando Automáticamente `next_billing_date`
```php
$sql = "UPDATE suscripciones
        SET last_billing_date = CURDATE(),
            next_billing_date = CASE LOWER(frequency)
                WHEN 'diario'        THEN DATE_ADD(COALESCE(next_billing_date, CURDATE()), INTERVAL 1 DAY)
                WHEN 'semanal'       THEN DATE_ADD(COALESCE(next_billing_date, CURDATE()), INTERVAL 1 WEEK)
                WHEN 'quincenal'     THEN DATE_ADD(COALESCE(next_billing_date, CURDATE()), INTERVAL 15 DAY)
                WHEN 'mensual'       THEN DATE_ADD(COALESCE(next_billing_date, CURDATE()), INTERVAL 1 MONTH)
                WHEN 'trimestral'    THEN DATE_ADD(COALESCE(next_billing_date, CURDATE()), INTERVAL 3 MONTH)
                WHEN 'cuatrimestral' THEN DATE_ADD(COALESCE(next_billing_date, CURDATE()), INTERVAL 4 MONTH)
                WHEN 'semestral'     THEN DATE_ADD(COALESCE(next_billing_date, CURDATE()), INTERVAL 6 MONTH)
                WHEN 'anual'         THEN DATE_ADD(COALESCE(next_billing_date, CURDATE()), INTERVAL 1 YEAR)
                ELSE DATE_ADD(COALESCE(next_billing_date, CURDATE()), INTERVAL 1 MONTH)
            END
        WHERE id = $suscripcionId";
Executor::doit($sql);
```
