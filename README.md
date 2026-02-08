# **Clopri App SDK Documentation**

Welcome to the **Clopri App SDK** guide. This SDK provides a set of powerful classes to handle networking, storage, caching, and core business entities (Products, Sales, People, Restocking, and Configuration) securely and efficiently.

---

# **1. HTTP Networking**

### **Function:** `clopriFetch`

A secure wrapper for making external HTTP requests. It includes built-in **Anti-SSRF** protection and handles header formatting automatically.

| Parameter      | Type     | Description                              |
| :------------- | :------- | :--------------------------------------- |
| **`$url`**     | `string` | The destination URL (http/s).            |
| **`$options`** | `array`  | Configuration options (see table below). |

### **Options Configuration**

| Key          | Default | Description                                       |
| :----------- | :------ | :------------------------------------------------ |
| `method`     | `'GET'` | HTTP Verb (`GET`, `POST`, `PUT`, `DELETE`).       |
| `headers`    | `[]`    | Array of headers.                                 |
| `body`       | `null`  | Request payload (form-data array or JSON string). |
| `timeout`    | `5`     | Timeout in seconds (Max 15s).                     |
| `verify_ssl` | `true`  | Enables/disables SSL verification.                |

#### Example

```php
// GET Request
$response = clopriFetch('[https://api.example.com/products](https://api.example.com/products)');
if ($response['ok']) {
    echo $response['body'];
}

// POST JSON Request
$response = clopriFetch('[https://api.example.com/orders](https://api.example.com/orders)', [
    'method' => 'POST',
    'headers' => ['Content-Type' => 'application/json'],
    'body' => json_encode(['product_id' => 123, 'qty' => 2])
]);
```

---

# **2. Input Handling**

### **Class:** `clopriRequest`

Safely retrieves and sanitizes input from GET, POST, and JSON body.

| Method     | Usage                            | Description               |
| ---------- | -------------------------------- | ------------------------- |
| **`get`**  | `::get('key', default, 'type')`  | Reads sanitized `$_GET`.  |
| **`post`** | `::post('key', default, 'type')` | Reads sanitized `$_POST`. |
| **`json`** | `::json('key', default)`         | Reads JSON body.          |

**Available Types:** `string`, `int`, `float`, `bool`, `email`, `url`, `array`.

#### Example

```php
// URL: /app?id=50&active=true

// Get parameters safely
$id = clopriRequest::get('id', 0, 'int');
$isActive = clopriRequest::get('active', false, 'bool');

// Get JSON body data
$productName = clopriRequest::json('name', 'Untitled Product');

```

---

# **3. Storage System (Sandbox)**

### **Class:** `clopriStorage`

Provides a secure storage isolated per `packageId`.

| Method          | Usage                        | Description                   |
| --------------- | ---------------------------- | ----------------------------- |
| **`save`**      | `::save('file.json', $data)` | Saves content (auto JSON).    |
| **`read`**      | `::read('file.json')`        | Reads file or returns `null`. |
| **`delete`**    | `::delete('file.json')`      | Deletes file.                 |
| **`exists`**    | `::exists('file.json')`      | Checks file presence.         |
| **`listFiles`** | `::listFiles()`              | Lists stored files.           |

#### Example

```php
// Save configuration
$config = ['theme' => 'dark', 'notifications' => true];
clopriStorage::save('settings.json', $config);

// Read configuration
$data = clopriStorage::read('settings.json');

```

---

# **4. Caching System**

### **Class:** `clopriCache`

| Method       | Usage                | Description                    |
| ------------ | -------------------- | ------------------------------ |
| **`set`**    | `::set('key', $val)` | Stores cache value.            |
| **`get`**    | `::get('key')`       | Retrieves cache value or null. |
| **`remove`** | `::remove('key')`    | Deletes key.                   |

#### Example

```php
// Check cache first
$stats = clopriCache::get('daily_stats');

if (!$stats) {
    // Calculate stats...
    $stats = ['visits' => 100, 'sales' => 5];
    // Save to cache
    clopriCache::set('daily_stats', $stats);
}

```

---

# **5. Core Entities**

The SDK exposes several data classes to manage the ERP logic efficiently.

---

### **5.1 Class: `ProductData`**

Manages inventory items and services.

### **Public Properties**

