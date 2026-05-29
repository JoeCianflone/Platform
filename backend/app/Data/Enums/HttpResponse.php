<?php declare(strict_types=1);

namespace App\Data\Enums;

/**
 * HTTP Response Enum
 */
enum HttpResponse: int
{
    case BAD_REQUEST = 400;
    case CREATED = 201;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case OK = 200;
    case SERVER_ERROR = 500;
    case UNAUTHORIZED = 401;
    case UNPROCESSABLE = 422;

    /**
     * Get the standard status message
     */
    public function status(): string
    {
        return match ($this) {
            self::OK => 'OK',
            self::CREATED => 'Created',
            self::BAD_REQUEST => 'Bad Request',
            self::UNAUTHORIZED => 'Unauthorized',
            self::FORBIDDEN => 'Forbidden',
            self::NOT_FOUND => 'Not Found',
            self::UNPROCESSABLE => 'Unprocessable Entity',
            self::SERVER_ERROR => 'Server Error',
        };
    }
}
