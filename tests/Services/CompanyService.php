<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 17-3-31
 * Time: 上午8:55
 */

namespace Polymer\Tests\Services;

use Polymer\Service\Service;

class CompanyService extends Service
{
    /**
     * 保存数据
     *
     * @return mixed
     */
    public function save()
    {
        $data = [
            'name' => '',
            'address' => 'chengdu',
            'phone' => '13800138000',
            'created' => time(),
            'updated' => time()
        ];
        $model = $this->app->model('company', [], 'Polymer\\Tests\\Models');
        return $model->save($data);
    }

    /**
     * 更新数据
     * @return mixed
     */
    public function update()
    {
        $model = $this->app->model('company', [], 'Polymer\\Tests\\Models');
        $data = ['name' => 'updateupdateupdateupdate'];
        return $model->update($data);
    }
}