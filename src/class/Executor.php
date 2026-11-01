<?php

declare(strict_types=1);

/**
 * Clase Stub para Executor (Interacción Directa con Base de Datos).
 * Facilita el autocompletado en IDEs y guía a los agentes de IA sin exponer la lógica interna del Core.
 */
class Executor
{
    /**
     * Registra una entrada en el log de consultas SQL del sistema.
     *
     * @param string $sql Consulta SQL ejecutada.
     * @param string $message Estado o mensaje descriptivo (por defecto "Ok").
     * @return void
     */
    public static function log(string $sql, string $message = "Ok"): void
    {
    }

    /**
     * Ejecuta una consulta SQL directa contra la base de datos de Clopri.
     *
     * Retorna un arreglo de dos posiciones:
     * - Índice 0 ($res): Objeto mysqli_result (para SELECT) o bool (para INSERT, UPDATE, DELETE).
     * - Índice 1 ($insert_id): int con el ID autogenerado en consultas INSERT.
     *
     * @param string $sql Consulta SQL única a ejecutar.
     * @return array{0: \mysqli_result|bool, 1: int|string} Retorna array($res, $insert_id).
     * @throws \Exception Si ocurre un error en la consulta o no pasa la validación de seguridad.
     */
    public static function doit(string $sql): array
    {
        return [false, 0];
    }
}
