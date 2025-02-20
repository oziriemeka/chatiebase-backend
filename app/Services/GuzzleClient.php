<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;

class GuzzleClient
{
    private $client;
    private $options;

    /**
     * HttpClient constructor.
     *
     * @param array $options Custom Guzzle options (e.g., timeout, headers)
     */
    public function __construct(array $options = [])
    {
        // Default options
        $defaultOptions = [
            'timeout' => 10, // Default timeout of 10 seconds
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ];

        // Merge default options with custom options
        $this->options = array_merge($defaultOptions, $options);

        // Initialize Guzzle client
        $this->client = new Client($this->options);
    }

    /**
     * Send a GET request.
     *
     * @param string $url
     * @param array $queryParams
     * @return Response
     * @throws GuzzleException
     */
    public function get(string $url, array $queryParams = []): Response
    {
        return $this->client->get($url, [
            'query' => $queryParams,
        ]);
    }

    /**
     * Send a POST request.
     *
     * @param string $url
     * @param array $data
     * @return Response
     * @throws GuzzleException
     */
    public function post(string $url, array $data = []): Response
    {
        return $this->client->post($url, [
            'json' => $data,
        ]);
    }

    /**
     * Send a PUT request.
     *
     * @param string $url
     * @param array $data
     * @return Response
     * @throws GuzzleException
     */
    public function put(string $url, array $data = []): Response
    {
        return $this->client->put($url, [
            'json' => $data,
        ]);
    }

    /**
     * Send a DELETE request.
     *
     * @param string $url
     * @param array $data
     * @return Response
     * @throws GuzzleException
     */
    public function delete(string $url, array $data = []): Response
    {
        return $this->client->delete($url, [
            'json' => $data,
        ]);
    }

    /**
     * Set custom options for the Guzzle client.
     *
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options): self
    {
        $this->options = array_merge($this->options, $options);
        $this->client = new Client($this->options);
        return $this;
    }
}
