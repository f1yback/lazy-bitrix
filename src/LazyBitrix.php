<?php


namespace f1yback\Bitrix24;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;


/**
 * Class LazyBitrix
 * @package f1yback\Bitrix24
 */
final class LazyBitrix
{

    /**
     * @var mixed|array
     */
    private mixed $credentials;
    /**
     * @var bool
     */
    private bool $auth = false;
    /**
     * @var Client
     */
    private Client $client;

    /**
     * Credentials array is required to set up wrapper.
     * Example:
     * $credentials =
     *          [
     *              'domain' => 'my.bitrix24.com',
     *              'auth' => '{key}', // in application context
     *              'webhook' => '{webhook}', // for cases when 'auth' is not set
     *              'id' => '{webhook_creator_id}' // for cases when 'auth' is not set
     *          ];
     * @param array $credentials
     * @throws \Exception
     */
    public function __construct(array $credentials)
    {
        $this->credentials = $credentials;
        if (!$this->validate__credentials())
            throw new \Exception("Wrong credentials");
        $this->setup();
    }

    /**
     * Setting up client
     * @return void
     */
    private function setup() {
        $request_uri = "https://{$this->credentials['domain']}/";
        if (!empty($this->credentials['auth'])) {
            $request_uri .= "rest/";
            $this->auth = true;
        } else
            $request_uri .= "rest/{$this->credentials['id']}/{$this->credentials['webhook']}/";
        $this->client = new Client([
            'base_uri' => $request_uri,
        ]);
    }

    /**
     * Check if required keys are set
     * @return bool
     */
    private function validate__credentials() {
        return !empty($this->credentials['domain']) &&
            (!empty($this->credentials['auth']) || (!empty($this->credentials['id']) && !empty($this->credentials['webhook'])));
    }

    /**
     * Request method based on async guzzle Http Client post-request method
     * @param string $method (e.g. crm.lead.list, crm.deal.add, tasks.task.add etc.)
     * @param callable $callback (your Bitrix24 $response handler)
     * @param array $data (Bitrix24 API params, if required)
     * @return void
     */
    public function request(string $method, callable $callback, array $data = []): void
    {
        if ($this->auth)
            $data['auth'] = $this->credentials['auth'];
        $response = $this->client->postAsync($method, $data);
        $response->then(
            function (ResponseInterface $response) use ($callback) {
                $callback($response->getBody()->getContents());
            },
            function (RequestException $exception) use ($callback) {
                $callback($exception->getMessage());
            }
        );
        $response->wait();
    }
}