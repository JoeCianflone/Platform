<?php declare(strict_types=1);

namespace App\Contracts;

interface Action
{
    public function handle(?DataObject $data = null): bool;
}
