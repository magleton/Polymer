<?php
/**
 * Created by PhpStorm.
 * User: macro
 * Date: 17-3-30
 * Time: 下午3:30
 */

namespace Polymer\Tests\Listener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class TestListener
{
    /**
     * @var null
     */
    private $params = null;

    /**
     * TestListener constructor.
     * @param $params
     */
    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * 保存之前处理数据
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $args->getObject()->setAddress('kkkllll');
    }

    /**
     * 更新数据之前处理数据
     *
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $args->getObject()->setAddress('sdfafasfasfsda');
    }
}