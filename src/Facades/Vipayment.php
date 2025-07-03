<?php

namespace Triyatna\Vipayment\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static VipaymentResponse getProfile()
 * @method static VipaymentResponse getGameServices(?string $filterType = null, ?string $filterValue = null, ?string $filterStatus = null)
 * @method static VipaymentResponse createGameOrder(string $service, string $dataNo, ?string $dataZone = null)
 * @method static VipaymentResponse createJokiOrder(string $service, string $emailUser, string $password, string $additionalData, int $quantity)
 * @method static VipaymentResponse checkGameOrderStatus(?string $trxId = null, ?int $limit = null)
 * @method static VipaymentResponse getNickname(string $gameCode, string $userId, ?string $zoneId = null)
 * @method static VipaymentResponse getPrepaidServices(?string $filterType = null, ?string $filterValue = null)
 * @method static VipaymentResponse createPrepaidOrder(string $service, string $dataNo)
 * @method static VipaymentResponse checkPrepaidOrderStatus(?string $trxId = null, ?int $limit = null)
 *
 * @see \Triyatna\Vipayment\VipaymentClient
 */
class Vipayment extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'vipayment';
    }
}
