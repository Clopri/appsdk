# 🧠 Clopri — Esquema SQL y Rieles de Base de Datos para IA (SDK v3.9.0)

> **PROPÓSITO DE ESTE ARCHIVO:** Este documento está optimizado específicamente para **Agentes de Inteligencia Artificial y LLMs**. Contiene el mapa relacional completo, las firmas exactas de columnas y las reglas de integridad contable para construir consultas con `Executor::doit($sql)` sin errores de columna ni violaciones de seguridad.

---

## ⚡ 1. Reglas Críticas de Ejecución SQL (`Executor::doit($sql)`)

1. **Firma y Retorno**:
   - `$query = Executor::doit($sql);`
   - `$query[0]`: Objeto `mysqli_result` en consultas `SELECT` (iterar con `while ($row = $query[0]->fetch_assoc())`) o `bool` en escrituras.
   - `$query[1]`: `int` con el ID autogenerado (`insert_id`) en consultas `INSERT`.
2. **Restricciones del Firewall SQL**:
   - **1 sola sentencia por llamada**: Nunca concatenes múltiples consultas separadas por `;`.
   - **Prohibido `WHERE 1=1` u `OR 1=1`**: Nunca uses comparaciones numéricas tautológicas. Para filtros dinámicos usa un arreglo `$conds = ["status = 1"]` y `WHERE " . implode(" AND ", $conds)`.
   - **Prohibido `UNION SELECT`**: Usa siempre **`UNION ALL SELECT`** cuando necesites combinar resultados (ej. `product` + `services`).
   - **Palabras bloqueadas**: `SLEEP(`, `BENCHMARK(`, `LOAD_FILE(`, `INTO OUTFILE`, `DROP TABLE`, `DROP DATABASE`, `TRUNCATE TABLE`, `INFORMATION_SCHEMA`, `GRANT ALL`, `EXEC(`.
3. **Autenticación de Usuario**:
   - La constante global `user_id` contiene el ID del usuario logueado o `null` si no hay sesión.
   - Valida sesión con: `if (!defined('user_id') || is_null(user_id)) { ... }`.
   - Usa `$uid = intval(user_id);` para poblar la columna `user_id` en todas las tablas.

---

## 🗺️ 2. Diccionario Compacto de Tablas y Columnas

### `sell` (Cabecera de Ventas / Facturas)
- **PK**: `id` (INT, AI)
- **Columnas**: `ref_id`, `person_id` (FK -> `person.id`), `p_id` (`1`=Contado, `2`=Crédito), `d_id` (`1`=Entregado, `2`=Pendiente, `3`=Cancelado), `box_id` (FK -> `cashdesk.id`, NULL), `subtotal` (DECIMAL), `taxes` (DECIMAL), `discount` (DECIMAL), `total` (DECIMAL: `subtotal + taxes - discount`), `cash` (DECIMAL: si `p_id=1` entonces `cash >= total`; si `p_id=2` entonces `cash < total`), `iva` (DECIMAL: ej. `18`), `wealth` (DECIMAL: ganancia `SUM(q * (price_out - price_in))`), `ncf` (VARCHAR), `payment_method` (VARCHAR: `'Efectivo'`, `'Transferencia'`, `'Tarjeta'`), `banktransferID` (INT: `0`), `creditExpirationDate` (DATETIME, NULL), `note` (TEXT), `stock_from_id`, `stock_to_id`, `UUID` (VARCHAR), `dataJSON` (JSON: `$.legalTip`, `$.ecf.legalStatus`, `$.ecf.status`), `status` (`1`=Activa, `0`/`2`=Anulada), `user_id` (INT), `client_info` (VARCHAR), `created_at` (DATETIME).

### `operation` (Detalle de Ítems y Movimientos de Inventario)
- **PK**: `id` (INT, AI)
- **Columnas**: `product_id` (FK -> `product.id` o `services.id`), `name` (VARCHAR), `q` (DECIMAL: cantidad), `price_in` (DECIMAL: costo), `min_price` (DECIMAL), `max_price` (DECIMAL), `price_out` (DECIMAL: precio venta), `itbis` (VARCHAR: `'on'` si grava ITBIS, `''` o `'0'` si no), `operation_type_id` (`1`=ENTRADA [suma stock], `2`=SALIDA [resta stock], `3`=ENTRADA-PENDIENTE, `4`=SALIDA-PENDIENTE, `5`=DEVOLUCIÓN, `6`=TRASPASO), `sell_id` (VARCHAR/INT: enlaza con `sell.id` en ventas, `re.ref_id` en compras, `cotizations.ref_id` en cotizaciones), `is_sell` (`1` en ventas, `0` en otros), `is_draft` (`0`=Afecta stock, `1`=Borrador/Cotización no afecta stock), `stock_id` (`0`), `idStore` (INT: `0` almacén por defecto), `note`, `packageCondition`, `expirationDate`, `destination`, `idStorefrom`, `idStoreTo`, `storageConditions`, `identifier`, `storageTemperature`, `idStoreMovement`, `UUID`, `dataJSON`, `status` (`1`=Activo, `0`=Cancelado), `user_id` (INT), `created_at` (DATETIME).

