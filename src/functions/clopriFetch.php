<?php

declare(strict_types=1);

/**
 * Realiza peticiones HTTP seguras (Anti-SSRF).
 *
 * @param string $url URL destino.
 * @param array{
 * method?: string,
 * headers?: array<string, string>,
 * body?: array|string|null,
 * timeout?: int,
 * verify_ssl?: bool,
 * follow_redirects?: bool
 * } $options Opciones de configuración.
 * @return array{ok: bool, status: int, body: ?string, error: ?string}
 */
function clopriFetch(string $url, array $options = []): array
{
    return [];
}

/**
 * Helper interno para estandarizar respuesta (privado o global según tu estructura).
 *
 * @param bool $ok Indica si la petición fue exitosa (2xx).
 * @param int $status Código de estado HTTP.
 * @param string|null $body Cuerpo de la respuesta.
 * @param string|null $error Mensaje de error si falló.
 * @return array{ok: bool, status: int, body: ?string, error: ?string}
 */
function _clopri_response(bool $ok, int $status, ?string $body = null, ?string $error = null): array
{
    return [];
}
