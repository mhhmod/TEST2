<?php

declare(strict_types=1);

namespace GrindCTRL\Theme;

// Autoload if available (dev tools only)
$autoload = __DIR__ . '/vendor/autoload.php';
if (is_readable($autoload)) {
	require_once $autoload;
}

use GrindCTRL\Theme\Theme\Setup;

// Bootstrap theme on after_setup_theme
add_action('after_setup_theme', static function (): void {
	Setup::get_instance()->boot();
});

