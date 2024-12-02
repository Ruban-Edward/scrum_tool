<?php
namespace App\Services;

use CodeIgniter\Config\Services;
use Exception;
use Redmine\Client\Client;
use Redmine\Client\NativeCurlClient;

/**
 * @author yuvansri <yuvansri@infinitisoftware.net>
 * 
 * It's redmine service class. Which provides as access to communication with
 * redmine api services.
 */
class RedmineService extends Services
{
    protected Client $client;
    protected string $usernameOrApiKey;
    protected string $password;


    /**
     * @param string username or api key
     * @param string password
     *
     * intiating remine client
     * 
     * @return \Redmine\Client\Client 
     */
    public function getClient($usernameOrApiKey = null, $password = null): Client
    {
        $usernameOrApiKey = (isset($usernameOrApiKey) && !empty($usernameOrApiKey)) 
            ? $usernameOrApiKey
            : config('Redmine')->apiKey;
        if (! isset($this->client) ||
            ! $this->client instanceof NativeCurlClient ||
            $this->usernameOrApiKey != $usernameOrApiKey) {
            $this->client = new NativeCurlClient(
                config('Redmine')->url,
                $usernameOrApiKey,
                $password
            );
            $this->usernameOrApiKey = $usernameOrApiKey;
        }
        return $this->client;
    }       
}