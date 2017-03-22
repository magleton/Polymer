<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/16
 * Time: 18:40
 */

namespace Polymer\Utils;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;

class SnowFlake extends AbstractIdGenerator
{
    /**
     * Generates an identifier for an entity.
     *
     * @param EntityManager|EntityManager $em
     * @param \Doctrine\ORM\Mapping\Entity $entity
     * @return mixed
     */
    public function generate(EntityManager $em, $entity)
    {
        return $this->generateID();
    }

    /**
     * Generate the 64bit unique ID.
     * @return number BIGINT
     */
    public function generateID()
    {
        /**
         * Current Timestamp - 41 bits
         */
        $curr_timestamp = floor(microtime(true) * 1000);
        /**
         * Subtract custom epoch from current time
         */
        $curr_timestamp -= app()->config('customer')['initial_epoch'];
        /**
         * Create a initial base for ID
         */
        $base = decbin(pow(2, 40) - 1 + $curr_timestamp);
        /**
         * Get ID of database server (10 bits)
         * Up to 512 machines
         */
        $shard_id = decbin(pow(2, 9) - 1 + $this->getServerShardId());
        /**
         * Generate a random number (12 bits)
         * Up to 2048 random numbers per db server
         */
        $random_part = mt_rand(1, pow(2, 11) - 1);
        $random_part = decbin(pow(2, 11) - 1 + $random_part);
        /**
         * Concatenate the final ID
         */
        $final_id = bindec($base) . bindec($shard_id) . bindec($random_part);
        /**
         * Return unique 64bit ID
         */
        return $final_id;
    }

    /**
     * Identify the database and get the ID.
     * Only MySQL.
     * @return \Exception|int|\PDOException
     */
    private function getServerShardId()
    {
        $em = app()->db(app()->component('database_name'));
        try {
            $database_name = $em->getConnection()->getDatabasePlatform()->getName();
        } catch (\PDOException $e) {
            return $e;
        }
        switch ($database_name) {
            case 'mysql':
                return (int)$this->getMySqlServerId();
            default:
                return (int)1;
        }
    }

    /**
     * Get server-id from mysql cluster or replication server.
     * @return mixed
     */
    private function getMySqlServerId()
    {
        $em = app()->db(app()->component('database_name'));
        /*$result = $em->getConnection()->query('SELECT @@server_id as server_id LIMIT 1')->fetch();
        return $result['server_id'];*/
        return $em->getConnection()->getActiveShardId();
    }

    /**
     * Return time from 64bit ID.
     * @param $id
     * @return number
     */
    public function getTimeFromID($id)
    {
        return bindec(substr(decbin($id), 0, 41)) - pow(2, 40) + 1 + app()->config('customer')['initial_epoch'];
    }
}