### `payment` (Cuentas por Cobrar: Deudas y Abonos)
- **PK**: `id` (INT, AI)
- **Columnas**: `person_id` (FK -> `person.id`), `sell_id` (FK -> `sell.id`), `payment_type_id` (`1`=Cargo/Deuda inicial de crédito, `2`=Abono/Pago recibido), `val` (DECIMAL: **NEGATIVO** `-monto` cuando `payment_type_id = 1`; **POSITIVO** `+monto` cuando `payment_type_id = 2`), `saldada` (`0`=Pendiente, `1`=Saldada), `payment_method` (VARCHAR), `UUID` (VARCHAR: agrupa varios pagos en un mismo recibo), `dataJSON`, `status` (`1`=Activo), `user_id` (INT), `created_at` (DATETIME).

### `person` (Clientes, Proveedores y Empleados)
- **PK**: `id` (INT, AI)
- **Columnas**: `tipo` (`1`=Empleado/Especialista, `2`=Proveedor, `3`=Cliente), `client_type_id` (FK -> `client_type.id`: `1`=Empresa/Crédito Fiscal, `2`=Gubernamental, `3`=Persona/Consumo, `4`=Especial, `5`=Exento), `no` (VARCHAR: RNC o Cédula), `name` (VARCHAR), `lastname` (VARCHAR), `company` (VARCHAR), `address` (VARCHAR), `email` (VARCHAR), `phone` (VARCHAR), `cell` (VARCHAR), `image` (VARCHAR), `gender`, `birthdate`, `hiredate`, `marital_status`, `nationality`, `social_security_number`, `position`, `department`, `contract_type`, `termination_date`, `base_salary`, `hourly_salary`, `observations`, `dataJSON`, `status` (`1`=Activo, `2`=Inactivo), `user_id` (INT), `created_at` (DATETIME).

### `product` (Productos Físicos)
- **PK**: `id` (INT, AI)
- **Columnas**: `category_id` (FK -> `category.id`), `barcode` (VARCHAR: único), `name` (VARCHAR), `description` (TEXT), `price_in` (DECIMAL: costo), `min_price` (DECIMAL), `max_price` (DECIMAL: precio venta), `itbis` (DECIMAL), `plusIva` (`1`=Más ITBIS, `0`=ITBIS incluido), `sell_negative` (`1`=Permite vender sin stock, `0`=No permite), `quantityPerPackage` (DECIMAL), `warranty_at`, `warranty_file`, `observations`, `image`, `dataJSON`, `status` (`1`=Activo, `2`=Eliminado), `user_id` (INT), `created_at` (DATETIME).

### `services` (Servicios Intangibles)
- **PK**: `id` (INT, AI)
- **Columnas**: `category_id` (FK -> `category.id`), `barcode` (VARCHAR), `name` (VARCHAR), `description` (TEXT), `price` (DECIMAL), `price_in` (DECIMAL), `price_out` (DECIMAL), `itbis` (DECIMAL), `plusIva` (`1` o `0`), `image`, `dataJSON`, `status` (`1`=Activo, `2`=Eliminado), `user_id` (INT), `created_at` (DATETIME).

### `category` (Categorías)
- **PK**: `id` (INT, AI)
- **Columnas**: `type` (`1`=Producto, `2`=Servicio), `prefix` (VARCHAR), `description` (VARCHAR), `dataJSON`, `status` (`1`=Activa, `2`=Inactiva), `user_id` (INT), `created_at` (DATETIME).

### `cashdesk` (Turnos y Cuadre de Caja)
- **PK**: `id` (INT, AI)
- **Columnas**: `user_id` (FK -> `user.id`), `opening_time` (DATETIME), `close_time` (DATETIME: `NULL` si el turno sigue abierto), `start_amount` (DECIMAL), `end_amount` (DECIMAL), `unbalanced` (VARCHAR/DECIMAL), `note` (TEXT), `dataJSON` (JSON: `$.legalTip`).

### `cotizations` (Cotizaciones)
- **PK**: `id` (INT, AI)
- **Columnas**: `ref_id` (INT/VARCHAR: enlaza con `operation.sell_id` donde `is_draft = 1`), `person_id` (FK -> `person.id`), `subtotal` (DECIMAL), `discount` (DECIMAL), `taxes` (DECIMAL), `total` (DECIMAL), `note` (TEXT), `dataJSON`, `status` (`1`=Activa), `user_id` (INT), `created_at` (DATETIME).

### `re` (Compras / Reabastecimiento y Cuentas por Pagar)
- **PK**: `id` (INT, AI)
- **Columnas**: `ref_id` (INT/VARCHAR: enlaza con `operation.sell_id` donde `operation_type_id = 1`), `bill_id` (INT), `person_id` (FK -> `person.id` donde `tipo = 2`), `p_id` (`1`=Contado, `2`=Crédito), `total` (DECIMAL), `paid` (DECIMAL: `0` si no se ha pagado; `total - paid` = deuda pendiente), `itbis` (DECIMAL), `taxes` (DECIMAL), `discount` (DECIMAL), `delivery` (DECIMAL), `ncf` (VARCHAR), `invoiceNumber` (VARCHAR), `invoiceDate` (DATE), `expirationDate` (DATE), `note` (TEXT), `stock_to_id`, `sell_from_id`, `dataJSON`, `status` (`1`=Activo), `user_id` (INT), `created_at` (DATETIME).

