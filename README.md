# 🚀 Clopri App SDK v3.9.0 — Documentación Oficial (IA-First)

Bienvenido al **SDK v3.9.0 de Clopri**, diseñado con una arquitectura **IA-First** (orientada tanto a agentes de Inteligencia Artificial como a desarrolladores).

En la versión **3.9.0**, todas las antiguas clases de modelos (`SellData`, `ProductData`, `PersonData`, etc.) han sido **eliminadas por obsoletas**. Ahora toda la interacción con la base de datos del ERP se realiza de forma directa, rápida y transparente utilizando SQL puro a través de una única clase: **`Executor::doit($sql)`**, acompañada de las 5 utilidades nativas del Core (`clopriRequest`, `clopriStorage`, `clopriCache`, `clopriConfig` y `clopriFetch`).

---

## 📌 1. Arquitectura de una App en Clopri v3.9.0

### 1.1 Estructura de Archivos y Manifiesto (`clopri.json`)

Toda aplicación de Clopri consta de un manifiesto `clopri.json`, un ícono en `./src/assets/icon.png` y **un único archivo monolítico `./src/app.php`** que contiene el Backend (API JSON), el HTML y el Frontend reactivo.

```
mi-app-clopri/
├── clopri.json
└── src/
    ├── app.php
    └── assets/
        └── icon.png
```

**Formato obligatorio de `clopri.json`:**

```json
{
  "packageId": "com.tunombrenombre.app",
  "version": "1.0.0",
  "name": "Nombre Elegante de la App",
  "author": "@usuario_clopri",
  "description": "Descripción corta y potente de lo que hace la app.",
  "icon": "./src/assets/icon.png",
  "app": "./src/app.php"
}
```

- **Regla de `packageId`**: Solo puede contener letras, números y puntos (`/^[a-zA-Z0-9.]+$/`). No uses guiones (`-`) ni guiones bajos (`_`).
- **Sincronización**: El `packageId` en `clopri.json` debe ser exactamente igual a la constante `PACKAGE_ID` declarada en el JavaScript de `./src/app.php`.

---

### 1.2 Stack de UI y Frontend (CDN Ultrarrápido)

A partir de la v3.9.0 **no se utiliza TailwindCSS**. Toda interfaz se construye utilizando estos **3 CDNs base obligatorios**:

1. **Bootstrap 5.3+ (CSS y JS Bundle)**: Sistema de grillas, componentes UI, tablas, tarjetas, modales y formularios.
2. **Vue.js 3 (Global Build)**: Componentes reactivos, manejo de estado y consumo de la API integrada en el mismo `app.php`.
3. **FontAwesome 6**: Iconografía del sistema.

```html
<!-- CDNs Base Obligatorios -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
```

> **Libertad de CDNs adicionales:** Después de incluir Bootstrap 5, Vue 3 y FontAwesome, la IA o el desarrollador tiene total libertad de agregar cualquier librería CDN adicional según las necesidades de la aplicación (por ejemplo: *Chart.js* para gráficas, *SweetAlert2* para alertas elegantes, *SheetJS/xlsx* para exportar a Excel, *FullCalendar*, *SortableJS*, etc.).

---

### 1.3 Validación de Sesión de Usuario (`user_id`)

El Core de Clopri define automáticamente la constante global `user_id`.
- Para verificar si el usuario ha iniciado sesión (para proteger vistas privadas o peticiones a la API), **basta con evaluar `is_null(user_id)`**:

```php
// Verificar si el usuario NO está logueado
if (!defined('user_id') || is_null(user_id)) {
    // Usuario no autenticado: bloquear petición o vista privada
}

// Obtener el ID numérico del usuario actual para guardar registros en la BD
$currentUserId = intval(user_id);
```

---

## 🛡️ 2. Escáner de Seguridad del Instalador (Reglas Estrictas)

Al instalar una app, el escáner de seguridad del instalador analiza el archivo `./src/app.php`. **Si el código contiene cualquiera de los siguientes elementos, la instalación será rechazada inmediatamente:**

| Categoría Bloqueada | Elementos Prohibidos (Nunca usar en `app.php`) | Alternativa Segura en Clopri SDK |
| :--- | :--- | :--- |
| **Superglobales PHP** | `$_GET`, `$_POST`, `$_REQUEST`, `$_ENV`, `$_SERVER`, `$_FILES`, `$_SESSION`, `$_COOKIE`, `$GLOBALS` | Usa `clopriRequest::get()`, `clopriRequest::post()`, `clopriRequest::json()` |
| **Funciones de Archivos** | `fopen`, `fwrite`, `file_put_contents`, `fputs`, `unlink`, `rename`, `copy`, `mkdir`, `rmdir`, `chmod`, `chown`, `chgrp`, `touch`, `symlink`, `link`, `file_get_contents`, `readfile` | Usa `clopriStorage::save()`, `clopriStorage::read()`, `clopriStorage::delete()`, `clopriStorage::listFiles()` |
| **Funciones de Red** | `curl_init`, `curl_exec`, `curl_multi_exec`, `fsockopen`, `pfsockopen` | Usa `clopriFetch($url, $options)` |
| **Ejecución / Shell / Eval** | `system`, `exec`, `shell_exec`, `passthru`, `proc_open`, `popen`, `pcntl_exec`, `expect_popen`, `eval`, `assert`, `create_function`, `call_user_func`, `call_user_func_array`, backticks `` `cmd` `` | Escribe lógica nativa en PHP limpio |
| **Entorno e Inclusión** | `include`, `include_once`, `require`, `require_once`, `__halt_compiler`, `getenv`, `apache_getenv`, `putenv`, `phpinfo`, `ini_set`, `ini_get_all`, `ini_restore`, `get_cfg_var`, `extract`, `parse_str` | Todo el código debe residir en `./src/app.php`. Usa `return;` en lugar de `exit` o `die` |
| **Variables Variables** | `$$var`, `${$var}` | Usa arrays asociativos estándar `$data[$key]` |

