<?php

declare(strict_types=1);

return [

    'modules_folder_name' => env('MODULE_FOLDER_NAME', 'modules'),

    'base_namespace' => env('MODULE_BASE_NAMESPACE', 'App'),

    'path' => base_path('modules'),

    'modules_src_folder_name' => env('MODULES_SRC_FOLDER_NAME', 'src'),

    'structure_path' => base_path('module-structure.yml'),

    'database_folder_name' => env('MODULES_DATABASE_FOLDER_NAME', 'database'),

    'factories_folder_name' => env('MODULES_FACTORIES_FOLDER_NAME', 'factories'),

    'seeders_folder_name' => env('MODULES_SEEDERS_FOLDER_NAME', 'seeders'),

];
