<?php declare(strict_types=1);

namespace App\Data\DataObject;

use App\Contracts\DataObject;
use App\Concerns\Data\DataObjects\DataObjectMaker;

final readonly class CoffeeDataObject implements DataObject
{
    use DataObjectMaker;

    public function __construct(
        public string $foo,
    ) {}
}
