<?php

declare(strict_types=1);

namespace Instapago\Instapago\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use Instapago\Instapago\Config\InstapagoConfig;
use Instapago\Instapago\Exceptions\GenericException;
use Instapago\Instapago\Exceptions\InstapagoTimeoutException;

final class GuzzleHttpClient implements HttpClientInterface
{
    private Client $client;

    public function __construct(?InstapagoConfig $config = null)
    {
        $config = $config ?? InstapagoConfig::default();
        $this->client = new Client($config->getGuzzleConfig());
    }

    /**
     * @throws GenericException
     * @throws InstapagoTimeoutException
     * @throws GuzzleException
     */
    public function request(string $method, string $url, array $data = []): array
    {
        if (! in_array($method, ['GET', 'POST', 'DELETE'])) {
            throw new GenericException('HTTP method not supported: ' . $method);
        }

        $key = ($method === 'GET') ? 'query' : 'form_params';
        $args[$key] = $data;

        try {
            $response = $this->client->request($method, $url, $args);
            $body = $response->getBody()->getContents();

            return json_decode($body, true) ?? [];
        } catch (ConnectException $e) {
            throw new InstapagoTimeoutException('Cannot connect to api.instapago.com');
        }
    }
}
