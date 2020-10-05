<?php

namespace WeDevBr\Mati;

use LogicException;
use WeDevBr\Mati\Support\Contracts\MatiClientInterface;

/**
 * Mati API wrapper class
 *
 * @author Gabriel Mineiro <gabrielpfgmineiro@gmail.com>
 */
class Mati
{
    protected $client;
    protected $client_id = null;
    protected $client_secret = null;

    /**
     * Mati class constructor
     *
     * @param string|null $client_id
     * @param string|null $client_secret
     */
    public function __construct(
        MatiClientInterface $client,
        string $client_id = null,
        string $client_secret = null
    ) {
        $this->client = $client;
        $this->resolveClientId($client_id);
        $this->resolveClientSecret($client_secret);

        if ($this->client_id && $this->client_secret) {
            $this->authorize();
        }
    }

    /**
     * Set Client ID for authorization
     *
     * @param string $client_id
     * @return self
     */
    public function setClientId(string $client_id)
    {
        $this->client_id = $client_id;

        return $this;
    }

    /**
     * Set Client Secret for authorization
     *
     * @param string $client_secret
     * @return self
     */
    public function setClientSecret(string $client_secret)
    {
        $this->client_secret = $client_secret;

        return $this;
    }

    /**
     * Set API access token
     *
     * Good to use with caching for JWT token
     *
     * @param string $access_token
     * @return self
     */
    public function setAccessToken(string $access_token)
    {
        $this->client->withToken($access_token);

        return $this;
    }

    /**
     * Authorize with Mati's API credentials
     *
     * @param string|null $client_id
     * @param string|null $client_secret
     * @return self
     */
    public function authorize(string $client_id = null, string $client_secret = null)
    {
        if ($client_id) {
            $this->setClientId($client_id);
        }

        if ($client_secret) {
            $this->setClientSecret($client_secret);
        }

        if (!($this->client_id && $this->client_secret)) {
            throw new LogicException('No client ID and secret were given to authorize Mati');
        }

        $response = $this->client->getAccessToken($this->client_id, $this->client_secret);

        $this->setAccessToken($response->object()->access_token);

        return $this;
    }

    /**
     * Alias for authorize()
     *
     * @see authorize()
     */
    public function authorise(...$args)
    {
        return $this->authorize(...$args);
    }

    /**
     * Resolve value for Client ID in the constructor
     *
     * @param string|null $client_id
     * @return void
     */
    protected function resolveClientId($client_id)
    {
        if ($client_id) {
            $this->setClientId($client_id);
        } else {
            $config_client_id = config('mati')['client_id'];
            if ($config_client_id) {
                $this->setClientId($config_client_id);
            }
        }
    }

    /**
     * Resolve value for Client Secret in the constructor
     *
     * @param string|null $client_secret
     * @return void
     */
    protected function resolveClientSecret($client_secret)
    {
        if ($client_secret) {
            $this->setClientSecret($client_secret);
        } else {
            $config_client_secret = config('mati')['client_secret'];
            if ($config_client_secret) {
                $this->setClientSecret($config_client_secret);
            }
        }
    }
}
