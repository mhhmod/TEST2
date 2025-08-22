<?php

declare(strict_types=1);

namespace GrindCTRL\Theme\Support;

final class Security
{
	public static function nonce_field(string $action, string $name = '_wpnonce'): string
	{
		return wp_nonce_field($action, $name, true, false);
	}

	public static function verify_nonce(?string $nonce, string $action): bool
	{
		return (bool) ( $nonce && wp_verify_nonce($nonce, $action) );
	}

	public static function csp_headers(): void
	{
		add_filter('wp_headers', static function (array $headers): array {
			$headers['Content-Security-Policy'] = "default-src 'self'; img-src 'self' data:; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'";
			$headers['X-Content-Type-Options'] = 'nosniff';
			$headers['Referrer-Policy'] = 'strict-origin-when-cross-origin';
			return $headers;
		});
	}
}