---

## 🧰 3. Clases y Funciones Core del SDK (`src/`)

El directorio `src/` contiene únicamente los componentes esenciales del Core:

### 3.1 `Executor::doit($sql)` — Motor de Base de Datos

Ejecuta consultas SQL directas contra la base de datos MySQL/MariaDB del cliente.

```php
$query = Executor::doit($sql);
// $query[0] -> mysqli_result (en SELECT) o bool (en INSERT/UPDATE/DELETE)
// $query[1] -> int (ID autogenerado en INSERT: $con->insert_id)
```

#### Cómo leer múltiples filas (`SELECT`):
```php
$res = Executor::doit("SELECT id, name, max_price FROM product WHERE status = 1 ORDER BY name ASC LIMIT 50");
$items = [];
if ($res && $res[0]) {
    while ($row = $res[0]->fetch_assoc()) {
        $items[] = $row;
    }
}
```

#### Cómo insertar y obtener el ID generado (`INSERT`):
```php
$uid = intval(user_id);
$res = Executor::doit("INSERT INTO category (type, prefix, description, status, user_id, created_at) VALUES (1, 'BEB', 'Bebidas', 1, $uid, NOW())");
$newCategoryId = $res[1]; // ID autogenerado
```

#### ⚠️ Reglas de Seguridad SQL en `Executor::doit($sql)`:
1. **Una sola consulta por llamada**: No se permite ejecutar múltiples sentencias separadas por `;` en un mismo `Executor::doit($sql)`.
2. **Prohibido `WHERE 1=1 AND ...` o `OR 1=1`**: Las comparaciones tautológicas están bloqueadas por seguridad. Construye tus filtros dinámicos usando un array `$conds = ["status = 1"]` e `implode(' AND ', $conds)`.
3. **Prohibido `UNION SELECT`**: Si necesitas unir tablas (ej. `product` y `services`), utiliza **`UNION ALL SELECT`** o ejecuta dos consultas separadas.
4. **Palabras clave bloqueadas**: `SLEEP()`, `BENCHMARK()`, `LOAD_FILE()`, `INTO OUTFILE`, `DROP TABLE`, `DROP DATABASE`, `TRUNCATE TABLE`, `INFORMATION_SCHEMA`, `GRANT ALL`, `EXEC(`.

---

### 3.2 `clopriRequest` — Entrada de Datos Sanitizada

Reemplaza a `$_GET`, `$_POST` y `file_get_contents('php://input')`.

| Método | Uso | Tipos Soportados (`$type`) |
| :--- | :--- | :--- |
| `clopriRequest::get($key, $default, $type)` | Parámetros de URL | `'string'`, `'int'`, `'float'`, `'bool'`, `'email'`, `'url'`, `'slug'`, `'array'`, `'raw'` |
| `clopriRequest::post($key, $default, $type)` | Datos de formulario | `'string'`, `'int'`, `'float'`, `'bool'`, `'email'`, `'url'`, `'slug'`, `'array'`, `'raw'` |
| `clopriRequest::json($key = null, $default = null)` | Body JSON de peticiones `fetch` | Si `$key` es `null`, retorna todo el array decodificado |

---

### 3.3 `clopriStorage` — Almacenamiento Sandbox por App

Permite leer y escribir archivos aislados dentro del directorio privado del `packageId`. Extensiones permitidas: `json`, `txt`, `csv`, `log`, `xml`, `md`.

- `clopriStorage::save('config.json', $arrayOrString)`
- `clopriStorage::read('config.json')`
- `clopriStorage::exists('config.json')`
- `clopriStorage::delete('config.json')`
- `clopriStorage::listFiles()`

---

### 3.4 `clopriCache` y `clopriConfig`

- **`clopriCache`**: `clopriCache::get($key)`, `clopriCache::set($key, $data)`, `clopriCache::remove($key)`, `clopriCache::flush()`.
- **`clopriConfig`**: `clopriConfig::get($key, $default)`, `clopriConfig::set($key, $value)`, `clopriConfig::getAdvanced($shortKey, $default)`, `clopriConfig::all()`.

---

### 3.5 `clopriFetch($url, $options)` — Peticiones HTTP Externas

Realiza llamadas HTTP/HTTPS hacia APIs externas con protección Anti-SSRF.

```php
$res = clopriFetch('https://api.ejemplo.com/v1/webhook', [
    'method'  => 'POST',
    'headers' => ['Content-Type' => 'application/json'],
    'body'    => json_encode(['evento' => 'nueva_venta', 'id' => 105]),
    'timeout' => 10
]);
if ($res['ok']) {
    $data = json_decode($res['body'], true);
}
```

---

## 🗄️ 4. Esquema de Tablas de Base de Datos de Clopri

Además del resumen general a continuación, cuentas con documentación dedicada tanto para desarrolladores (un archivo por cada tabla SQL con todas sus columnas, relaciones y ejemplos) como para agentes de Inteligencia Artificial:

