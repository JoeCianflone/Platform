<?php declare(strict_types=1);

namespace App\Support\Queries;

abstract class Query
{
    abstract public function handle(): mixed;

    public static function run(mixed ...$args): mixed
    {
        return app(static::class)->handle(...$args);
    }
}
