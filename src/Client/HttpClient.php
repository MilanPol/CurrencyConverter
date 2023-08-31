<?php

namespace App\Client;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class HttpClient
{
    protected HttpClientInterface $client;

    protected string $domainUrl;

    protected ?string $authString = null;

    public function __construct(
        HttpClientInterface $client,
        string $domainUrl,
        ?string $authString
    ) {
        $this->client = $client;
        $this->domainUrl = $domainUrl;
        $this->authString = $authString;
    }

    public function get(string $path)
    {
        $response = $this->client->request(
            'GET',
            $this->domainUrl . $path
        );

        $content = null;
        try {
            $response->getHeaders()['content-type'][0];
            $content = $response->getContent();
        } catch (ClientExceptionInterface $e) {
            if ($e->getCode() === 404) {
                $content = 'not active';
            }
        }

        return $content;
    }
}