- 📚 **[Índice de Documentación SQL para Desarrolladores (`docs/README.md`)](./docs/README.md)**
- 🧠 **[Esquema SQL Unificado para IA (`docs/SCHEMA_IA.md`)](./docs/SCHEMA_IA.md)**
- **Documentación detallada por tabla (`docs/tables/`):**
  - [📄 `sell` — Facturas y Ventas](./docs/tables/sell.md)
  - [📦 `operation` — Movimientos de Inventario y Líneas de Detalle](./docs/tables/operation.md)
  - [💳 `payment` — Pagos, Deudas de Crédito y Abonos](./docs/tables/payment.md)
  - [👥 `person` — Clientes, Proveedores y Empleados](./docs/tables/person.md)
  - [🏷️ `product` — Catálogo de Productos Inventariables](./docs/tables/product.md)
  - [🛠️ `services` — Catálogo de Servicios](./docs/tables/services.md)
  - [🗂️ `category` — Categorías de Productos y Servicios](./docs/tables/category.md)
  - [🏦 `cashdesk` — Turnos, Apertura y Cuadre de Caja](./docs/tables/cashdesk.md)
  - [📝 `cotizations` — Cotizaciones y Presupuestos](./docs/tables/cotizations.md)
  - [🚚 `re` — Compras, Reabastecimiento y Cuentas por Pagar](./docs/tables/re.md)
  - [💸 `spend` y `concept` — Gastos Operativos y Conceptos](./docs/tables/spend_concept.md)
  - [🧾 `control_ncf` y `ncf_detail` — Comprobantes Fiscales (NCF / e-CF)](./docs/tables/ncf.md)
  - [🔄 `suscripciones` — Facturación Recurrente y Membresías](./docs/tables/suscripciones.md)
  - [🔧 `workshop` — Órdenes de Taller y Servicio Técnico](./docs/tables/workshop.md)
  - [⚙️ `configuration`, `user` y Catálogos Auxiliares](./docs/tables/configuration_user.md)

A continuación se documentan las tablas principales del Core de Clopri para construir consultas con `Executor::doit($sql)`.

### 4.1 Tabla `sell` (Cabecera de Facturas / Ventas)

Almacena todas las facturas de venta (al contado y a crédito).

| Columna | Tipo | Descripción / Regla de Negocio |
| :--- | :--- | :--- |
| `id` | `INT` (PK, AI) | Identificador único de la factura. |
| `person_id` | `INT` | ID del cliente (`person.id`). |
| `p_id` | `INT` | Condición de pago: `1` = Al Contado, `2` = A Crédito. |
| `d_id` | `INT` | Estado de entrega: `1` = Entregado, `2` = Pendiente, `3` = Cancelado. |
| `subtotal` | `DECIMAL(12,2)` | Suma de `(q * price_out)` antes de impuestos y descuentos. |
| `taxes` | `DECIMAL(12,2)` | Monto total de impuestos (ITBIS) de la factura. |
| `discount` | `DECIMAL(12,2)` | Monto de descuento aplicado a la factura. |
| `total` | `DECIMAL(12,2)` | Total final facturado (`subtotal + taxes - discount`). |
| `cash` | `DECIMAL(12,2)` | Monto pagado al emitir la factura. En ventas al contado `cash >= total`; en ventas a crédito `cash < total` (usualmente `0`). |
| `iva` | `DECIMAL(5,2)` | Porcentaje de impuesto general aplicado (ej. `18` para 18%). |
| `wealth` | `DECIMAL(12,2)` | Ganancia bruta de la venta: `SUM(q * (price_out - price_in))`. |
| `ncf` | `VARCHAR(30)` | Número de Comprobante Fiscal asignado (ej. `B0100000001`, `E310000000001`). |
| `payment_method` | `VARCHAR(50)` | Método de pago: `'Efectivo'`, `'Transferencia'`, `'Tarjeta'`, etc. |
| `banktransferID` | `INT` | ID de cuenta bancaria si aplica (`0` por defecto). |
| `creditExpirationDate`| `DATETIME` | Fecha de vencimiento cuando la venta es a crédito (`NULL` si es al contado). |
| `note` | `TEXT` | Nota o comentario de la venta. |
| `box_id` | `INT` | ID de cierre de caja (`NULL` si no está empaquetada en caja antigua). |
| `UUID` | `VARCHAR(64)` | Identificador único universal para evitar duplicidad. |
| `dataJSON` | `JSON / TEXT` | Metadatos adicionales (ej. `{"legalTip": 150.00, "ecf": {"legalStatus": "ACCEPTED"}}`). |
| `status` | `INT` | `1` = Activa, `0` o `2` = Anulada/Eliminada. |
| `user_id` | `INT` | ID del usuario que realizó la venta (`user_id`). |
| `created_at` | `DATETIME` | Fecha y hora de emisión (`NOW()`). |

---

### 4.2 Tabla `operation` (Movimientos de Inventario y Líneas de Detalle)

Almacena cada línea de producto o servicio perteneciente a una venta (`sell`), compra (`re`), cotización (`cotizations`) o ajuste de inventario.

