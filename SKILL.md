---
name: clopri-app-builder
description: Build, edit, and validate single-file applications for the Clopri ecosystem using Clopri App SDK v3.9.0. Use when the user asks to create a Clopri app, extension, module, plugin, report, POS tool, or dashboard, or mentions clopri.json, Executor::doit, clopriRequest, clopriStorage, or Clopri database tables.
---
# Clopri App Builder

Build secure, reactive single-file applications (`./src/app.php` + `clopri.json`) for the Clopri platform using SDK v3.9.0, direct SQL execution via `Executor::doit($sql)`, Bootstrap 5, Vue.js 3, and FontAwesome 6 via CDN.

## When to Use

- The user asks to build, design, or modify an application or module for Clopri.
- The user wants to interact with Clopri tables (`sell`, `operation`, `payment`, `person`, `product`, `services`, `cashdesk`, `cotizations`, `re`, `spend`, `suscripciones`, `workshop`, `control_ncf`, `ncf_detail`).
- The user needs a `clopri.json` manifest and a monolithic `./src/app.php` that passes the Clopri security scanner.

## Steps

### 1. Define the Package Identity and Manifest (`clopri.json`)

Every Clopri app uses the following project structure:

- `./clopri.json` (App manifest)
- `./src/app.php` (Single-file backend API + HTML + Vue.js 3 frontend)
- `./src/assets/icon.png` (App icon)

Generate `clopri.json` using this exact structure:

```json
{
  "packageId": "com.tunombrenombre.app",
  "version": "1.0.0",
  "name": "Nombre Elegante de la App",
  "author": "@usuario_clopri",
  "description": "Descripcion corta y potente de lo que hace la app.",
  "icon": "./src/assets/icon.png",
  "app": "./src/app.php"
}
```

Follow these identity rules:

- Format `packageId` using only alphanumeric characters and dots (`/^[a-zA-Z0-9.]+$/`). Never use hyphens (`-`) or underscores (`_`) in `packageId`.
- Synchronize `packageId` in `clopri.json` with `const PACKAGE_ID` inside the JavaScript of `./src/app.php`.

### 2. Enforce Installer Security Rules (Antivirus Scanner)

The Clopri core installer inspects `./src/app.php` with `token_get_all()` before installation. Never generate malicious code or code that attempts to bypass the core sandbox.

Never use any of these blocked PHP functions:

- Execution and dynamic calls: `system`, `exec`, `shell_exec`, `passthru`, `proc_open`, `popen`, `pcntl_exec`, `expect_popen`, `eval`, `assert`, `create_function`, `call_user_func`, `call_user_func_array`.
- Filesystem functions: `fopen`, `fwrite`, `file_put_contents`, `fputs`, `unlink`, `rename`, `copy`, `mkdir`, `rmdir`, `chmod`, `chown`, `chgrp`, `touch`, `symlink`, `link`, `file_get_contents`, `readfile`.
- Network functions: `curl_exec`, `curl_multi_exec`, `curl_init`, `fsockopen`, `pfsockopen`.
- Environment and config functions: `getenv`, `apache_getenv`, `putenv`, `phpinfo`, `ini_set`, `ini_get_all`, `ini_restore`, `get_cfg_var`, `extract`, `parse_str`.

Never use any of these blocked superglobals, tokens, or syntax:

- Superglobals: `$_GET`, `$_POST`, `$_REQUEST`, `$_ENV`, `$_SERVER`, `$_FILES`, `$_SESSION`, `$_COOKIE`, `$GLOBALS`.
- Language tokens: `include`, `include_once`, `require`, `require_once`, `eval`, `__halt_compiler`.
- Termination statements: Never use `exit` or `die`. Always use `return;` to stop script execution after sending a JSON API response or blocking an unauthorized view.
- Shell backticks (`` `cmd` ``) and variable variables (`$$var` or `${$var}`).

### 3. Validate User Authentication via `user_id`

Check whether a user is logged in by evaluating the global constant `user_id` with `is_null(user_id)`:

