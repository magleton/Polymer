<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 16-12-12
 * Time: 上午8:55
 */

namespace Polymer\Presenter\Contracts;

interface PresentableInterface
{
    /**
     * 获取一个Presenter的实例
     *
     * @return mixed
     */
    public function present();
}