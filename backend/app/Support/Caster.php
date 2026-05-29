<?php declare(strict_types=1);

namespace App\Support;

use App\Contracts\CastFromData;

final class Caster
{
    public static function toSnapshot(
        CastFromData|string $snapshotClass,
        object $data
    ): CastFromData {
        return $snapshotClass::make($data);
    }
}