```php
if (!defined('user_id') || is_null(user_id)) {
    // User is not authenticated: block private API route or view
}
$uid = intval(user_id);
```

Skip the `is_null(user_id)` check only on routes or views explicitly designed for public external access.

### 4. Use Only Core SDK v3.9.0 Classes

All legacy model classes (`SellData`, `ProductData`, `PersonData`, `OperationData`, `Utils`, etc.) are obsolete and removed in SDK v3.9.0. Use only these 6 core interfaces:

- `Executor::doit($sql)`: Executes a single SQL query and returns `array($res, $insert_id)`, where `$query[0]` is the `mysqli_result` (iterate with `while ($row = $query[0]->fetch_assoc())`) and `$query[1]` is the auto-increment `insert_id` for `INSERT` statements.
- `clopriRequest`: Reads sanitized inputs via `clopriRequest::get($key, $default, $type)`, `clopriRequest::post($key, $default, $type)`, and `clopriRequest::json($key = null, $default = null)`.
- `clopriStorage`: Manages sandboxed app files (`json`, `txt`, `csv`, `log`, `xml`, `md`) via `save()`, `read()`, `delete()`, `exists()`, and `listFiles()`.
- `clopriCache`: Manages in-memory cache via `get()`, `set()`, `remove()`, and `flush()`.
- `clopriConfig`: Reads and updates configuration via `get()`, `set()`, `getAdvanced()`, and `all()`.
- `clopriFetch($url, $options)`: Performs external HTTP requests with Anti-SSRF protection.

### 5. Follow Clopri Database Schema and Business Rails

Always sanitize PHP variables with `intval()`, `floatval()`, and `addslashes()` before interpolating them into `$sql`.

#### Creating an Invoice (`sell` + `operation` + `payment` + `ncf_detail`)

1. Insert the invoice header into `sell`:
   - Set `p_id = 1` for cash sales (`cash = total`) or `p_id = 2` for credit sales (`cash < total`).
   - Populate `person_id`, `p_id`, `subtotal`, `taxes`, `total`, `cash`, `iva`, `discount`, `ncf`, `payment_method`, `note`, `user_id`, `created_at` (`NOW()`), `wealth` (`SUM(q * (price_out - price_in))`), `banktransferID`, `creditExpirationDate`, `UUID`, `dataJSON`, and `status = 1`.
   - Capture `$sell_id = intval($resSell[1])`.
2. Insert each line item into `operation`:
   - Set `operation_type_id = 2` (outbound stock), `sell_id = $sell_id`, `is_sell = 1`, `is_draft = 0`, `status = 1`, `idStore = 0`, `stock_id = 0`, `product_id`, `name`, `q`, `price_in`, `price_out`, `itbis` (`'on'` if taxed, `''` if exempt), `user_id`, and `created_at` (`NOW()`).
3. Register initial credit debt in `payment` when `cash < total`:
   - Insert into `payment` with `payment_type_id = 1`, a **negative** `val` (`-abs($total - $cash)`), `saldada = 0`, `person_id`, `sell_id`, `user_id`, and `created_at` (`NOW()`).
4. Register fiscal voucher in `ncf_detail` when an NCF is issued:
   - Insert `tipodoc`, `tipo`, `ncf`, `sell_id`, `status = 1`, `user_id`, `created_at`, `fecfinvig`, and increment `secuenciaactual` in `control_ncf`.

#### Recording a Customer Payment (`payment`)

- Insert into `payment` with `payment_type_id = 2`, a **positive** `val` (`> 0`), `UUID`, `payment_method`, `person_id`, `sell_id`, `saldada` (`1` if full balance is paid, `0` otherwise), `user_id`, and `created_at` (`NOW()`).
- When the invoice balance reaches zero, run `UPDATE payment SET saldada = 1, user_id = $uid WHERE person_id = $person_id AND sell_id = $sell_id`.

#### Calculating Real-Time Product Stock (`operation` + `product`)

Calculate stock per store (`idStore = 0` by default) dynamically from `operation`:

