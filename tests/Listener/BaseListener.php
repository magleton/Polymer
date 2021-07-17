<?php
/**
 * Created by PhpStorm.
 * User: macro
 * Date: 17-3-30
 * Time: 下午3:30
 */

namespace Polymer\Tests\Listener;

use Doctrine\Persistence\Event\LifecycleEventArgs;

class BaseListener
{
    /**
     * @var array
     */
    private array $params;

    /**
     * TestListener constructor.
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * 保存之前处理数据
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        echo '持久化(保存)之前';
        //$args->getObject()->setLastLoginAt(344444);
    }

    /**
     * 更新数据之前处理数据
     *
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        echo '持久化(更新)之前';
        $args->getObject()->setAddress('sdfafasfasfsda');
    }
}