<?php

declare(strict_types=1);

namespace App\Helper;

use Redis;


class RedisHelper
{

    private static $redisConnection;

    public static function getRedisConnection() : \Redis {
        if (null === RedisHelper::$redisConnection) {
            RedisHelper::$redisConnection = new \Redis();
            RedisHelper::$redisConnection->connect("127.0.0.1",6379);
        }
        return  RedisHelper::$redisConnection;
    }

    public static function getRedisCache($chave){

        if (RedisHelper::getRedisConnection()->exists($chave))
        {
            $cachedValue = RedisHelper::getRedisConnection()->get($chave);
            if($cachedValue != null && $cachedValue != "null"){
                return $cachedValue;
            }
        }
        return null;
    }

    public static function getRedisCacheObject($chave){
        $cachedReturn = RedisHelper::getRedisCache($chave);
        if($cachedReturn != null ){
            $data = \json_decode($cachedReturn, true);
            return $data;
        }
        return null;
    }

    public static function setRedisCache($chave,$data, $timeout){
        if($timeout == null || $timeout == 0){
            RedisHelper::getRedisConnection()->setEx($chave,3600,$data);
        }
        else{
            RedisHelper::getRedisConnection()->setEx($chave,$timeout,$data);
        }


    }

    public static function setRedisCacheObject($chave,$data, $timeout){
        if ($data == null){
            RedisHelper::setRedisCache($chave,"EMPTY",3600);
        }
        else
        {
            RedisHelper::setRedisCache($chave,json_encode($data),3600);
        }
    }

    public static function clearCache(){
        RedisHelper::getRedisConnection()->flushAll();
    }
   


}
