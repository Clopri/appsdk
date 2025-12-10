<?php

declare(strict_types=1);

/**
 * Clase Stub para PersonData.
 * Gestiona Clientes, Empleados, Proveedores y Contactos.
 */
class PersonData
{
    /** @var string Nombre de la tabla. */
    public static $tablename = "person";

    // --- Campos de la Tabla 'person' ---

    /** @var int|string ID de la persona. */
    public $id;

    /** @var string|int Estado (1=Activo, etc). */
    public $status;

    /** @var int|string ID del usuario que creó/modificó. */
    public $user_id;

    /** @var string Fecha de creación. */
    public $created_at;

    /** @var string User Agent del navegador. */
    public $client_info;

    /** @var int|string|null Tipo de cliente. */
    public $client_type_id;

    /** @var string|null Nombre del archivo de imagen. */
    public $image;

    /** @var string|int Número de identificación/documento (Cédula/RNC). */
    public $no;

    /** @var string Nombre. */
    public $name;

    /** @var string Apellido. */
    public $lastname;

    /** @var string Dirección. */
    public $address;

    /** @var string Email principal. */
    public $email;

    /** @var string Teléfono fijo. */
    public $phone;

    /** @var string Celular. */
    public $cell;

    /** @var string Género. */
    public $gender;

    /** @var string Fecha de nacimiento (Y-m-d). */
    public $birthdate;

    /** @var string Fecha de contratación. */
    public $hiredate;

    /** @var string Estado civil. */
    public $marital_status;

    /** @var string Nombre de la compañía (para Proveedores). */
    public $company;

    // --- Campos Específicos de Empleados ---

    /** @var string Nacionalidad. */
    public $nationality;

    /** @var string Número de seguridad social. */
    public $social_security_number;

    /** @var string Posición/Cargo. */
    public $position;

    /** @var string Departamento. */
    public $department;

    /** @var string Tipo de contrato. */
    public $contract_type;

    /** @var string Fecha de terminación. */
    public $termination_date;

    /** @var float Salario base. */
    public $base_salary;

    /** @var float Salario por hora. */
    public $hourly_salary;

    /** @var string Observaciones generales. */
    public $observations;

    // --- Campos de Contacto Adicional / Login ---

    /** @var string Email secundario. */
    public $email1;

    /** @var string Dirección secundaria. */
    public $address1;

    /** @var string Teléfono secundario. */
    public $phone1;

    /** @var string Contraseña (Hash). */
    public $password;

    /** @var int Tipo de persona (1=Empleado, 2=Proveedor, 3=Cliente, etc). */
    public $tipo;

    // --- Propiedades Dinámicas (Inyectadas por JOINs en SQL) ---

    /** @var string Descripción del tipo de persona (de tabla person_type). */
    public $nombre_tipo;

    /** @var string Descripción del estado (de tabla status). */
    public $status_dsc;

    /** @var string Color asociado al estado (de tabla status). */
    public $color;

    /** @var string Etiqueta del estado (de tabla status). */
    public $label_description;

    /** @var int ID del servicio (en getSpecialistByService). */
    public $service_id;

    /** @var string Observación individual (en getComments). */
    public $observation;


    public function __construct() {}

    /**
     * Agrega un nuevo empleado.
     * @return array|bool Resultado de la operación.
     */
    public function add_employee()
    {
        return false;
    }

    /**
     * Agrega un nuevo cliente.
     * @return array|bool
     */
    public function add_client()
    {
        return false;
    }

    /**
     * Agrega un nuevo proveedor.
     * @return void
     */
    public function add_provider() {}

    /**
     * Agrega un comentario/observación a una persona.
     * @param int|string $person_id
     * @param string $comentarios
     * @return array|bool
     */
    public function add_comment($person_id, $comentarios)
    {
        return false;
    }

    /** @return void */
    public function update_employee() {}

    /** @return array|bool */
    public function update_client()
    {
        return false;
    }

    /** @return void */
    public function update_provider() {}

    /** @return void */
    public function update_contact() {}

    /** @return void */
    public function update_passwd() {}

    /** @return void */
    public function update_image() {}

