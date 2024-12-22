<?php

namespace App\Libraries;

use Predis\Client;

class JobQueue
{
    private $redis;

    public function __construct()
    {
        $this->redis = new Client();
    }

    public function push($queue, $job)
    {
        $this->redis->rpush($queue, json_encode($job));
    }

    public function pop($queue)
    {
        $job = $this->redis->lpop($queue);
        return $job ? json_decode($job, true) : null;
    }
}

