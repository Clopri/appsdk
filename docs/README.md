# 📚 Documentación del Esquema SQL de Clopri (Guía para Desarrolladores)

Bienvenido al índice de referencia de base de datos de **Clopri App SDK v3.9.0**.
En esta versión, todas las consultas y transacciones se ejecutan directamente mediante **`Executor::doit($sql)`**.

Cada archivo de esta carpeta documenta al detalle las columnas, tipos de datos, relaciones y consultas SQL listas para producción de cada módulo del ERP:

---

## 🗂️ Índice de Tablas por Módulo

### 1. Facturación, Inventario y Cuentas por Cobrar
- 📄 **[`sell` — Facturas y Ventas](./tables/sell.md)**: Cabecera de ventas al contado y a crédito, cálculo de utilidad (`wealth`), propina legal (`$.legalTip`) y reportes 607 / CxC.
- 📦 **[`operation` — Movimientos de Inventario y Líneas de Detalle](./tables/operation.md)**: Renglones de facturas, compras y cotizaciones (`is_draft`), y cálculo de existencia en tiempo real por almacén (`idStore`).
- 💳 **[`payment` — Pagos, Deudas de Crédito y Abonos](./tables/payment.md)**: Cargos iniciales en negativo (`payment_type_id = 1`), abonos en positivo (`payment_type_id = 2`), recibos agrupados por `UUID` y saldo de facturas (`saldada = 1`).
- 🧾 **[`control_ncf` y `ncf_detail` — Comprobantes Fiscales](./tables/ncf.md)**: Lotes de secuencias NCF/e-CF autorizadas por la DGII y asignación por venta.

### 2. Catálogo Comercial y Directorio
- 👥 **[`person` — Clientes, Proveedores y Empleados](./tables/person.md)**: Directorio unificado (`tipo = 1` Empleado, `tipo = 2` Proveedor, `tipo = 3` Cliente) y clasificación fiscal (`client_type_id`).
- 🏷️ **[`product` — Productos Inventariables](./tables/product.md)**: Catálogo de artículos físicos, precios (`price_in`, `min_price`, `max_price`), control de venta en negativo (`sell_negative`) y alertas de bajo stock.
- 🛠️ **[`services` — Servicios Intangibles](./tables/services.md)**: Mano de obra, igualas y servicios sin restricción de stock.
- 🗂️ **[`category` — Categorías](./tables/category.md)**: Clasificación y prefijos de productos y servicios.

### 3. Operaciones de Caja, Compras, Cotizaciones y Gastos
- 🏦 **[`cashdesk` — Turnos y Cuadre de Caja](./tables/cashdesk.md)**: Apertura, arqueo en vivo y cierre de turnos por cajero.
- 📝 **[`cotizations` — Cotizaciones y Presupuestos](./tables/cotizations.md)**: Presupuestos enlazados a `operation` mediante `ref_id` con `is_draft = 1`.
- 🚚 **[`re` — Compras, Reabastecimiento y Cuentas por Pagar](./tables/re.md)**: Recepción de mercancía de proveedores (`operation_type_id = 1`) y control de deuda a proveedores.
- 💸 **[`spend` y `concept` — Gastos Operativos y Conceptos](./tables/spend_concept.md)**: Registro de gastos menores con soporte de NCF/RNC.

### 4. Módulos Especializados y Configuración
- 🔄 **[`suscripciones` — Facturación Recurrente y Membresías](./tables/suscripciones.md)**: Ciclos automáticos (`diario`, `semanal`, `quincenal`, `mensual`, `trimestral`, `semestral`, `anual`).
- 🔧 **[`workshop` — Órdenes de Taller y Servicio Técnico](./tables/workshop.md)**: Recepción, diagnóstico, serie/IMEI y entrega de equipos reparados.
- ⚙️ **[`configuration`, `user` y Catálogos Auxiliares](./tables/configuration_user.md)**: Configuración global, usuarios (`user_id`) y tablas de catálogo (`status`, `client_type`, `person_type`, `ncf_type`, `observations`).

---

## 🤖 Referencia Rápida para Inteligencia Artificial

Si estás alimentando un agente de IA o un LLM con contexto de base de datos, utiliza el documento condensado de alta densidad:
- 🧠 **[`SCHEMA_IA.md` — Esquema SQL Unificado para IA](./SCHEMA_IA.md)**
