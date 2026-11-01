# ⚙️ Tablas `configuration`, `user` y Catálogos Auxiliares

Este documento detalla las tablas de configuración global del ERP, usuarios del sistema y tablas auxiliares de catálogo.

---

## 📋 1. Tabla `configuration` (Parámetros Globales del Sistema)

Almacena los ajustes generales de la empresa (nombre comercial, RNC, moneda, impuestos, logo y configuración avanzada en JSON).

| Campo | Tipo | Descripción y Uso |
| :--- | :--- | :--- |
| **`id`** | `INT` (PK, AI) | ID del parámetro de configuración. |
| **`short`** | `VARCHAR(100)` | Clave única corta para buscar el ajuste (ej. `'company_name'`, `'impuesto-val'`, `'advancedConfiguration'`). |
| **`name`** | `VARCHAR(150)` | Nombre descriptivo del parámetro. |
| **`kind`** | `INT` | Tipo de dato del parámetro. |
| **`val`** | `TEXT / JSON` | Valor configurado. En `short = 'advancedConfiguration'`, contiene un arreglo JSON de objetos `[{"short": "clave", "val": "valor"}]`. |
| **`short_name`** | `VARCHAR(100)` | Alias corto opcional. |
| **`is_active`** | `TINYINT(1)` | `1` = Parámetro público/activo, `0` = Inactivo. |

### Ejemplo: Leer o Actualizar una Configuración
```php
// Leer configuración por su clave `short`
$res = Executor::doit("SELECT val FROM configuration WHERE short = 'company_name' LIMIT 1");

// Extraer un valor dentro del JSON de `advancedConfiguration`
$shortKey = addslashes('impresion_automatica');
$sqlAdv = "SELECT JSON_UNQUOTE(
               JSON_EXTRACT(
                   val,
                   REPLACE(
                       JSON_UNQUOTE(JSON_SEARCH(val, 'one', '$shortKey', NULL, '$[*].short')),
                       '.short',
                       '.val'
                   )
               )
           ) AS val
           FROM configuration
           WHERE short = 'advancedConfiguration'";
$resAdv = Executor::doit($sqlAdv);
```

---

## 📋 2. Tabla `user` (Usuarios del Sistema)

Contiene las cuentas de los operadores, cajeros y administradores de Clopri.
> **Recuerda:** Para saber si un usuario tiene sesión iniciada desde tu app, valida la constante `user_id` con `is_null(user_id)`.

| Campo | Tipo | Descripción y Uso |
| :--- | :--- | :--- |
| **`id`** | `INT` (PK, AI) | ID único del usuario (coincide con la constante `user_id` cuando está logueado). |
| **`name`** | `VARCHAR(100)` | Nombre del usuario. |
| **`lastname`** | `VARCHAR(100)` | Apellido del usuario. |
| **`username`** | `VARCHAR(100)` | Nombre de usuario único para iniciar sesión. |
| **`email`** | `VARCHAR(150)` | Correo electrónico. |
| **`password`** | `VARCHAR(255)` | Hash de contraseña. |
| **`kind`** | `INT` | Nivel de acceso: `1` = Administrador general, `0` = Usuario con permisos granulares. |
| **`status`** | `INT` | `1` = Activo, `2` = Inactivo / Eliminado. |
| **`image`** | `VARCHAR(255)` | Avatar del usuario. |
| **`permits`** | `JSON / TEXT` | Permisos granulares por módulo en formato JSON. |
| **`dataJSON`** | `JSON / TEXT` | Preferencias adicionales del usuario en formato JSON. |
| **`created_at`** | `DATETIME` | Fecha de alta del usuario. |

---

## 📋 3. Tablas de Catálogo Auxiliar

### `status` (Estados Generales)
- **`id`**: `1` = Activo, `2` = Inactivo / Eliminado.
- **`description`**: Texto del estado (`'Activo'`, `'Inactivo'`).
- **`color`**, **`label_description`**: Color y etiqueta visual para badges.

### `client_type` (Tipos Fiscales de Cliente)
- **`id`**: `1` = Empresa (Crédito Fiscal), `2` = Gubernamental, `3` = Persona Física (Consumo), `4` = Regímenes Especiales, `5` = Exento.
- **`description`**: Nombre del tipo de cliente.
- **`status`**: `1` = Activo.

### `person_type` (Tipos de Persona)
- **`id`**: `1` = Empleado/Especialista, `2` = Proveedor, `3` = Cliente.
- **`description`**: Descripción del rol.

### `ncf_type` (Tipos de Comprobante Fiscal)
- **`id`**: ID del tipo de documento.
- **`code`**: Prefijo del NCF (`'B01'`, `'B02'`, `'B14'`, `'B15'`, `'E31'`, `'E32'`, etc.).
- **`description`**: Nombre oficial del comprobante.

### `observations` (Comentarios y Notas sobre Personas)
- **`id`**, **`person_id`** (`person.id`), **`observation`** (`TEXT`), **`user_id`**, **`created_at`**.
