<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class RedisConnectionTest extends TestCase
{
    public function test_it_can_connect_to_redis()
    {
        $result = Redis::connection()->ping();
        $this->assertEquals('PONG', $result);
    }
}
