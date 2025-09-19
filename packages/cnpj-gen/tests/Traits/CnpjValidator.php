<?php

declare(strict_types=1);

namespace Lacus\CnpjGen\Tests\Traits;

trait CnpjValidator
{
    use EnvironmentVariables;

    protected function isValid(string $cnpjString): bool
    {
        $apiUrl = $_ENV['API_URL'] ?? getenv('API_URL');
        $apiToken = $_ENV['API_TOKEN'] ?? getenv('API_TOKEN');

        if (!$apiUrl || !$apiToken) {
            throw new \Exception('API environment variables not defined.');
        }

        $curl = curl_init();
        $cnpjEscaped = urlencode($cnpjString);
        $requestUrl = "{$apiUrl}/cnpj/val/{$cnpjEscaped}";
        curl_setopt_array($curl, [
            CURLOPT_URL => $requestUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$apiToken}",
            ],
        ]);
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($httpCode >= 400) {
            throw new \Exception("HTTP error ({$httpCode}) with CNPJ \"{$cnpjString}\"");
        }

        return json_decode($response, true)['result'] ?? false;
    }
}
