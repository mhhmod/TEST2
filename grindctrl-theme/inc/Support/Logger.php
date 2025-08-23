<?php

declare(strict_types=1);

namespace GrindCTRL\Theme\Support;

/**
 * Lightweight logger wrapper.
 *
 * @since 3.0.0
 */
final class Logger
{
	public static function info(string $message, array $context = []): void
	{
		self::write('INFO', $message, $context);
	}

	public static function warning(string $message, array $context = []): void
	{
		self::write('WARNING', $message, $context);
	}

	public static function error(string $message, array $context = []): void
	{
		self::write('ERROR', $message, $context);
	}

	private static function write(string $level, string $message, array $context): void
	{
		$channel = apply_filters('grindctrl/logger/channel', 'grindctrl');
		$line = sprintf('[%s] %s: %s %s', $channel, $level, $message, $context ? wp_json_encode($context) : '');
		error_log($line);
	}
}