```sql
SELECT p.id, p.barcode, p.name, p.price_in, p.min_price, p.max_price,
       COALESCE(o.stock_total, 0) AS q
FROM product p
LEFT JOIN (
    SELECT product_id,
           SUM(CASE WHEN operation_type_id = 1 THEN q WHEN operation_type_id = 2 THEN -q ELSE 0 END) AS stock_total
    FROM operation
    WHERE idStore = 0 AND status = 1 AND is_draft = 0 AND operation_type_id IN (1, 2)
    GROUP BY product_id
) o ON p.id = o.product_id
WHERE p.status = 1
```

#### Reference of Additional Core Tables

- `person`: `id`, `no` (RNC/Cedula), `name`, `lastname`, `company`, `address`, `email`, `phone`, `cell`, `tipo` (`1`=Employee, `2`=Provider, `3`=Client), `client_type_id` (`1`=Company, `2`=Gov, `3`=Individual, `4`=Special, `5`=Exempt), `status` (`1`=Active), `user_id`, `created_at`, `dataJSON`.
- `services` & `category`: `services` (`id`, `category_id`, `barcode`, `name`, `description`, `price`, `itbis`, `plusIva`, `status = 1`); `category` (`id`, `type`, `prefix`, `description`, `status = 1`).
- `cashdesk`: `id`, `user_id`, `opening_time`, `close_time` (`NULL` while open), `start_amount`, `end_amount`, `unbalanced`, `note`, `dataJSON`.
- `cotizations`: `id`, `ref_id` (links to `operation.sell_id` with `is_draft = 1`), `person_id`, `subtotal`, `discount`, `taxes`, `total`, `note`, `status = 1`, `user_id`, `created_at`.
- `re`: Restocking/purchases (`id`, `ref_id` links to `operation.sell_id` with `operation_type_id = 1`, `bill_id`, `person_id` provider, `p_id`, `total`, `paid`, `itbis`, `ncf`, `invoiceNumber`, `invoiceDate`, `expirationDate`, `note`, `status = 1`, `user_id`, `created_at`).
- `spend` & `concept`: Expenses (`id`, `concept_id`, `amount`, `bill_id`, `ncf`, `date_at`, `observations`, `itbis`, `rnc`, `provider`, `status = 1`, `user_id`, `created_at`).
- `suscripciones`: Recurring billing (`id`, `client_id`, `product_id`, `product_name`, `price`, `frequency`, `start_date`, `end_date`, `next_billing_date`, `last_billing_date`, `status`, `UUID`, `user_id`, `created_at`).
- `workshop`: Repair orders (`id`, `client_id`, `description`, `brand`, `model`, `serie`, `date_in`, `date_out`, `returned`, `observation`, `status = 1`, `dataJSON`, `user_id`, `created_at`).
- `configuration` & `user`: System settings (`short`, `name`, `val`, `is_active`) and ERP users (`id`, `name`, `lastname`, `username`, `email`, `kind`, `status`, `permits`, `dataJSON`).

### 6. Build the UI with Bootstrap 5, Vue.js 3, and FontAwesome via CDN

Never use TailwindCSS. Always include the 3 base CDNs in `<head>`:

- Bootstrap 5 CSS & JS Bundle (`https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css` and `bootstrap.bundle.min.js`)
- FontAwesome 6 (`https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css`)
- Vue.js 3 Global Build (`https://unpkg.com/vue@3/dist/vue.global.prod.js`)

Add any extra CDN libraries required by the app after the base CDNs (such as Chart.js, SweetAlert2, SheetJS, or SortableJS).

## Gotchas

- Never write `WHERE 1=1 AND ...` or `OR 1=1` in SQL queries because tautological conditions are blocked by the SQL firewall. Build a `$conds` array and join with `implode(' AND ', $conds)`.
- Never write `UNION SELECT` in SQL queries because it is blocked by the SQL firewall. Use `UNION ALL SELECT` instead.
- Never pass multiple semicolon-separated statements in a single `Executor::doit($sql)` call. Execute one statement per call.
