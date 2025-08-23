<?php

declare(strict_types=1);

namespace GrindCTRL\Theme;

// Autoload if available (dev tools only)
$autoload = __DIR__ . '/vendor/autoload.php';
if (is_readable($autoload)) {
	require_once $autoload;
} else {
	// Minimal PSR-4 autoloader fallback
	spl_autoload_register(static function (string $class): void {
		$prefix = __NAMESPACE__ . '\\';
		$base_dir = __DIR__ . '/inc/';
		$len = strlen($prefix);
		if (strncmp($prefix, $class, $len) !== 0) {
			return;
		}
		$relative_class = substr($class, $len);
		$file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
		if (is_readable($file)) {
			require $file;
		}
	});
}

use GrindCTRL\Theme\Theme\Setup;

// Bootstrap theme on after_setup_theme
add_action('after_setup_theme', static function (): void {
	Setup::get_instance()->boot();
});

