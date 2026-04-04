<?php

declare(strict_types=1);

namespace Lacus\CnpjGen\Tests\Utils;

use Exception;

trait ExternalCnpjValidator
{
    use EnvironmentVariables;

    protected function isValid(string $cnpjString): bool
    {
        /** @var string */
        $apiUrl = $_ENV['API_URL'] ?? getenv('API_URL');

        if (!$apiUrl) {
            throw new Exception('API URL not defined.');
        }

        /** @var string */
        $apiToken = $_ENV['API_TOKEN'] ?? getenv('API_TOKEN');

        if (!$apiToken) {
            throw new Exception('API secret not defined.');
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

        /** @var string */
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($httpCode >= 400) {
            throw new Exception("HTTP error ({$httpCode}) with CNPJ \"{$cnpjString}\"");
        }

        $parsedResponse = json_decode($response, true);

        if (!is_array($parsedResponse) || !isset($parsedResponse['result'])) {
            throw new Exception('Invalid JSON response for CNPJ "{$cnpjString}"');
        }

        return (bool) $parsedResponse['result'];
    }
}
