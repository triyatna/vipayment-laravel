<?php

namespace Triyatna\Vipayment;

use Illuminate\Http\Client\Response;

/**
 * Data Transfer Object (DTO) untuk respons standar dari API VIPayment.
 */
final class VipaymentResponse
{
    public readonly int $statusCode;

    public function __construct(
        public readonly bool $success,
        public readonly ?array $data,
        public readonly ?string $message,
        // debugging
        public readonly ?Response $rawResponse = null,
    ) {
        $this->statusCode = $rawResponse?->status() ?? 0;
    }

    /**
     * cek transaksi/request berhasil dan harus memiliki data.
     */
    public function hasData(): bool
    {
        return $this->success && !empty($this->data);
    }
}
