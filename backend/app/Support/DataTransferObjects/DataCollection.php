<?php declare(strict_types=1);

namespace App\Support\DataTransferObjects;

use Illuminate\Pagination\LengthAwarePaginator;

abstract readonly class DataCollection
{
    /**
     * @param  array<int, DataTransferObject>  $items
     */
    public function __construct(
        public array $items,
        public int $total,
        public int $page,
        public int $perPage,
    ) {}

    /**
     * @param  LengthAwarePaginator<int, mixed>  $paginator
     */
    public static function fromPaginator(LengthAwarePaginator $paginator): static
    {
        // @phpstan-ignore-next-line new.static
        return new static(
            items: array_map(
                fn ($item) => static::mapItem($item),
                $paginator->items()
            ),
            total: $paginator->total(),
            page: $paginator->currentPage(),
            perPage: $paginator->perPage(),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'items' => array_map(fn ($item) => $item->toArray(), $this->items),
            'total' => $this->total,
            'page' => $this->page,
            'perPage' => $this->perPage,
        ];
    }

    abstract protected static function mapItem(mixed $item): DataTransferObject;
}
