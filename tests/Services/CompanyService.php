<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 17-3-31
 * Time: 上午8:55
 */

namespace Polymer\Tests\Services;

use Polymer\Service\Service;
use Polymer\Utils\FuncUtils;

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
            'name' => 'aaa',
            'address' => 'chengduaaaaaaa',
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

    /**
     * 测试获取数据
     *
     * @return null|object
     */
    public function getData()
    {
        $repositoryObj = $this->app->repository('company', 'db1', APP_PATH . '/Entity/', 'Polymer\Tests\Entity\Models', 'Polymer\Tests\Entity\Repositories');
        $entity = $repositoryObj->findOneBy(['id' => 2]);
        return FuncUtils::entityToArray($entity);
    }
}