| Columna | Tipo | Descripción / Regla de Negocio |
| :--- | :--- | :--- |
| `id` | `INT` (PK, AI) | ID del movimiento. |
| `product_id` | `INT` | ID del producto (`product.id`) o servicio (`services.id`). |
| `name` | `VARCHAR(255)` | Nombre del producto o servicio al momento de la transacción. |
| `q` | `DECIMAL(12,2)` | Cantidad de unidades. |
| `price_in` | `DECIMAL(12,2)` | Costo unitario de compra (`product.price_in`). |
| `min_price` | `DECIMAL(12,2)` | Precio mínimo configurado (usado en entradas/compras). |
| `max_price` | `DECIMAL(12,2)` | Precio máximo configurado (usado en entradas/compras). |
| `price_out` | `DECIMAL(12,2)` | Precio unitario de venta facturado. |
| `itbis` | `VARCHAR(20)` | Indicador de impuesto en la línea (`'on'` si grava ITBIS, `''` o `'0'` si es exento). |
| `operation_type_id` | `INT` | `1` = ENTRADA (suma stock), `2` = SALIDA (resta stock), `3` = ENTRADA-PENDIENTE, `4` = SALIDA-PENDIENTE, `5` = DEVOLUCIÓN, `6` = TRASPASO. |
| `sell_id` | `VARCHAR(64)` | En ventas: `sell.id`. En compras: `re.ref_id`. En cotizaciones: `cotizations.ref_id`. |
| `is_sell` | `TINYINT` | `1` cuando el movimiento corresponde a una factura de venta (`sell`), `0` en otros casos. |
| `is_draft` | `TINYINT` | `0` = Movimiento firme (afecta inventario), `1` = Borrador/Cotización (no afecta inventario). |
| `stock_id` | `INT` | ID de lote/stock (por defecto `0` o `1`). |
| `idStore` | `INT` | ID del almacén o sucursal (`0` por defecto). |
| `note` | `TEXT` | Nota del movimiento (opcional). |
| `expirationDate` | `DATE` | Fecha de vencimiento del lote (opcional). |
| `UUID` | `VARCHAR(64)` | UUID de operación (opcional). |
| `status` | `INT` | `1` = Activo, `0` = Cancelado/Anulado. |
| `user_id` | `INT` | Usuario que ejecutó la operación (`user_id`). |
| `created_at` | `DATETIME` | Fecha y hora del movimiento (`NOW()`). |

---

### 4.3 Tabla `payment` (Cuentas por Cobrar, Deudas Iniciales y Abonos)

Controla los cargos de facturas a crédito y los abonos/pagos recibidos de los clientes.

| Columna | Tipo | Descripción / Regla de Negocio |
| :--- | :--- | :--- |
| `id` | `INT` (PK, AI) | ID del registro de pago. |
| `person_id` | `INT` | ID del cliente (`person.id`). |
| `sell_id` | `INT` | ID de la factura asociada (`sell.id`). |
| `payment_type_id` | `INT` | `1` = Cargo/Deuda inicial de venta a crédito (`val` negativo). `2` = Abono/Pago recibido (`val` positivo). |
| `val` | `DECIMAL(12,2)` | Monto. **Negativo** (`-1500.00`) cuando `payment_type_id = 1`; **Positivo** (`+500.00`) cuando `payment_type_id = 2`. |
| `saldada` | `TINYINT` | `0` = Factura aún con saldo pendiente, `1` = Factura totalmente saldada. |
| `payment_method` | `VARCHAR(50)` | Método de pago del abono (`'Efectivo'`, `'Transferencia'`, `'Tarjeta'`). |
| `UUID` | `VARCHAR(64)` | Agrupa múltiples abonos realizados en un mismo recibo de cobro. |
| `dataJSON` | `JSON / TEXT` | Datos adicionales en JSON. |
| `status` | `INT` | `1` = Activo. |
| `user_id` | `INT` | Usuario que registró el pago (`user_id`). |
| `created_at` | `DATETIME` | Fecha y hora del pago (`NOW()`). |

---

### 4.4 Tabla `person` (Clientes, Proveedores y Empleados)

Directorio unificado de personas y entidades vinculadas al negocio.

| Columna | Tipo | Descripción / Regla de Negocio |
| :--- | :--- | :--- |
| `id` | `INT` (PK, AI) | ID de la persona. |
| `tipo` | `INT` | `1` = Empleado / Especialista, `2` = Proveedor, `3` = Cliente. |
| `client_type_id` | `INT` | Tipo fiscal (tabla `client_type`): `1` = Empresa (Crédito Fiscal), `2` = Gubernamental, `3` = Persona Física/Consumo, `4` = Regímenes Especiales, `5` = Exento. |
| `no` | `VARCHAR(50)` | RNC, Cédula o Pasaporte (11 dígitos sin guiones = Cédula, 9 dígitos = RNC). |
| `name` | `VARCHAR(150)` | Nombre(s). |
| `lastname` | `VARCHAR(150)` | Apellido(s). |
| `company` | `VARCHAR(150)` | Nombre comercial o empresa (usado en proveedores y clientes corporativos). |
| `address` | `VARCHAR(255)` | Dirección física. |
| `email` | `VARCHAR(150)` | Correo electrónico. |
| `phone` | `VARCHAR(50)` | Teléfono residencial/oficina. |
| `cell` | `VARCHAR(50)` | Teléfono celular / WhatsApp. |
| `image` | `VARCHAR(255)` | Foto o avatar (opcional). |
| `base_salary`, `hourly_salary`, `position`, `department`, `hiredate` | `Varios` | Campos exclusivos de empleados (`tipo = 1`). |
| `dataJSON` | `JSON / TEXT` | Metadatos extra del cliente/empleado. |
| `status` | `INT` | `1` = Activo, `2` = Inactivo/Eliminado. |
| `user_id` | `INT` | Usuario creador (`user_id`). |
| `created_at` | `DATETIME` | Fecha de registro (`NOW()`). |

