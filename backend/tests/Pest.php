<?php declare(strict_types=1);

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/*
|--------------------------------------------------------------------------
| Module test discovery
|--------------------------------------------------------------------------
|
| Pest discovers module tests via phpunit.xml testsuites. Here we bind
| the correct base class and traits to all test locations.
|
*/

$backendPath = dirname(__DIR__);
$moduleFeatureDirs = glob("{$backendPath}/modules/*/tests/Feature", GLOB_ONLYDIR) ?: [];
$moduleUnitDirs = glob("{$backendPath}/modules/*/tests/Unit", GLOB_ONLYDIR) ?: [];

pest()
    ->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature', ...$moduleFeatureDirs);

pest()
    ->extend(TestCase::class)
    ->in('Unit', ...$moduleUnitDirs);

pest()->in('Architecture');

/*
|--------------------------------------------------------------------------
| Module namespace helper
|--------------------------------------------------------------------------
*/

function moduleNamespaces(): array
{
    return array_map(
        fn (string $dir): string => 'App\\' . basename($dir),
        glob(dirname(__DIR__) . '/modules/*', GLOB_ONLYDIR) ?: []
    );
}

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});
