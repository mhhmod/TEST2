<?php

declare(strict_types=1);

namespace GrindCTRL\Theme\Support;

final class PWA
{
	public static function hook(): void
	{
		add_action('wp_head', [self::class, 'manifest_link']);
	}

	public static function manifest_link(): void
	{
		$theme_uri = get_template_directory_uri();
		echo '<link rel="manifest" href="' . esc_url($theme_uri . '/assets/pwa/manifest.webmanifest') . '">';
	}
}