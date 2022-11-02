<?php

namespace Shihab\Zoom;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Firebase\JWT\JWT;
use GuzzleHttp\ClientInterface;

class Api implements ApiInterface
{
    public $client;
    public $jwt;
    public $headers;

    protected $url = "https://api.zoom.us/v2/";

    public function __construct(ClientInterface $client = null)
    {
        $this->client = $client;
        $this->jwt = $this->generateToken();
        $this->headers = [
            'Authorization' => 'Bearer ' . $this->jwt,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    private function generateToken(): string
    {
        $key = config('services.zoom.key');
        $secret = config('services.zoom.secret');
        $payload = [
            'iss' => $key,
            'exp' => strtotime('+1 minute'),
        ];

        return JWT::encode($payload, $secret, 'HS256');
    }

    public function get(array $query = []): Response
    {
        $path = 'users/me/meetings';
        $request = $this->request();
        return $request->get($this->url . $path, $query);
    }

    public function post(array $body = []): Response
    {
        $path = 'users/me/meetings';
        $request = $this->request();
        return $request->post($this->url . $path, $body);
    }

    public function patch(string $path, array $body = []): Response
    {
        $request = $this->request();
        return $request->patch($this->url . $path, $body);
    }

    public function delete(string $path, array $body = []): Response
    {
        $request = $this->request();
        return $request->delete($this->url . $path, $body);
    }

    public function request(): PendingRequest
    {
        return Http::withHeaders([
            'authorization' => 'Bearer ' . $this->jwt,
            'content-type' => 'application/json',
        ]);
    }
}
