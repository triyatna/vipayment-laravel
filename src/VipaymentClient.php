<?php

namespace Triyatna\Vipayment;

use Illuminate\Support\Facades\Http;

class VipaymentClient
{
    protected string $apiId;
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct(string $apiId, string $apiKey, string $baseUrl)
    {
        $this->apiId = $apiId;
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;
    }

    /**
     * Send a request to the API.
     */
    protected function sendRequest(string $endpoint, array $params = []): array
    {
        $payload = array_merge([
            'key' => $this->apiKey,
            'sign' => md5($this->apiId . $this->apiKey),
        ], $params);

        $response = Http::asForm()->post($this->baseUrl . $endpoint, $payload);

        return $response->json();
    }

    // ----------- ACCOUNT -----------
    public function getProfile(): array
    {
        return $this->sendRequest('profile');
    }

    // ----------- GAME-FEATURE -----------
    public function getGameServices(?string $filterType = null, ?string $filterValue = null, ?string $filterStatus = null): array
    {
        $params = ['type' => 'services'];
        if ($filterType) $params['filter_type'] = $filterType;
        if ($filterValue) $params['filter_value'] = $filterValue;
        if ($filterStatus) $params['filter_status'] = $filterStatus;

        return $this->sendRequest('game-feature', $params);
    }

    public function createGameOrder(string $service, string $dataNo, ?string $dataZone = null): array
    {
        $params = [
            'type' => 'order',
            'service' => $service,
            'data_no' => $dataNo,
        ];
        if ($dataZone) $params['data_zone'] = $dataZone;

        return $this->sendRequest('game-feature', $params);
    }

    public function createJokiOrder(string $service, string $emailUser, string $password, string $additionalData, int $quantity): array
    {
        return $this->sendRequest('game-feature', [
            'type' => 'order',
            'service' => $service,
            'data_no' => $emailUser, // email / username
            'data_zone' => $password, // password
            'additional_data' => $additionalData, // Login|Nickname|Hero|Catatan
            'quantity' => $quantity,
        ]);
    }

    public function checkGameOrderStatus(?string $trxId = null, ?int $limit = null): array
    {
        $params = ['type' => 'status'];
        if ($trxId) $params['trxid'] = $trxId;
        if ($limit) $params['limit'] = $limit;

        return $this->sendRequest('game-feature', $params);
    }

    public function getNickname(string $gameCode, string $userId, ?string $zoneId = null): array
    {
        $params = [
            'type' => 'get-nickname',
            'code' => $gameCode,
            'target' => $userId,
        ];
        if ($zoneId) $params['additional_target'] = $zoneId;

        return $this->sendRequest('game-feature', $params);
    }

    // ----------- PREPAID -----------
    public function getPrepaidServices(?string $filterType = null, ?string $filterValue = null): array
    {
        $params = ['type' => 'services'];
        if ($filterType) $params['filter_type'] = $filterType;
        if ($filterValue) $params['filter_value'] = $filterValue;

        return $this->sendRequest('prepaid', $params);
    }

    public function createPrepaidOrder(string $service, string $dataNo): array
    {
        return $this->sendRequest('prepaid', [
            'type' => 'order',
            'service' => $service,
            'data_no' => $dataNo,
        ]);
    }

    public function checkPrepaidOrderStatus(?string $trxId = null, ?int $limit = null): array
    {
        $params = ['type' => 'status'];
        if ($trxId) $params['trxid'] = $trxId;
        if ($limit) $params['limit'] = $limit;

        return $this->sendRequest('prepaid', $params);
    }
}
