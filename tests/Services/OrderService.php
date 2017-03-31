<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 17-3-31
 * Time: 上午9:25
 */

namespace Polymer\Tests\Services;

use Polymer\Service\Service;

class OrderService extends Service
{
    /**
     * 保存数据
     * @return mixed
     */
    public function add()
    {
        $data = [
            'address' => 'address01',
            'phone' => '13800138000',
            'desc' => 'this is a desc',
            'user_id' => 23456,
            'status' => 1,
            'created' => time(),
            'updated' => 0
        ];
        $model = $this->app->model('order', [], 'Polymer\Tests\Models');
        return $model->save($data);
    }

    public function update()
    {
        $data = [
            'address' => 'update Address',
            'updated' => time()
        ];
        $model = $this->app->model('order', [], 'Polymer\Tests\Models');
        return $model->update(['id' => 1], $data);
    }
}