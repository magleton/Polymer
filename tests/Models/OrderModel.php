<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 17-3-31
 * Time: 上午8:50
 */

namespace Polymer\Tests\Models;

use Polymer\Model\Model;

class OrderModel extends Model
{
    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'order';

    /**
     * 数据库表名
     *
     * @var string
     */
    protected $schema = 'db1';

    /**
     * 验证规则
     *
     * @var array
     */
    protected $rules = [];

    /**
     * 实体命名空间
     *
     * @var string
     */
    protected $entityNamespace = 'Polymer\Tests\Entity\Models';

    /**
     * 实体文件的文件系统路径
     *
     * @var string
     */
    protected $entityFolder = ROOT_PATH . '/test/Entity/Models';

    /**
     * Repository的命名空间
     *
     * @var string
     */
    protected $repositoryNamespace = 'Polymer\Tests\Entity\Repositories';

    /**
     * 保存数据
     *
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    public function save(array $data = [])
    {
        try {
            $obj = $this->make($data, [], true);
            $this->em->persist($obj);
            $this->em->flush();
            return $obj->getId();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 修改数据
     *
     * @param array $criteria
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    public function update(array $criteria, array $data = [])
    {
        try {
            $obj = $this->make($data, $criteria, true);
            $this->em->persist($obj);
            $this->em->flush();
            return $obj->getId();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}