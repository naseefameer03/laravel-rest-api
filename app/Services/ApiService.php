<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;

class ApiService
{
    protected string $baseUrl;

    public function __construct(string $baseUrl = '')
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    protected function request(string $method, string $endpoint, array $options = [])
    {
        try {
            $response = Http::timeout(10)->withOptions([
                'verify' => false, // Disable SSL verify if needed
            ])->{$method}($this->baseUrl . $endpoint, $options);

            // Throw exception for 4xx & 5xx
            $response->throw();

            return $response->json();
        } catch (RequestException $e) {
            // Log and rethrow or return custom error
            Log::error('API Request Failed', [
                'method'   => $method,
                'endpoint' => $endpoint,
                'error'    => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function get(string $endpoint, array $params = [])
    {
        return $this->request('get', $endpoint, $params);
    }

    public function post(string $endpoint, array $data = [])
    {
        return $this->request('post', $endpoint, $data);
    }

    public function put(string $endpoint, array $data = [])
    {
        return $this->request('put', $endpoint, $data);
    }

    public function patch(string $endpoint, array $data = [])
    {
        return $this->request('patch', $endpoint, $data);
    }

    public function delete(string $endpoint, array $data = [])
    {
        return $this->request('delete', $endpoint, $data);
    }
}
