<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Redis;

class RedisEventHelper
{
    public static function publish($eventType, $payload)
    {
        $message = array_merge(['type' => $eventType], $payload);
        Redis::publish('vote_events', json_encode($message));
    }
}