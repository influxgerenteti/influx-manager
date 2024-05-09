<?php
namespace App\Helper;

use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\Store\RedisStore;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Lock\Store\RetryTillSaveStore;

class LockHelper
{


    /**
     *
     * @var \Symfony\Component\Lock\Factory
     */
    private $lockFactory;

    /**
     *
     * @var \Symfony\Component\Lock\Store\RetryTillSaveStore
     */
    private $redisStoreObject;

    /**
     *
     * @var \Symfony\Component\Lock\Lock
     */
    private $lockController;

    function __construct()
    {
        $this->redisStoreObject = new RedisStore(RedisAdapter::createConnection("redis://" . getenv("REDIS_HOST") . ":" . getenv("REDIS_PORT") . ""));
        $this->redisStoreObject = new RetryTillSaveStore($this->redisStoreObject);
        $this->lockFactory      = new Factory($this->redisStoreObject);
    }

    /**
     *
     * @return \Symfony\Component\Lock\Lock
     */
    public function getLock ()
    {
        return $this->lockController;
    }

    /**
     *
     * @return \Symfony\Component\Lock\Factory
     */
    public function getLockFactory ()
    {
        return $this->lockFactory;
    }

    /**
     * Configura o Lock para o facade
     *
     * @param mixed|string|\Symfony\Component\Lock\Lock $lockName
     */
    public function setLock ($lockName)
    {
        if (gettype($lockName) === "string") {
            $this->lockController = $this->lockFactory->createLock($lockName, 10, true);
        } else {
            $this->lockController = $lockName;
        }
    }


}
