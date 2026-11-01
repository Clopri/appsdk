# 🏦 Tabla `cashdesk` — Turnos, Apertura y Cuadre de Caja

La tabla `cashdesk` controla la apertura, el seguimiento en tiempo real y el cierre de los turnos de caja por usuario (`user_id`).

### Regla de Cálculo del Turno de Caja:
Un turno está **abierto** mientras `close_time IS NULL`. Todas las transacciones realizadas por ese `user_id` entre `opening_time` y `close_time` (o `NOW()` si sigue abierto) pertenecen a ese turno:
1. **Ventas al contado**: `SELECT SUM(s.total) FROM sell s WHERE s.status = 1 AND s.cash >= s.total AND s.user_id = cd.user_id AND s.created_at >= cd.opening_time AND (cd.close_time IS NULL OR s.created_at <= cd.close_time)`
2. **Abonos recibidos**: `SELECT SUM(p.val) FROM payment p WHERE p.val > 0 AND p.user_id = cd.user_id AND p.created_at >= cd.opening_time AND (cd.close_time IS NULL OR p.created_at <= cd.close_time)`

---

## 📋 Estructura de Columnas

| Campo | Tipo | Nulo | Por Defecto | Descripción y Uso |
| :--- | :--- | :--- | :--- | :--- |
| **`id`** | `INT` (PK, AI) | No | `AUTO_INCREMENT` | Identificador único del turno de caja. |
| **`user_id`** | `INT` | No | — | ID del cajero/usuario propietario del turno (`user.id`). |
| **`opening_time`** | `DATETIME` | No | `NOW()` | Fecha y hora exacta en que se abrió la caja. |
| **`close_time`** | `DATETIME` | Sí | `NULL` | Fecha y hora de cierre (`NULL` indica que la caja sigue **abierta**). |
| **`start_amount`** | `DECIMAL(12,2)` | No | `0.00` | Fondo de efectivo inicial con el que empieza el cajero. |
| **`end_amount`** | `DECIMAL(12,2)` | No | `0.00` | Monto total facturado/cobrado acumulado al momento de cerrar la caja. |
| **`unbalanced`** | `DECIMAL / VARCHAR`| Sí | `0` | Descuadre o diferencia entre el efectivo físico contado y el esperado por el sistema. |
| **`note`** | `TEXT` | Sí | `''` | Notas o justificación del cierre/apertura de caja. |
| **`dataJSON`** | `JSON / TEXT` | Sí | `NULL` | Datos adicionales en JSON (ej. `$.legalTip` para propina legal acumulada). |

---

## 🛠️ Ejemplos de Uso con `Executor::doit($sql)`

### 1. Verificar si el Usuario Actual Tiene una Caja Abierta
```php
$uid = intval(user_id);
$res = Executor::doit("SELECT * FROM cashdesk WHERE user_id = $uid AND close_time IS NULL LIMIT 1");
$cajaAbierta = ($res && $res[0]) ? $res[0]->fetch_assoc() : null;
```

### 2. Abrir un Nuevo Turno de Caja
```php
$uid = intval(user_id);
$fondoInicial = 2000.00;
$sql = "INSERT INTO cashdesk (user_id, opening_time, close_time, start_amount, end_amount, unbalanced, note)
        VALUES ($uid, NOW(), NULL, '$fondoInicial', '0.00', '0', 'Apertura turno matutino')";
$res = Executor::doit($sql);
```

### 3. Consultar Turnos con Cálculo Automático de Ventas y Abonos
```php
$sql = "SELECT
            cd.*,
            JSON_UNQUOTE(JSON_EXTRACT(cd.dataJSON, '$.legalTip')) AS legalTip,
            COALESCE((
                SELECT SUM(s.total)
                FROM sell s
                WHERE s.status = 1
                  AND s.cash >= s.total
                  AND s.user_id = cd.user_id
                  AND s.created_at >= cd.opening_time
                  AND (cd.close_time IS NULL OR s.created_at <= cd.close_time)
            ), 0)
            +
            COALESCE((
                SELECT SUM(p.val)
                FROM payment p
                WHERE p.val > 0
                  AND p.user_id = cd.user_id
                  AND p.created_at >= cd.opening_time
                  AND (cd.close_time IS NULL OR p.created_at <= cd.close_time)
            ), 0) AS monto_acumulado,
            COALESCE((
                SELECT COUNT(1)
                FROM sell s2
                WHERE s2.status = 1
                  AND s2.user_id = cd.user_id
                  AND s2.created_at >= cd.opening_time
                  AND (cd.close_time IS NULL OR s2.created_at <= cd.close_time)
            ), 0) AS total_ventas
        FROM cashdesk cd
        ORDER BY cd.opening_time DESC
        LIMIT 20";
$res = Executor::doit($sql);
```

### 4. Cerrar la Caja
```php
$sql = "UPDATE cashdesk
        SET close_time = NOW(),
            end_amount = $montoAcumulado,
            note = 'Cierre conforme',
            unbalanced = 0
        WHERE id = $cashdeskId";
Executor::doit($sql);
```
