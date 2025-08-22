<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
	$rectorConfig->paths([
		__DIR__ . '/inc',
		__DIR__ . '/functions.php',
	]);
	$rectorConfig->sets([
		LevelSetList::UP_TO_PHP_83,
		SetList::CODE_QUALITY,
	]);
};
