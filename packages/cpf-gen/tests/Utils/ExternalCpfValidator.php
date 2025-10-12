<?php

declare(strict_types=1);

namespace Lacus\CpfGen\Tests\Utils;

use Exception;

trait ExternalCpfValidator
{
    use EnvironmentVariables;

    protected function isValid(string $cpfString): bool
    {
        $apiUrl = $_ENV['API_URL'] ?? getenv('API_URL');

        if (!$apiUrl) {
            throw new Exception('API URL not defined.');
        }

        $apiToken = $_ENV['API_TOKEN'] ?? getenv('API_TOKEN');

        if (!$apiToken) {
            throw new Exception('API secret not defined.');
        }

        $curl = curl_init();
        $cpfEscaped = urlencode($cpfString);
        $requestUrl = "{$apiUrl}/cpf/val/{$cpfEscaped}";
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
            throw new Exception("HTTP error ({$httpCode}) with CPF \"{$cpfString}\"");
        }

        return json_decode($response, true)['result'] ?? false;
    }
}
