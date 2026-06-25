<?php declare(strict_types=1);

use Illuminate\Support\Str;

return [

    'enabled' => (bool) env('TENANCY_ENABLED', true),

    /*
     * Derived from APP_NAME at config load time.
     * Produces e.g. "myapp" for APP_NAME="My App".
     * Used as the prefix for tenant database names: {prefix}_tenant_{id}
     */
    'db_prefix' => Str::slug(env('APP_NAME', 'app')),

];
