<?php
namespace Config;
use CodeIgniter\Config\BaseConfig;

/**
 * For maintain configs related to redmin api services
 */
class Redmine extends BaseConfig
{
    /**
     * ------------------------------------------------------------------------
     * Redmine Site Url
     * ------------------------------------------------------------------------
     * 
     * Root url for interact with redmine api services
     */
    public string $url = "https://dev-redmine.infinitisoftware.net";
    
    /**
     * api pagination limit
     */
    public int $limit= 25;

    public string $username="";
    public string $password="";
    public string $apiKey = "";
}