---

### 4.5 Tablas `product`, `services` y `category` (Catálogo Comercial)

#### Tabla `product` (Productos inventariables):
- `id` (PK), `category_id` (`category.id`), `barcode` (SKU/Código de barras único), `name`, `description`
- `price_in` (Costo de compra), `min_price` (Precio mínimo de venta), `max_price` (Precio regular de venta)
- `itbis` (Porcentaje/Indicador de impuesto), `plusIva` (`1` = Impuesto sumado al precio, `0` = Impuesto incluido)
- `sell_negative` (`1` = Permite facturar sin stock, `0` = Bloquea venta sin stock)
- `quantityPerPackage` (Unidades por paquete), `warranty_at`, `observations`, `image`, `dataJSON`
- `status` (`1` = Activo, `2` = Eliminado), `user_id`, `created_at`

#### Tabla `services` (Servicios intangibles):
- `id` (PK), `category_id` (`category.id`), `barcode`, `name`, `description`
- `price` (Precio de venta del servicio), `itbis`, `plusIva` (`1` o `0`), `image`, `dataJSON`
- `status` (`1` = Activo, `2` = Eliminado), `user_id`, `created_at`

#### Tabla `category` (Categorías):
- `id` (PK), `type` (`1` = Producto, `2` = Servicio), `prefix` (Prefijo corto), `description` (Nombre de categoría), `status` (`1` = Activa, `2` = Inactiva), `user_id`, `created_at`, `dataJSON`

---

### 4.6 Tabla `cashdesk` (Turnos y Cuadres de Caja)

| Columna | Tipo | Descripción |
| :--- | :--- | :--- |
| `id` | `INT` (PK, AI) | ID del turno de caja. |
| `user_id` | `INT` | Cajero responsable del turno (`user.id`). |
| `opening_time` | `DATETIME` | Fecha/hora de apertura (`NOW()`). |
| `close_time` | `DATETIME` | Fecha/hora de cierre (`NULL` mientras el turno sigue abierto). |
| `start_amount` | `DECIMAL(12,2)` | Efectivo inicial al abrir la caja. |
| `end_amount` | `DECIMAL(12,2)` | Total acumulado en el turno al cerrar (ventas al contado + abonos). |
| `unbalanced` | `VARCHAR/DEC` | Diferencia o descuadre registrado en el cierre. |
| `note` | `TEXT` | Observaciones del cajero. |
| `dataJSON` | `JSON / TEXT` | Datos extra como `$.legalTip` (propina legal). |

---

### 4.7 Tablas `control_ncf` y `ncf_detail` (Facturación Fiscal DGII)

- **`control_ncf`**: Administra los rangos autorizados de comprobantes.
  - Columnas: `id`, `tipodoc` (`ncf_type.id`), `tipo` (`client_type.id`), `fecinivig` (Inicio vigencia), `fecfinvig` (Fin vigencia), `secuenciaini`, `secuenciafin`, `secuenciaactual`, `status` (`1`=Activo), `user_id`, `created_at`.
- **`ncf_detail`**: Vincula cada NCF emitido con su factura en `sell`.
  - Columnas: `id`, `tipodoc`, `tipo`, `ncf` (NCF completo, ej. `B0100000001`), `sell_id` (`sell.id`), `fecfinvig`, `status` (`1`=Activo), `user_id`, `created_at`.

---

### 4.8 Tablas `cotizations`, `re`, `spend`, `concept`, `suscripciones`, `workshop`, `configuration`, `user`

- **`cotizations` (Cotizaciones)**:
  - `id`, `ref_id` (enlaza con `operation.sell_id` donde `is_draft = 1`), `person_id`, `subtotal`, `discount`, `taxes`, `total`, `note`, `status` (`1`=Activa), `user_id`, `created_at`, `dataJSON`.
- **`re` (Compras / Reabastecimiento y Cuentas por Pagar a Proveedores)**:
  - `id`, `ref_id` (enlaza con `operation.sell_id` donde `operation_type_id = 1`), `bill_id`, `person_id` (Proveedor), `p_id` (`1`=Contado, `2`=Crédito), `total`, `paid` (Monto pagado o `0` si está pendiente), `itbis`, `taxes`, `discount`, `delivery`, `ncf`, `invoiceNumber`, `invoiceDate`, `expirationDate`, `note`, `dataJSON`, `status` (`1`=Activo), `user_id`, `created_at`.
- **`spend` y `concept` (Gastos Operativos y Conceptos)**:
  - `concept`: `id`, `description`, `status` (`1`=Activo, `2`=Inactivo), `user_id`, `created_at`.
  - `spend`: `id`, `concept_id` (`concept.id`), `amount` (Total gasto), `subtotal`, `itbis`, `bill_id`, `ncf`, `rnc`, `provider`, `date_at`, `observations`, `box_id`, `category_id`, `image`, `dataJSON`, `status` (`1`=Activo), `user_id`, `created_at`.
- **`suscripciones` (Facturación Recurrente / Membresías)**:
  - `id`, `client_id` (`person.id`), `product_id`, `product_name`, `price`, `frequency` (`'diario'`, `'semanal'`, `'quincenal'`, `'mensual'`, `'trimestral'`, `'cuatrimestral'`, `'semestral'`, `'anual'`), `start_date`, `end_date`, `next_billing_date`, `last_billing_date`, `status` (`1`=Activa, `2`=Inactiva, `3`=Cancelada), `dataJSON`, `user_id`, `created_at`, `UUID`.