| Property              | Type     | Description            |
| --------------------- | -------- | ---------------------- |
| `$name`               | `string` | Product name.          |
| `$barcode`            | `string` | Unique code (SKU/UPC). |
| `$price_in`           | `float`  | Cost price.            |
| `$min_price`          | `float`  | Minimum selling price. |
| `$max_price`          | `float`  | Selling price.         |
| `$quantityPerPackage` | `float`  | Quantity per package.  |
| `$category_id`        | `int`    | Category ID.           |

### **Public Methods**

| Method                            | Description                     |
| --------------------------------- | ------------------------------- |
| **`::getById($id)`**              | Retrieves a product by ID.      |
| **`::searchProduct($q, $limit)`** | Searches by name/barcode.       |
| **`->add()`**                     | Adds the product to the system. |
| **`->update()`**                  | Updates product information.    |

#### Example

```php
// Create a new product
$prod = new ProductData();
$prod->name = "Wireless Mouse";
$prod->barcode = "WM-001";
$prod->price_in = 10.00;
$prod->min_price = 20.00;
$prod->max_price = 25.00;
$prod->category_id = 1;
$prod->add();

// Search for a product
$results = ProductData::searchProduct("Mouse");

```

---

### **5.2 Class: `SellData`**

Manages the core lifecycle of sales transactions, including invoicing and receivables.

#### **Key Properties**

| Property              | Type     | Description                                    |
| --------------------- | -------- | ---------------------------------------------- |
| **`$id`**             | `int`    | Unique identifier of the sale.                 |
| **`$total`**          | `float`  | Final amount of the sale.                      |
| **`$cash`**           | `float`  | Amount paid by the client.                     |
| **`$person_id`**      | `int`    | ID of the client associated with the sale.     |
| **`$status`**         | `int`    | Status flag (`1` = Active, `0` = Inactive).    |
| **`$payment_method`** | `string` | E.g., `'Cash'`, `'Credit Card'`, `'Transfer'`. |
| **`$ncf`**            | `string` | Fiscal invoice number (Tax ID).                |
| **`$note`**           | `string` | Optional internal notes.                       |

#### **Public Methods**

| Method                    | Type       | Description                                                             |
| ------------------------- | ---------- | ----------------------------------------------------------------------- |
| **`getSellIA`**           | `static`   | **AI Ready.** Returns a detailed dataset joining Sale + Items + Client. |
| **`getAllSellByDate`**    | `static`   | Returns a list of `SellData` objects between two dates.                 |
| **`getReportReceivable`** | `static`   | **Financial.** Generates an Accounts Receivable report.                 |
| **`getById`**             | `static`   | Retrieves a single sale object by its ID.                               |
| **`add`**                 | `instance` | Saves the current sale object to the database.                          |

#### Example

```php
// Create a new sale
$sale = new SellData();
$sale->person_id = 5;      // Client ID
$sale->total = 1500.00;    // Total Amount
$sale->cash = 1500.00;     // Amount Paid
$sale->payment_method = 'Credit Card';
$sale->note = "Sale created via API";
$sale->add();

// Check debts
$debts = SellData::getReportReceivable(null, null, null, null, 5);

```

---

### **5.3 Class: `PersonData`**

Handles Clients, Employees, and Providers.

### **Public Properties**

| Property          | Type     | Description                                           |
| ----------------- | -------- | ----------------------------------------------------- |
| `$name`           | `string` | First name.                                           |
| `$lastname`       | `string` | Last name.                                            |
| `$no`             | `string` | Identification number.                                |
| `$email`          | `string` | Email address.                                        |
| `$phone`          | `string` | Phone number.                                         |
| `$client_type_id` | `int`    | Type: 1=COMPANY, 2=GOV, 3=PERSON, 4=UNIQUE, 5=EXEMPT. |

### **Public Methods**

| Method                     | Description               |
| -------------------------- | ------------------------- |
| **`->add_client()`**       | Registers a new client.   |
| **`->add_employee()`**     | Registers a new employee. |
| **`::getClientsActive()`** | Returns active clients.   |
| **`::getById($id)`**       | Retrieves person details. |

#### Example

```php
// Register a new client
$client = new PersonData();
$client->name = "John";
$client->lastname = "Doe";
$client->no = "001-0000000-0";
$client->client_type_id = 3;
$client->add_client();

```

---

### **5.4 Class: `CategoryData`**

Manages product categories and classifications.

### **Public Properties**

| Property       | Type     | Description                     |
| -------------- | -------- | ------------------------------- |
| `$description` | `string` | The name/label of the category. |
| `$prefix`      | `string` | Short code for the category.    |
| `$type`        | `int`    | Classification ID.              |

