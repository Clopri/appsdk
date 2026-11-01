# 👥 Tabla `person` — Clientes, Proveedores y Empleados

La tabla `person` centraliza el directorio de todas las personas físicas y jurídicas que interactúan con el ERP: **Clientes**, **Proveedores** y **Empleados / Especialistas**.

---

## 📋 Estructura de Columnas

| Campo | Tipo | Nulo | Por Defecto | Descripción y Uso |
| :--- | :--- | :--- | :--- | :--- |
| **`id`** | `INT` (PK, AI) | No | `AUTO_INCREMENT` | Identificador único de la persona. |
| **`tipo`** | `INT` | No | `3` | Rol dentro del sistema (enlaza con `person_type.id`):<br>• `1` = **Empleado / Especialista / Paciente**<br>• `2` = **Proveedor**<br>• `3` = **Cliente** |
| **`client_type_id`** | `INT` | Sí | `NULL` | Clasificación fiscal del cliente (enlaza con `client_type.id` para emisión de NCF):<br>• `1` = Crédito Fiscal (Empresa)<br>• `2` = Gubernamental<br>• `3` = Consumidor Final (Persona)<br>• `4` = Regímenes Especiales<br>• `5` = Exento |
| **`no`** | `VARCHAR(50)` | Sí | `''` | Número de documento fiscal o identidad: **RNC** (9 dígitos) o **Cédula** (11 dígitos). |
| **`name`** | `VARCHAR(150)` | No | — | Nombre(s) o razón social principal. |
| **`lastname`** | `VARCHAR(150)` | Sí | `''` | Apellido(s). |
| **`company`** | `VARCHAR(150)` | Sí | `''` | Nombre comercial de la empresa (muy utilizado en proveedores `tipo = 2`). |
| **`address`** | `VARCHAR(255)` | Sí | `''` | Dirección principal. |
| **`email`** | `VARCHAR(150)` | Sí | `''` | Correo electrónico principal. |
| **`phone`** | `VARCHAR(50)` | Sí | `''` | Teléfono fijo o de oficina. |
| **`cell`** | `VARCHAR(50)` | Sí | `''` | Teléfono móvil / WhatsApp. |
| **`image`** | `VARCHAR(255)` | Sí | `''` | Nombre o ruta de archivo de la foto/logo. |
| **`gender`** | `VARCHAR(20)` | Sí | `NULL` | Género (usado en empleados `tipo = 1`). |
| **`birthdate`** | `DATE` | Sí | `NULL` | Fecha de nacimiento. |
| **`hiredate`** | `DATE` | Sí | `NULL` | Fecha de contratación (empleados). |
| **`marital_status`** | `VARCHAR(50)` | Sí | `NULL` | Estado civil (empleados). |
| **`nationality`** | `VARCHAR(80)` | Sí | `NULL` | Nacionalidad (empleados). |
| **`social_security_number`** | `VARCHAR(50)` | Sí | `NULL` | Número de Seguridad Social / TSS (empleados). |
| **`position`** | `VARCHAR(100)` | Sí | `NULL` | Cargo o puesto de trabajo (empleados). |
| **`department`** | `VARCHAR(100)` | Sí | `NULL` | Departamento o área (empleados). |
| **`contract_type`** | `VARCHAR(80)` | Sí | `NULL` | Tipo de contrato laboral (empleados). |
| **`termination_date`**| `DATE` | Sí | `NULL` | Fecha de salida/desvinculación (empleados). |
| **`base_salary`** | `DECIMAL(12,2)` | Sí | `0.00` | Salario base mensual (empleados). |
| **`hourly_salary`** | `DECIMAL(12,2)` | Sí | `0.00` | Salario por hora (empleados). |
| **`observations`** | `TEXT` | Sí | `NULL` | Observaciones internas de recursos humanos o ficha. |
| **`dataJSON`** | `JSON / TEXT` | Sí | `NULL` | Campos personalizados o configuración extra en formato JSON. |
| **`status`** | `INT` | No | `1` | Estado del registro (enlaza con `status.id`):<br>• `1` = Activo<br>• `2` = Inactivo / Eliminado |
| **`user_id`** | `INT` | No | `user_id` | Usuario que creó o actualizó el registro. |
| **`created_at`** | `DATETIME` | No | `NOW()` | Fecha y hora de creación. |

---

## 🛠️ Ejemplos de Uso con `Executor::doit($sql)`

### 1. Registrar un Nuevo Cliente (`tipo = 3`)
```php
$uid = intval(user_id);
$sql = "INSERT INTO person (
            image, no, name, lastname, address, email, phone, cell,
            tipo, status, client_type_id, user_id, created_at, dataJSON
        ) VALUES (
            '', '131000001', 'Comercial Dominicana', 'SRL', 'Av. 27 de Febrero',
            'contacto@comercial.do', '8095550101', '8295550102',
            3, 1, 1, $uid, NOW(), NULL
        )";
$res = Executor::doit($sql);
$clientId = intval($res[1]);
```

### 2. Registrar un Nuevo Proveedor (`tipo = 2`)
```php
$uid = intval(user_id);
$sql = "INSERT INTO person (
            no, name, lastname, company, address, email, phone, cell,
            tipo, status, user_id, created_at
        ) VALUES (
            '101000009', 'Carlos', 'Méndez', 'Distribuidora Nacional SA',
            'Zona Industrial de Herrera', 'ventas@distribuidora.do', '8095559999', '',
            2, 1, $uid, NOW()
        )";
$res = Executor::doit($sql);
```

### 3. Listar Clientes Activos
```php
$sql = "SELECT per.*, sta.description AS status_dsc
        FROM person per
        LEFT JOIN status sta ON sta.id = per.status
        WHERE per.tipo = 3 AND per.status = 1
        ORDER BY per.id DESC";
$res = Executor::doit($sql);
```