- **`workshop` (Taller / Reparaciones de Equipos)**:
  - `id`, `client_id` (`person.id`), `description`, `brand`, `model`, `serie`, `date_in`, `date_out`, `returned`, `observation`, `status` (`1`=Activo), `dataJSON`, `user_id`, `created_at`.
- **`configuration` (Parámetros Globales)**:
  - `id`, `short` (Clave única, ej. `'company_name'`, `'advancedConfiguration'`), `name`, `kind`, `val`, `short_name`, `is_active`.
- **`user` (Usuarios del ERP)**:
  - `id`, `name`, `lastname`, `username`, `email`, `password`, `kind` (`1`=Admin, `0`=Estándar), `status` (`1`=Activo, `2`=Inactivo), `image`, `permits` (JSON), `dataJSON`, `created_at`.

---

## 🛤️ 5. Rieles de Negocio (Guías Paso a Paso en SQL para la IA y Desarrolladores)

Estos rieles muestran el flujo exacto que sigue el Core de Clopri para registrar transacciones complejas manteniendo la integridad contable y de inventario.

### 🛤️ Riel 1: Cómo Crear una Factura Completa (Al Contado o A Crédito)

Crear una factura en Clopri requiere hasta 4 pasos coordinados con `Executor::doit($sql)`:

```php
$uid = intval(user_id);
$person_id = intval($body['person_id']); // ID del cliente en tabla person
$p_id = intval($body['p_id'] ?? 1);      // 1 = Al Contado, 2 = A Crédito
$subtotal = floatval($body['subtotal']);
$taxes = floatval($body['taxes']);
$discount = floatval($body['discount'] ?? 0);
$total = ($subtotal + $taxes) - $discount;
$cash = ($p_id === 1) ? $total : floatval($body['cash'] ?? 0); // Si es crédito y no abonó inicial, cash = 0
$iva = floatval($body['iva'] ?? 18);
$payment_method = addslashes($body['payment_method'] ?? 'Efectivo');
$ncf = addslashes($body['ncf'] ?? '');
$note = addslashes($body['note'] ?? '');
$wealth = floatval($body['wealth'] ?? 0); // Ganancia calculada: SUM(q * (price_out - price_in))
$banktransferID = intval($body['banktransferID'] ?? 0);
$creditExp = !empty($body['creditExpirationDate']) ? "'" . addslashes($body['creditExpirationDate']) . "'" : "NULL";
$uuid = uniqid('sell_', true);

// PASO 1: Insertar la cabecera de la factura en `sell`
$sqlSell = "INSERT INTO sell (person_id, p_id, subtotal, taxes, total, cash, iva, discount, ncf, payment_method, note, user_id, created_at, wealth, banktransferID, creditExpirationDate, UUID, dataJSON, status)
            VALUES ($person_id, $p_id, $subtotal, $taxes, $total, $cash, $iva, $discount, '$ncf', '$payment_method', '$note', $uid, NOW(), $wealth, $banktransferID, $creditExp, '$uuid', NULL, 1)";
$resSell = Executor::doit($sqlSell);
$sell_id = intval($resSell[1]);

// PASO 2: Insertar cada ítem vendido en `operation` (operation_type_id = 2 -> SALIDA de inventario)
foreach ($body['items'] as $item) {
    $prod_id = intval($item['product_id']);
    $q = floatval($item['q']);
    $price_in = floatval($item['price_in'] ?? 0);
    $price_out = floatval($item['price_out']);
    $itbis_flag = !empty($item['itbis']) ? 'on' : '';
    $item_name = addslashes($item['name'] ?? '');

    $sqlOp = "INSERT INTO operation (status, price_in, price_out, stock_id, product_id, q, operation_type_id, sell_id, is_sell, user_id, created_at, itbis, name, idStore)
              VALUES (1, $price_in, $price_out, 0, '$prod_id', '$q', 2, $sell_id, 1, $uid, NOW(), '$itbis_flag', '$item_name', 0)";
    Executor::doit($sqlOp);
}

// PASO 3: Si la factura es A CRÉDITO (p_id = 2 o cash < total), registrar la deuda inicial en `payment`
if ($cash < $total) {
    // IMPORTANTE: En Clopri, la deuda inicial se guarda con valor NEGATIVO y payment_type_id = 1
    $deudaInicial = -abs($total - $cash);
    $sqlDeuda = "INSERT INTO payment (person_id, sell_id, user_id, val, payment_type_id, saldada, created_at)
                 VALUES ($person_id, $sell_id, $uid, $deudaInicial, 1, 0, NOW())";
    Executor::doit($sqlDeuda);
}

// PASO 4 (Opcional): Si lleva Comprobante Fiscal (NCF), registrar en `ncf_detail`
if (!empty($ncf) && !empty($body['tipodoc'])) {
    $tipodoc = addslashes($body['tipodoc']);
    $tipo_cli = addslashes($body['tipo_cliente'] ?? '1');
    $fecfinvig = addslashes($body['fecfinvig'] ?? date('Y-12-31'));
    $sqlNcf = "INSERT INTO ncf_detail (tipodoc, tipo, ncf, sell_id, status, user_id, created_at, fecfinvig)
               VALUES ('$tipodoc', '$tipo_cli', '$ncf', $sell_id, 1, $uid, NOW(), '$fecfinvig')";
    Executor::doit($sqlNcf);
}
```

---

### 🛤️ Riel 2: Cómo Registrar un Abono / Cobro de Cuenta por Cobrar

