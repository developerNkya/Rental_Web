<?php
function get_redis()
{
    $config = config('Redis');
    $redis = new Predis\Client([
        'scheme' => 'tcp',
        'host'   => $config->host,
        'port'   => $config->port,
        'password' => $config->password,
        'database' => $config->database,
    ]);
    return $redis;
}
