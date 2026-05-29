<?php declare(strict_types=1);

namespace App\Concerns\Responses;

trait BuildsEnvelope
{
    /**
     * @return array<string, mixed>
     */
    private function buildEnvelope(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'code' => $this->httpResponse->value,
            'status' => $this->httpResponse->status(),
            'data' => $this->data,
            'errors' => (object) $this->errors,
            'meta' => (object) $this->meta,
        ];
    }
}