Cuando un cliente abona a una factura a crédito:
1. Se inserta un registro en `payment` con `payment_type_id = 2` y `val` **positivo**.
2. Si la suma de abonos cubre la deuda total, se actualiza `saldada = 1`.

```php
$uid = intval(user_id);
$person_id = intval($body['person_id']);
$sell_id = intval($body['sell_id']);
$montoAbono = abs(floatval($body['amount']));
$metodo = addslashes($body['payment_method'] ?? 'Efectivo');
$receiptUUID = addslashes($body['uuid'] ?? uniqid('pay_', true));

// 1. Verificar balance pendiente actual de la factura
// Como la deuda inicial es negativa (payment_type_id=1) y los abonos son positivos (payment_type_id=2),
// SUM(val) es negativo mientras exista deuda (ej. -1000 + 400 = -600).
$resBalance = Executor::doit("SELECT COALESCE(SUM(val), 0) AS balance FROM payment WHERE person_id = $person_id AND sell_id = $sell_id AND saldada = 0");
$rowBal = $resBalance[0]->fetch_assoc();
$deudaActual = abs(floatval($rowBal['balance']));

$quedaSaldada = ($montoAbono >= ($deudaActual - 0.01)) ? 1 : 0;

// 2. Insertar el abono (payment_type_id = 2, val positivo)
$sqlAbono = "INSERT INTO payment (person_id, sell_id, user_id, val, payment_type_id, saldada, created_at, UUID, payment_method)
             VALUES ($person_id, $sell_id, $uid, $montoAbono, 2, $quedaSaldada, NOW(), '$receiptUUID', '$metodo')";
Executor::doit($sqlAbono);

// 3. Si la factura quedó saldada, marcar todos los registros asociados a esa factura como saldada = 1
if ($quedaSaldada === 1) {
    Executor::doit("UPDATE payment SET saldada = 1, user_id = $uid WHERE person_id = $person_id AND sell_id = $sell_id");
}
```

---

### 🛤️ Riel 3: Cómo Consultar el Inventario / Existencia Real por Almacén

En Clopri el stock actual **se calcula en tiempo real** sumando las entradas (`operation_type_id = 1`) y restando las salidas (`operation_type_id = 2`) donde `status = 1` e `is_draft = 0`:

```php
$idStore = 0; // Almacén principal por defecto
$sqlStock = "
    SELECT 
        p.id,
        p.barcode,
        p.name,
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
    ORDER BY p.name ASC
";
$res = Executor::doit($sqlStock);
```

---

### 🛤️ Riel 4: Cómo Registrar una Compra / Reabastecimiento (`re` + `operation`)

```php
$uid = intval(user_id);
$ref_id = time(); // Referencia de enlace entre `re` y `operation`
$provider_id = intval($body['person_id']); // Proveedor (person.tipo = 2)
$p_id = intval($body['p_id'] ?? 1);        // 1 = Contado, 2 = Crédito
$total = floatval($body['total']);
$paid = ($p_id === 1) ? $total : floatval($body['paid'] ?? 0);
$itbis = floatval($body['itbis'] ?? 0);
$ncf = addslashes($body['ncf'] ?? '');
$invNum = addslashes($body['invoiceNumber'] ?? '');
$note = addslashes($body['note'] ?? '');

// 1. Insertar cabecera de compra en `re`
$sqlRe = "INSERT INTO re (ref_id, bill_id, person_id, p_id, total, paid, itbis, status, user_id, created_at, ncf, invoiceNumber, note)
          VALUES ($ref_id, 0, $provider_id, $p_id, $total, $paid, $itbis, 1, $uid, NOW(), '$ncf', '$invNum', '$note')";
$resRe = Executor::doit($sqlRe);

// 2. Insertar cada producto recibido en `operation` (operation_type_id = 1 -> ENTRADA de stock)
foreach ($body['items'] as $item) {
    $prod_id = intval($item['product_id']);
    $q = floatval($item['q']);
    $price_in = floatval($item['price_in']);
    $min_price = floatval($item['min_price'] ?? $price_in);
    $max_price = floatval($item['max_price'] ?? $price_in);
    $name = addslashes($item['name'] ?? '');

    $sqlOp = "INSERT INTO operation (status, price_in, min_price, max_price, price_out, stock_id, product_id, q, operation_type_id, sell_id, user_id, created_at, itbis, name, idStore)
              VALUES (1, $price_in, $min_price, $max_price, $max_price, 0, '$prod_id', '$q', 1, '$ref_id', $uid, NOW(), '0', '$name', 0)";
    Executor::doit($sqlOp);
}
```

---

### 🛤️ Riel 5: Cómo Crear una Cotización (`cotizations` + `operation`)

En las cotizaciones, los ítems se registran en `operation` con **`is_draft = 1`** para que no descuenten del inventario hasta convertirse en venta:

```php
$uid = intval(user_id);
$ref_id = time();
$person_id = intval($body['person_id']);
$subtotal = floatval($body['subtotal']);
$discount = floatval($body['discount'] ?? 0);
$taxes = floatval($body['taxes'] ?? 0);
$total = ($subtotal + $taxes) - $discount;
$note = addslashes($body['note'] ?? '');

// 1. Insertar cotización
$sqlCot = "INSERT INTO cotizations (ref_id, person_id, subtotal, discount, taxes, total, status, user_id, created_at, note)
           VALUES ($ref_id, $person_id, $subtotal, $discount, $taxes, $total, 1, $uid, NOW(), '$note')";
$resCot = Executor::doit($sqlCot);

// 2. Insertar ítems como borrador (is_draft = 1)
foreach ($body['items'] as $item) {
    $prod_id = intval($item['product_id']);
    $q = floatval($item['q']);
    $price_in = floatval($item['price_in'] ?? 0);
    $price_out = floatval($item['price_out']);
    $itbis = !empty($item['itbis']) ? 'on' : '';
    $name = addslashes($item['name'] ?? '');

    $sqlOp = "INSERT INTO operation (price_in, min_price, max_price, price_out, is_draft, stock_id, product_id, q, operation_type_id, sell_id, user_id, created_at, itbis, name, idStore)
              VALUES ($price_in, 0, 0, $price_out, 1, 0, '$prod_id', '$q', 2, '$ref_id', $uid, NOW(), '$itbis', '$name', 0)";
    Executor::doit($sqlOp);
}
```

---

## 💻 6. Plantilla Base Oficial (`./src/app.php`)

```php
<?php
// =========================================================================
// 1. CONFIGURACIÓN E INICIO (SINGLE-FILE APP CLOPRI v3.9.0)
// =========================================================================
$api = clopriRequest::get('api', 'false', 'string');
$route = clopriRequest::get('route', '', 'string');

function responseOk($data = null, $message = 'OK') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => false, 'message' => $message, 'data' => $data]);
}

function responseError($message, $code = 400) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($code);
    echo json_encode(['error' => true, 'message' => $message, 'data' => null]);
}

// =========================================================================
// 2. RUTAS DE LA API INTEGRADA (BACKEND)
// =========================================================================
if ($api === 'true') {
    try {
        // Validar que el usuario esté logueado mediante la constante user_id
        if (!defined('user_id') || is_null(user_id)) {
            throw new Exception('Sesión no válida. Inicia sesión para continuar.');
        }

        $body = clopriRequest::json() ?? [];

        switch ($route) {
            case 'init':
                $res = Executor::doit("SELECT id, name, max_price FROM product WHERE status = 1 ORDER BY id DESC LIMIT 20");
                $products = [];
                if ($res && $res[0]) {
                    while ($row = $res[0]->fetch_assoc()) {
                        $products[] = $row;
                    }
                }
                responseOk(['products' => $products, 'user_id' => user_id]);
                break;

            default:
                throw new Exception("Ruta de API no encontrada: " . $route);
        }
    } catch (\Throwable $e) {
        responseError($e->getMessage());
    }
    return; // NUNCA usar exit ni die
}

// =========================================================================
// 3. PROTECCIÓN DE VISTA (SOLO USUARIOS LOGUEADOS)
// =========================================================================
$esAppPrivada = true;
if ($esAppPrivada && (!defined('user_id') || is_null(user_id))) {
    echo '<div style="padding:2rem;font-family:sans-serif;text-align:center;color:#ef3135;"><h3>Acceso Restringido</h3><p>Debes iniciar sesión en Clopri para acceder a esta aplicación.</p></div>';
    return;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clopri App</title>
    <!-- CDNs Base Obligatorios: Bootstrap 5, FontAwesome 6, Vue 3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        [v-cloak] { display: none; }
        body { background-color: #f8f9fa; }
    </style>
</head>
<body>
    <div id="app" v-cloak>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
            <div class="container">
                <span class="navbar-brand fw-bold">
                    <i class="fa-solid fa-cubes me-2"></i>{{ appName }}
                </span>
                <button class="btn btn-light btn-sm" @click="loadData" :disabled="loading">
                    <i class="fa-solid fa-rotate" :class="{'fa-spin': loading}"></i> Actualizar
                </button>
            </div>
        </nav>

        <main class="container">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Productos Activos</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th class="text-end">Precio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="p in products" :key="p.id">
                                    <td>#{{ p.id }}</td>
                                    <td>{{ p.name }}</td>
                                    <td class="text-end fw-semibold">{{ Number(p.max_price).toFixed(2) }}</td>
                                </tr>
                                <tr v-if="products.length === 0">
                                    <td colspan="3" class="text-center text-muted py-4">Sin registros</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        const { createApp, ref, onMounted } = Vue;
        const API_URL = window.location.href.split('?')[0];
        const TOKEN = '<?= defined("__TOKEN__") ? __TOKEN__ : "" ?>';
        const PACKAGE_ID = 'com.tunombrenombre.app';

        createApp({
            setup() {
                const appName = ref('Mi App Clopri');
                const loading = ref(false);
                const products = ref([]);

                const api = async (route, payload = null) => {
                    loading.value = true;
                    try {
                        const url = `${API_URL}?packageId=${PACKAGE_ID}&api=true&route=${route}&token=${TOKEN}`;
                        const res = await fetch(url, {
                            method: payload ? 'POST' : 'GET',
                            headers: { 'Content-Type': 'application/json' },
                            body: payload ? JSON.stringify(payload) : null
                        });
                        const json = await res.json();
                        if (json.error) throw new Error(json.message);
                        return json.data;
                    } catch (err) {
                        alert(err.message);
                        return null;
                    } finally {
                        loading.value = false;
                    }
                };

                const loadData = async () => {
                    const data = await api('init');
                    if (data) products.value = data.products || [];
                };

                onMounted(loadData);

                return { appName, loading, products, loadData };
            }
        }).mount('#app');
    </script>
</body>
</html>
```

---

#### Clopri App SDK v3.9.0
Soporte: `support@clopri.com`