### `spend` y `concept` (Gastos Menores y Conceptos)
- **`concept`**: `id` (PK), `description` (VARCHAR), `status` (`1`=Activo, `2`=Inactivo), `user_id`, `created_at`.
- **`spend`**: `id` (PK), `concept_id` (FK -> `concept.id`), `amount` (DECIMAL), `subtotal` (DECIMAL), `itbis` (DECIMAL, NULL), `bill_id` (INT), `ncf` (VARCHAR), `rnc` (VARCHAR), `provider` (VARCHAR), `date_at` (DATETIME/DATE), `observations` (TEXT), `box_id`, `category_id`, `image`, `dataJSON`, `status` (`1`=Activo), `user_id`, `created_at`.

### `control_ncf` y `ncf_detail` (Comprobantes Fiscales)
- **`control_ncf`**: `id` (PK), `tipodoc` (FK -> `ncf_type.id`), `tipo` (FK -> `client_type.id`), `fecinivig` (DATE), `fecfinvig` (DATE), `secuenciaini` (INT), `secuenciafin` (INT), `secuenciaactual` (INT), `dataJSON`, `status` (`1`=Activo), `user_id`, `created_at`.
- **`ncf_detail`**: `id` (PK), `tipodoc`, `tipo`, `ncf` (VARCHAR: NCF completo), `sell_id` (FK -> `sell.id`), `fecfinvig` (DATE), `status` (`1`=Activo), `user_id`, `created_at`.

### `suscripciones` (Facturación Recurrente)
- **PK**: `id` (INT, AI)
- **Columnas**: `client_id` (FK -> `person.id`), `product_id`, `product_name` (VARCHAR), `price` (DECIMAL), `frequency` (`'diario'`, `'semanal'`, `'quincenal'`, `'mensual'`, `'trimestral'`, `'cuatrimestral'`, `'semestral'`, `'anual'`), `start_date` (DATE), `end_date` (DATE, NULL), `next_billing_date` (DATE), `last_billing_date` (DATE, NULL), `status` (`1`=Activa, `2`=Inactiva, `3`=Cancelada), `dataJSON`, `UUID` (VARCHAR), `user_id` (INT), `created_at` (DATETIME).

### `workshop` (Órdenes de Taller)
- **PK**: `id` (INT, AI)
- **Columnas**: `client_id` (FK -> `person.id`), `description` (TEXT), `brand` (VARCHAR), `model` (VARCHAR), `serie` (VARCHAR), `date_in` (DATE/DATETIME), `date_out` (DATE/DATETIME), `returned` (VARCHAR/INT), `observation` (TEXT), `dataJSON`, `status` (`1`=Activo), `user_id` (INT), `created_at` (DATETIME).

### `configuration`, `user` y Catálogos
- **`configuration`**: `id`, `short` (VARCHAR clave única), `name`, `kind`, `val` (TEXT/JSON), `short_name`, `is_active` (`1`=Activo).
- **`user`**: `id`, `name`, `lastname`, `username`, `email`, `password`, `kind` (`1`=Admin, `0`=Estándar), `status` (`1`=Activo, `2`=Inactivo), `image`, `permits` (JSON), `dataJSON`, `created_at`.
- **Catálogos**: `status` (`id`, `description`, `color`, `label_description`), `client_type` (`id`, `description`, `status`), `person_type` (`id`, `description`), `ncf_type` (`id`, `code`, `description`), `observations` (`id`, `person_id`, `observation`, `user_id`, `created_at`).

---

## 🛤️ 3. Patrones Transaccionales Obligatorios para la IA

1. **Facturación (`sell` -> `operation` -> `payment` -> `ncf_detail`)**:
   - Siempre insertar primero en `sell` y capturar `$sell_id = intval($resSell[1])`.
   - Insertar cada producto vendido en `operation` con `operation_type_id = 2`, `sell_id = $sell_id`, `is_sell = 1`, `is_draft = 0`, `status = 1`, `idStore = 0`.
   - Si es a crédito (`cash < total`), insertar la deuda inicial en `payment` con `payment_type_id = 1`, `val = -abs($total - $cash)` (negativo) y `saldada = 0`.
2. **Cobros / Abonos (`payment`)**:
   - Insertar el pago en `payment` con `payment_type_id = 2` y `val = +abs($abono)` (positivo).
   - Si `SUM(val)` de esa factura llega a `>= 0`, ejecutar `UPDATE payment SET saldada = 1 WHERE person_id = $person_id AND sell_id = $sell_id`.
3. **Existencia de Inventario (`operation`)**:
   - Calcular siempre con `SUM(CASE WHEN operation_type_id = 1 THEN q WHEN operation_type_id = 2 THEN -q ELSE 0 END)` filtrando `WHERE idStore = 0 AND status = 1 AND is_draft = 0 AND operation_type_id IN (1, 2)`.