    /**
     * Actualiza un comentario existente.
     * @param string $comentarios
     * @return array|bool
     */
    public function update_comment($comentarios)
    {
        return false;
    }

    /**
     * Obtiene persona por ID.
     * @param int|string $id
     * @return self|null
     */
    public static function getById($id)
    {
        return null;
    }

    /**
     * Obtiene persona por número de documento (no/cédula).
     * @param string $id Número de documento.
     * @return self|null
     */
    public static function getByNo($id)
    {
        return null;
    }

    /**
     * Obtiene paciente (tipo 1) por número.
     * @param string $id
     * @return self|null
     */
    public static function getByNoPacient($id)
    {
        return null;
    }

    /**
     * Valida duplicados: Busca por No, excluyendo un ID específico.
     * @param string $id Número de documento.
     * @param int|string $person_id ID a excluir.
     * @return self|null
     */
    public static function getByNoById($id, $person_id)
    {
        return null;
    }

    /**
     * Obtiene todas las personas de tipo 1 (Empleados/Usuarios).
     * @return self[]
     */
    public static function getAll()
    {
        return [];
    }

    /**
     * Obtiene los comentarios asociados a una persona.
     * @param int|string $id
     * @return self|null Retorna objeto mapeado a la tabla observations.
     */
    public static function getComments($id)
    {
        return null;
    }

    /**
     * Obtiene todos los clientes (tipo 3).
     * @return self[]
     */
    public static function getClients()
    {
        return [];
    }

    /**
     * Obtiene clientes activos (tipo 3, status 1).
     * @return self[]
     */
    public static function getClientsActive()
    {
        return [];
    }

    /**
     * Obtiene proveedores activos (tipo 2, status 1).
     * @return self[]
     */
    public static function getProvidersActive()
    {
        return [];
    }

    /**
     * Obtiene personas que cumplen años hoy.
     * @return self[]
     */
    public static function getBirthDay()
    {
        return [];
    }

    /**
     * Filtra empleados (tipo 1) activos por descripción del tipo.
     * @param string $typePerson
     * @return self[]
     */
    public static function getPersonByType($typePerson)
    {
        return [];
    }

    /**
     * Filtra empleados (tipo 1) por descripción del tipo (incluye inactivos).
     * @param string $typePerson
     * @return self[]
     */
    public static function getPersonByTypeAll($typePerson)
    {
        return [];
    }

    /**
     * Filtra por descripción de tipo e incluye metadatos de color/label.
     * @param string $typePerson
     * @return self[]
     */
    public static function getPersonByAll($typePerson)
    {
        return [];
    }

    /**
     * Obtiene historial/auditoría de personas.
     * @param string $typePerson
     * @return self[]
     */
    public static function getAudit($typePerson)
    {
        return [];
    }

    /**
     * Obtiene especialistas (tipos 2 al 11).
     * @return self[]
     */
    public static function getSpecialist()
    {
        return [];
    }

    /**
     * Obtiene especialistas activos (tipos 2 al 5).
     * @return self[]
     */
    public static function getSpecialistActive()
    {
        return [];
    }

    /**
     * Obtiene especialistas que ofrecen un servicio específico.
     * @param int|string $id Service ID.
     * @return self[]
     */
    public static function getSpecialistByService($id)
    {
        return [];
    }

    /**
     * Obtiene clientes/pacientes (tipo 1) que tienen crédito habilitado.
     * @return self[]
     */
    public static function getClientsWithCredit()
    {
        return [];
    }

    /**
     * Obtiene contactos (tipo 3).
     * @return self[]
     */
    public static function getContacts()
    {
        return [];
    }

    /**
     * Obtiene proveedores (tipo 2).
     * @return self[]
     */
    public static function getProviders()
    {
        return [];
    }

    /**
     * Obtiene proveedores activos (alias de getProvidersActive).
     * @return self[]
     */
    public static function getActiveProviders()
    {
        return [];
    }

    /**
     * Búsqueda simple por nombre.
     * @param string $q
     * @return self[]
     */
    public static function getLike($q)
    {
        return [];
    }
}