### **Public Methods**

| Method                 | Description                          |
| ---------------------- | ------------------------------------ |
| **`->add()`**          | Creates a new category.              |
| **`->update()`**       | Updates existing category.           |
| **`->del($status)`**   | Soft deletes or restores a category. |
| **`::getAllActive()`** | Returns all active categories.       |

#### Example

```php
$cat = new CategoryData();
$cat->description = 'Electronics';
$cat->prefix = 'EL';
$cat->type = 1;
$cat->add();

```

---

### **5.5 Class: `ReData` (Restocking/Expenses)**

Manages purchases, expenses, and inventory restocking.

### **Public Properties**

| Property          | Type     | Description                           |
| ----------------- | -------- | ------------------------------------- |
| `$person_id`      | `int`    | Provider ID.                          |
| `$total`          | `float`  | Total amount of the invoice.          |
| `$itbis`          | `float`  | Tax amount (ITBIS/VAT).               |
| `$ncf`            | `string` | Fiscal Invoice Number.                |
| `$invoiceNumber`  | `string` | Provider's internal invoice number.   |
| `$expirationDate` | `string` | Invoice expiration date (YYYY-MM-DD). |
| `$discount`       | `float`  | Applied discount amount.              |
| `$delivery`       | `float`  | Delivery costs.                       |

### **Public Methods**

| Method        | Description                     |
| ------------- | ------------------------------- |
| **`->add()`** | Registers the purchase/expense. |

#### Example

```php
$re = new ReData();
$re->person_id = 7; // Provider
$re->total = 230.00;
$re->itbis = 18.40;
$re->ncf = 'B0100000001';
$re->invoiceNumber = 'INV-999';
$re->add();

```

---

### **5.6 Class: `CotizationData` (Quotes)**

Handles sales quotes/estimates for clients.

### **Public Properties**

| Property     | Type    | Description          |
| ------------ | ------- | -------------------- |
| `$person_id` | `int`   | Client ID.           |
| `$subtotal`  | `float` | Amount before taxes. |
| `$taxes`     | `float` | Tax amount.          |
| `$total`     | `float` | Final quote total.   |

### **Public Methods**

| Method        | Description                    |
| ------------- | ------------------------------ |
| **`->add()`** | Saves the quote to the system. |

#### Example

```php
$quote = new CotizationData();
$quote->person_id = 10;
$quote->subtotal = 100.00;
$quote->taxes = 18.00;
$quote->total = 118.00;
$quote->add();

```

---

### **5.7 Class: `UserData`**

Manages system users and authentication credentials.

### **Public Properties**

| Property    | Type     | Description             |
| ----------- | -------- | ----------------------- |
| `$username` | `string` | Unique login username.  |
| `$name`     | `string` | User's first name.      |
| `$lastname` | `string` | User's last name.       |
| `$email`    | `string` | Contact email.          |
| `$password` | `string` | Hashed password string. |

### **Public Methods**

| Method        | Description                  |
| ------------- | ---------------------------- |
| **`->add()`** | Registers a new system user. |

#### Example

```php
$user = new UserData();
$user->username = 'admin_user';
$user->name = 'Admin';
$user->lastname = 'User';
$user->email = 'admin@clopri.com';
$user->password = password_hash('securepass', PASSWORD_DEFAULT);
$user->add();

```

---

### **5.8 Class: `ConfigurationData`**

Handles global system settings.

### **Public Methods**

| Method                    | Usage                                 | Description                                |
| ------------------------- | ------------------------------------- | ------------------------------------------ |
| **`::updateValFromName`** | `::updateValFromName('key', 'value')` | Updates a specific configuration value.    |
| **`::getByPreffix`**      | `::getByPreffix('prefix')`            | Retrieves config values matching a prefix. |

#### Example

```php
ConfigurationData::updateValFromName('company_name', 'My Business Inc.');

```

---

### **5.9 Class: `Utils`**

Helper methods for common formatting tasks.

### **Public Methods**

| Method                  | Usage                           | Description                                   |
| ----------------------- | ------------------------------- | --------------------------------------------- |
| **`moneyFormat`**       | `::moneyFormat('Symbol', $val)` | Formats float to currency string.             |
| **`noPermissionPrint`** | `::noPermissionPrint()`         | Returns standard error for permission denied. |

#### Example

```php
echo Utils::moneyFormat('RD$', 1500.50);
// Output: RD$ 1,500.50

```

---

#### Clopri App SDK

support@clopri.com
