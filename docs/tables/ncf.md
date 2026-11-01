# 🧾 Tablas `control_ncf` y `ncf_detail` — Comprobantes Fiscales (NCF / e-CF)

Estas dos tablas administran las secuencias de Números de Comprobantes Fiscales (NCF tradicionales serie `B` y Comprobantes Fiscales Electrónicos `e-CF` serie `E`) y su vinculación con cada factura emitida en [`sell`](./sell.md).

---

## 📋 1. Tabla `control_ncf` (Secuencias y Rangos Autorizados)

Almacena los lotes de numeración autorizados por la DGII y su contador actual (`secuenciaactual`).

| Campo | Tipo | Nulo | Por Defecto | Descripción y Uso |
| :--- | :--- | :--- | :--- | :--- |
| **`id`** | `INT` (PK, AI) | No | `AUTO_INCREMENT` | Identificador del lote de secuencia NCF. |
| **`tipodoc`** | `INT / VARCHAR` | No | — | ID del tipo de comprobante (enlaza con `ncf_type.id`, donde `ncf_type.code` es `'B01'`, `'B02'`, `'B14'`, `'B15'`, `'E31'`, `'E32'`, etc.). |
| **`tipo`** | `INT / VARCHAR` | No | — | ID del tipo de cliente asociado (`client_type.id`). |
| **`fecinivig`** | `DATE` | No | — | Fecha de inicio de vigencia del rango autorizado. |
| **`fecfinvig`** | `DATE` | No | — | Fecha de vencimiento de la secuencia fiscal. |
| **`secuenciaini`** | `INT` | No | — | Número inicial autorizado del rango. |
| **`secuenciafin`** | `INT` | No | — | Número límite final autorizado del rango. |
| **`secuenciaactual`** | `INT` | No | — | Próximo correlativo disponible para emitir. Se incrementa en `+1` tras emitir cada comprobante. |
| **`dataJSON`** | `JSON / TEXT` | Sí | `NULL` | Metadatos adicionales en JSON. |
| **`status`** | `INT` | No | `1` | `1` = Activo, `0` o `2` = Inactivo/Agotado. |
| **`user_id`** | `INT` | No | `user_id` | Usuario que configuró el rango. |
| **`created_at`** | `DATETIME` | No | `NOW()` | Fecha de registro. |

---

## 📋 2. Tabla `ncf_detail` (Comprobantes Emitidos por Venta)

Guarda la trazabilidad fiscal de cada factura emitida con comprobante.

| Campo | Tipo | Nulo | Por Defecto | Descripción y Uso |
| :--- | :--- | :--- | :--- | :--- |
| **`id`** | `INT` (PK, AI) | No | `AUTO_INCREMENT` | Identificador único del detalle NCF. |
| **`tipodoc`** | `VARCHAR(10)` | No | — | Tipo de comprobante emitido. |
| **`tipo`** | `VARCHAR(10)` | No | — | Tipo de cliente al que se emitió. |
| **`ncf`** | `VARCHAR(30)` | No | — | Número de NCF completo (ej. `'B0100000025'`). |
| **`sell_id`** | `INT` | No | — | ID de la factura en la tabla `sell` (`sell.id`). |
| **`fecfinvig`** | `DATE` | Sí | `NULL` | Fecha de vencimiento del comprobante impresa en la factura. |
| **`status`** | `INT` | No | `1` | `1` = Activo. |
| **`user_id`** | `INT` | No | `user_id` | Usuario que emitió el comprobante. |
| **`created_at`** | `DATETIME` | No | `NOW()` | Fecha y hora de emisión. |

---

## 🛠️ Ejemplos de Uso con `Executor::doit($sql)`

### 1. Obtener un NCF Disponible, Asignarlo a una Venta e Incrementar la Secuencia
```php
$uid = intval(user_id);
$tipodoc = 1; // Ej. 1 = Crédito Fiscal (B01)
$hoy = date('Y-m-d');

// 1. Buscar secuencia activa y vigente
$sqlGetNcf = "SELECT a.*, nt.code
              FROM control_ncf a
              INNER JOIN ncf_type nt ON a.tipodoc = nt.id
              WHERE a.tipodoc = $tipodoc
                AND '$hoy' >= a.fecinivig
                AND '$hoy' <= a.fecfinvig
                AND a.status = 1
                AND a.secuenciaactual <= a.secuenciafin
              LIMIT 1";
$resNcf = Executor::doit($sqlGetNcf);
$lote = ($resNcf && $resNcf[0]) ? $resNcf[0]->fetch_assoc() : null;

if ($lote) {
    // Formatear correlativo (8 dígitos para serie B, 10 dígitos para serie E)
    $padLength = (strpos($lote['code'], 'E') === 0) ? 10 : 8;
    $ncfCompleto = $lote['code'] . str_pad((string)$lote['secuenciaactual'], $padLength, '0', STR_PAD_LEFT);

    // 2. Registrar en ncf_detail vinculado a $sell_id
    $sqlDetail = "INSERT INTO ncf_detail (tipodoc, tipo, ncf, sell_id, status, user_id, created_at, fecfinvig)
                  VALUES ('{$lote['tipodoc']}', '{$lote['tipo']}', '$ncfCompleto', $sell_id, 1, $uid, NOW(), '{$lote['fecfinvig']}')";
    Executor::doit($sqlDetail);

    // 3. Incrementar secuenciaactual en control_ncf
    $nuevaSecuencia = intval($lote['secuenciaactual']) + 1;
    Executor::doit("UPDATE control_ncf SET secuenciaactual = $nuevaSecuencia WHERE id = " . intval($lote['id']));
}
```

### 2. Consulta para Reporte Fiscal 607 (Ventas con NCF)
```php
$desde = '2026-10-01';
$hasta = '2026-10-31';

$sql = "SELECT 
            sell.id,
            sell.person_id,
            person.no AS cedula_rnc,
            person.name,
            person.lastname,
            CASE 
                WHEN person.no REGEXP '^[0-9]+$' AND LENGTH(TRIM(person.no)) = 11 THEN '1'
                ELSE '2'
            END AS type_id,
            sell.subtotal,
            sell.taxes,
            sell.discount,
            sell.total,
            sell.payment_method,
             sell.created_at,
            ncf_detail.tipodoc AS ncf_detail_tipodoc,
            ncf_detail.ncf AS ncf_full_number
        FROM sell
        LEFT JOIN ncf_detail ON ncf_detail.sell_id = sell.id
        INNER JOIN person ON sell.person_id = person.id
        WHERE ncf_detail.id IS NOT NULL
          AND sell.status = 1
          AND DATE(sell.created_at) BETWEEN '$desde' AND '$hasta'
        ORDER BY sell.created_at DESC";
$res = Executor::doit($sql);
```
