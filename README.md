# **Clopri App SDK Documentation**

Welcome to the **Clopri App SDK** guide. This SDK provides a set of powerful classes to handle networking, storage, caching, and core business entities (Products, Sales, People) securely and efficiently.

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
$response = clopriFetch('https://api.example.com/products');
if ($response['ok']) {
    echo $response['body'];
}

// POST JSON Request
$response = clopriFetch('https://api.example.com/orders', [
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
| :--------- | :------------------------------- | :------------------------ |
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
| :-------------- | :--------------------------- | :---------------------------- |
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
| :----------- | :------------------- | :----------------------------- |
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

### **5.1 Class: `ProductData`**

Manages inventory items and services.

### **Public Properties**

| Property              | Type     | Description            |
| :-------------------- | :------- | :--------------------- |
| `$name`               | `string` | Product name.          |
| `$barcode`            | `string` | Unique code (SKU/UPC). |
| `$price_in`           | `float`  | Cost price.            |
| `$max_price`          | `float`  | Selling price.         |
| `$quantityPerPackage` | `float`  | Quantity per package.  |
| `$category_id`        | `int`    | Category ID.           |

### **Public Methods**

| Method                            | Description                     |
| :-------------------------------- | :------------------------------ |
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

Manages the core lifecycle of sales transactions, including invoicing, payments, cancellations, and advanced financial reporting.

#### **Key Properties**

Before using the methods, it is important to understand the main properties of the object.

| Property              | Type     | Description                                    |
| :-------------------- | :------- | :--------------------------------------------- |
| **`$id`**             | `int`    | Unique identifier of the sale.                 |
| **`$total`**          | `float`  | Final amount of the sale.                      |
| **`$cash`**           | `float`  | Amount paid by the client.                     |
| **`$person_id`**      | `int`    | ID of the client associated with the sale.     |
| **`$status`**         | `int`    | Status flag (`1` = Active, `0` = Inactive).    |
| **`$payment_method`** | `string` | E.g., `'Cash'`, `'Credit Card'`, `'Transfer'`. |
| **`$ncf`**            | `string` | Fiscal invoice number (Tax ID).                |
| **`$note`**           | `string` | Optional internal notes.                       |

---

#### **Public Methods**

| Method                    | Type       | Description                                                                                                  |
| :------------------------ | :--------- | :----------------------------------------------------------------------------------------------------------- |
| **`getSellIA`**           | `static`   | **AI Ready.** Returns a detailed dataset joining Sale + Items (Operations) + Client. Best for deep analysis. |
| **`getAllSellByDate`**    | `static`   | Returns a list of `SellData` objects between two dates. Automatically recalculates remaining debts.          |
| **`getReportReceivable`** | `static`   | **Financial.** Generates an Accounts Receivable report (Pending debts, expired credits).                     |
| **`getById`**             | `static`   | Retrieves a single sale object by its ID.                                                                    |
| **`getAbono`**            | `static`   | Retrieves the list of partial payments (installments) made to a specific sale.                               |
| **`add`**                 | `instance` | Saves the current sale object to the database.                                                               |
| **`cancel`**              | `instance` | Cancels the sale and voids related operations.                                                               |

---

#### **Examples**

**1. Analyzing Sales Data (AI / Reporting)**
Retrieve a rich dataset to analyze which items are selling best with specific clients.

```php
// Get the last 100 detailed records
$dataset = (new SellData())->getSellIA(100);

foreach ($dataset as $row) {
    echo "Client: {$row->nombre_cliente} bought {$row->nombre_item} for ${$row->total_venta}<br>";
}
```

**2. creating a New Sale**
How to instantiate and save a sale manually.

```php
$sale = new SellData();
$sale->person_id = 5;      // Client ID
$sale->total = 1500.00;    // Total Amount
$sale->cash = 1500.00;     // Amount Paid
$sale->payment_method = 'Credit Card';
$sale->note = "Sale created via API";

// Save to DB
$sale->add();
```

**3. Checking Accounts Receivable**
Check for unpaid sales or debts.

```php
// Get debts for a specific client (ID: 5)
$debts = SellData::getReportReceivable(
    client_id: 5,
    statuPayment: 'Pendiente' // Filter by 'Pending' status
);

foreach ($debts as $debt) {
    echo "Invoice #{$debt->id} - Owed: {$debt->remaining_debt} <br>";
}
```

---

### **5.3 Class: `PersonData`**

Handles Clients, Employees, and Providers.

### **Public Properties**

| Property          | Type     | Description                                                      |
| :---------------- | :------- | :--------------------------------------------------------------- |
| `$name`           | `string` | First name.                                                      |
| `$lastname`       | `string` | Last name.                                                       |
| `$no`             | `string` | Identification number.                                           |
| `$email`          | `string` | Email address.                                                   |
| `$phone`          | `string` | Phone number.                                                    |
| `$client_type_id` | `int`    | type person, 1=EMPRESA, 2=GOBIERNO, 3=PERSONA, 4=UNICO, 5=EXENTO |

### **Public Methods**

| Method                     | Description               |
| :------------------------- | :------------------------ |
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
$client->email = "john@example.com";
$client->no = "00100200304"; // ID Document
$client->client_type_id = 1;
$client->add_client();
```

---

#### Clopri App SDK

support@clopri.com
