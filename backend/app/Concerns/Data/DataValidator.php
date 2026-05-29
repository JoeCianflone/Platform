<?php declare(strict_types=1);

namespace App\Concerns\Data;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait DataValidator
{
    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [];
    }

    /**
     * @param  array<int|string, mixed>  $args
     *
     * @throws ValidationException
     */
    public static function validate(array $args): void
    {
        $validator = Validator::make($args, static::rules());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
