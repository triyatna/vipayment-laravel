<?php

namespace Triyatna\Vipayment\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array getProfile()
 * @method static array getGameServices(?string $filterType = null, ?string $filterValue = null, ?string $filterStatus = null)
 * @method static array createGameOrder(string $service, string $dataNo, ?string $dataZone = null)
 * @method static array createJokiOrder(string $service, string $emailUser, string $password, string $additionalData, int $quantity)
 * @method static array checkGameOrderStatus(?string $trxId = null, ?int $limit = null)
 * @method static array getNickname(string $gameCode, string $userId, ?string $zoneId = null)
 * @method static array getPrepaidServices(?string $filterType = null, ?string $filterValue = null)
 * @method static array createPrepaidOrder(string $service, string $dataNo)
 * @method static array checkPrepaidOrderStatus(?string $trxId = null, ?int $limit = null)
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
