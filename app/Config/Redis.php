<?php
namespace Config;

use CodeIgniter\Config\BaseConfig;

class Redis extends BaseConfig
{
    public $host = '127.0.0.1';
    public $port = 6379;
    public $password = null;
    public $database = 0;
}
