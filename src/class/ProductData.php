<?php

declare(strict_types=1);

/**
 * Clase Stub para ProductData.
 * Facilita el autocompletado en IDEs sin ejecutar lógica de base de datos.
 */
class ProductData
{
    /** @var string Nombre de la tabla en base de datos. */
    public static $tablename = "product";

    // --- Propiedades de la Base de Datos ---

    /** @var int|string ID del producto. */
    public $id;

    /** @var string Código de barras. */
    public $barcode;

    /** @var string Nombre del producto. */
    public $name;

    /** @var string Descripción detallada. */
    public $description;

    /** @var float Precio de compra/entrada. */
    public $price_in;

    /** @var float Precio mínimo de venta. */
    public $min_price;

    /** @var float Precio máximo de venta. */
    public $max_price;

    /** @var float Impuesto (ITBIS). */
    public $itbis;

    /** @var string Ruta o nombre de la imagen. */
    public $image;

    /** @var int ID de la categoría asociada. */
    public $category_id;

    /** @var int|string ID del usuario que creó/modificó. */
    public $user_id;

    /** @var string Fecha de creación (SQL Datetime). */
    public $created_at;

    /** @var string Estado del producto ("1" activo, etc). */
    public $status;

    /** @var string Información del navegador/cliente (User Agent). */
    public $client_info;

    /** @var string Información de garantía (texto/fecha). */
    public $warranty_at;

    /** @var string Nombre del archivo de garantía adjunto. */
    public $warranty_file;

    /** @var string Observaciones internas. */
    public $observations;

    /** @var int|bool Indica si se puede vender en negativo (1 o 0). */
    public $sell_negative;

    /** @var float Cantidad por paquete (Nuevo parámetro). */
    public $quantityPerPackage = 0.0;

    // --- Propiedades Dinámicas (Inyectadas por consultas SQL) ---

    /** @var string Descripción del estado (ej: "Activo"). */
    public $status_dsc;

    /** @var string Tipo de item ('Producto' o 'Servicio'). */
    public $tipo;

    /** @var string Nombre de la categoría (Inyectado en joins). */
    public $category;

    /** @var string Nombre de la categoría (Alias usado en searchProduct). */
    public $category_name;


    public function __construct() {}

    /**
     * Obtiene la categoría asociada a este producto.
     * @return CategoryData|null
     */
    public function getCategory()
    {
        return null;
    }

    /**
     * Agrega el producto a la base de datos.
     * @return array|bool Resultado de la operación.
     */
    public function add()
    {
        return false;
    }

    /**
     * Agrega el producto incluyendo imagen.
     * @return array|bool
     */
    public function add_with_image()
    {
        return false;
    }

    /**
     * Actualiza la información del producto.
     * @return array|bool
     */
    public function update()
    {
        return false;
    }

    /**
     * Actualiza solo la imagen del producto.
     * @return void
     */
    public function update_image() {}

    /**
     * Actualiza solo los precios del producto.
     * @return void
     */
    public function update_price() {}

    /**
     * Busca un producto por su ID.
     * @param int|string $id
     * @return self|null
     */
    public static function getById($id)
    {
        return null;
    }

    /**
     * Obtiene productos por categoría (activos y con precio).
     * @param int|string $category_id
     * @return self[]
     */
    public static function getByCategoryID($category_id)
    {
        return [];
    }

    /**
     * Busca un producto exacto por código de barras.
     * @param string $barcode
     * @return self|null
     */
    public static function getBardCode($barcode)
    {
        return null;
    }

    /**
     * Obtiene todos los productos y servicios unificados.
     * @param bool $limit Usar límite.
     * @param int $qt Cantidad límite.
     * @return self[]
     */
    public static function getAllProductService($limit = false, $qt = 200)
    {
        return [];
    }

    /**
     * Buscador avanzado de productos y servicios.
     * Soporta búsqueda por palabras clave o código de barras exacto.
     *
     * @param string $search Término de búsqueda.
     * @param int $limit Límite de resultados.
     * @param bool $barcode Si es true, busca coincidencia exacta de barcode.
     * @return self[]
     */
    public static function searchProduct($search, $limit = 50, $barcode = false)
    {
        return [];
    }

    /**
     * Obtiene un Producto o Servicio por ID, unificando la estructura.
     * @param int|string $id
     * @param bool $active Solo activos.
     * @return self|null
     */
    public static function getProductServiceById($id, $active = true)
    {
        return null;
    }

    /**
     * Cuenta cuántos productos hay en una categoría.
     * @param int|string $k Category ID
     * @return self|null Retorna objeto con propiedad 's' (count).
     */
    public static function CountByCategory($k)
    {
        return null;
    }

    /**
     * Obtiene todos los productos con joins a estado y categoría.
     * @param bool $erased Si es true busca status=2, si no status=1.
     * @return self[]
     */
    public static function getAll($erased = false)
    {
        return [];
    }

    /**
     * Obtiene solo los productos activos.
     * @return self[]
     */
    public static function getActive()
    {
        return [];
    }

    /**
     * Obtiene todos los productos de una categoría (sin filtro de precio).
     * @param int|string $id
     * @return self[]
     */
    public static function getAllByCategoryId($id)
    {
        return [];
    }

    /**
     * Búsqueda simple (LIKE) por nombre, barcode o ID.
     * @param string $p Término de búsqueda.
     * @return self[]
     */
    public static function getLike($p)
    {
        return [];
    }

    /**
     * Verifica si existe un producto con el código de barras dado.
     * @param string $p Código de barras.
     * @return bool True si existe, False si no.
     */
    public static function productExists($p)
    {
        return false;
    }
}
