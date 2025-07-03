<?php

namespace Triyatna\Vipayment;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class VipaymentClient
{
    public function __construct(
        protected readonly string $apiId,
        protected readonly string $apiKey,
        protected readonly string $baseUrl
    ) {}

    /**
     * Mengirim request ke API dan mengembalikan response api.
     */
    protected function sendRequest(string $endpoint, array $params = []): VipaymentResponse
    {
        $payload = array_merge([
            'key' => $this->apiKey,
            'sign' => md5($this->apiId . $this->apiKey),
        ], $params);

        try {
            $response = Http::asForm()->post($this->baseUrl . $endpoint, $payload);

            $response->throw();

            $responseData = $response->json();


            if (is_null($responseData)) {
                return new VipaymentResponse(false, null, 'API returned a non-JSON or empty response.', $response);
            }

            if (isset($responseData['result']) && $responseData['result'] === false) {
                return new VipaymentResponse(false, $responseData['data'] ?? null, $responseData['message'] ?? 'Request failed.', $response);
            }
            return new VipaymentResponse(true, $responseData['data'] ?? $responseData, $responseData['message'] ?? 'Request successful.', $response);
        } catch (ConnectionException $e) {
            return new VipaymentResponse(false, null, 'Connection Error: ' . $e->getMessage());
        } catch (RequestException $e) {
            $errorData = $e->response->json();
            $errorMessage = $errorData['message'] ?? 'An unknown API error occurred.';
            return new VipaymentResponse(false, $errorData, "API Error: {$errorMessage} (Status: {$e->response->status()})", $e->response);
        } catch (\Exception $e) {
            return new VipaymentResponse(false, null, 'An unexpected error occurred: ' . $e->getMessage());
        }
    }


    // ----------- ACCOUNT -----------
    public function getProfile(): VipaymentResponse
    {
        return $this->sendRequest('profile');
    }

    // ----------- GAME-FEATURE -----------
    public function getGameServices(?string $filterType = null, ?string $filterValue = null, ?string $filterStatus = null): VipaymentResponse
    {
        $params = ['type' => 'services'];
        if ($filterType) $params['filter_type'] = $filterType;
        if ($filterValue) $params['filter_value'] = $filterValue;
        if ($filterStatus) $params['filter_status'] = $filterStatus;

        return $this->sendRequest('game-feature', $params);
    }

    public function createGameOrder(string $service, string $dataNo, ?string $dataZone = null): VipaymentResponse
    {
        $params = [
            'type' => 'order',
            'service' => $service,
            'data_no' => $dataNo,
        ];
        if ($dataZone) $params['data_zone'] = $dataZone;

        return $this->sendRequest('game-feature', $params);
    }

    public function createJokiOrder(string $service, string $emailUser, string $password, string $additionalData, int $quantity): VipaymentResponse
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

    public function checkGameOrderStatus(?string $trxId = null, ?int $limit = null): VipaymentResponse
    {
        $params = ['type' => 'status'];
        if ($trxId) $params['trxid'] = $trxId;
        if ($limit) $params['limit'] = $limit;

        return $this->sendRequest('game-feature', $params);
    }

    public function getNickname(string $gameCode, string $userId, ?string $zoneId = null): VipaymentResponse
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
    public function getPrepaidServices(?string $filterType = null, ?string $filterValue = null): VipaymentResponse
    {
        $params = ['type' => 'services'];
        if ($filterType) $params['filter_type'] = $filterType;
        if ($filterValue) $params['filter_value'] = $filterValue;

        return $this->sendRequest('prepaid', $params);
    }

    public function createPrepaidOrder(string $service, string $dataNo): VipaymentResponse
    {
        return $this->sendRequest('prepaid', [
            'type' => 'order',
            'service' => $service,
            'data_no' => $dataNo,
        ]);
    }

    public function checkPrepaidOrderStatus(?string $trxId = null, ?int $limit = null): VipaymentResponse
    {
        $params = ['type' => 'status'];
        if ($trxId) $params['trxid'] = $trxId;
        if ($limit) $params['limit'] = $limit;

        return $this->sendRequest('prepaid', $params);
    }

    public function webhookValidate(string $signatureHeader): bool
    {
        if (empty($signatureHeader)) {
            return false;
        }
        $expectedSignature = md5($this->apiId . $this->apiKey);
        return hash_equals($expectedSignature, $signatureHeader);
    }
}
