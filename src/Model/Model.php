<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 2016/10/26
 * Time: 19:42
 */
namespace Polymer\Model;

use Doctrine\DBAL\Sharding\PoolingShardManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Polymer\Boot\Application;
use Polymer\Exceptions\EntityValidateErrorException;
use Polymer\Exceptions\ModelInstanceErrorException;
use Polymer\Utils\Constants;
use Symfony\Component\Validator\Validator\RecursiveValidator;

class Model
{
    /**
     * 应用APP
     *
     * @var Application
     */
    protected $app = null;

    /**
     * 验证组件
     *
     * @var RecursiveValidator
     */
    protected $validator = null;

    /**
     * 要验证的实体对象
     *
     * @var null
     */
    protected $entityObject = null;

    /**
     * EntityManager实例
     *
     * @var EntityManager
     */
    protected $em = null;

    /**
     * 自定义数据
     *
     * @var array
     */
    protected $data = [];

    /**
     * 模型构造函数
     * @param array $params
     * @throws ModelInstanceErrorException
     */
    public function __construct(array $params = [])
    {
        try {
            $this->app = app();
            $schema = isset($params['schema']) ? $params['schema'] : $this->getProperty('schema');
            if ($schema) {
                $this->em = $this->app->db($schema);
            }
        } catch (\Exception $e) {
            throw new ModelInstanceErrorException('模型实例化错误' . $e->getMessage());
        }
    }

    /**
     * 生成数据库表的实体对象
     *
     * @param array $data 自定义数据
     * @param array $criteria 获取对象的条件(用于更新数据)
     * @param bool $returnEObj 是否返回实体对象
     * @return Object|$this
     * @throws \Exception
     */
    protected function make(array $data = [], array $criteria = [], $returnEObj = false)
    {
        try {
            $this->data = $data;
            $this->entityObject = $this->obtainEObj($criteria);
            foreach ($this->mergeParams($data) as $k => $v) {
                $setMethod = 'set' . ucfirst(str_replace(' ', '', lcfirst(ucwords(str_replace('_', ' ', $k)))));
                if (method_exists($this->entityObject, $setMethod)) {
                    $this->entityObject->$setMethod($v);
                }
            }
            return $returnEObj ? $this->entityObject : $this;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 获取实体对象
     *
     * @throws EntityNotFoundException
     * @param array $criteria
     * @return Object
     */
    private function obtainEObj(array $criteria = [])
    {
        $entityName = $this->getProperty('table');
        $entityFolder = $this->getProperty('entityFolder');
        $dbName = $this->getProperty('schema');
        $entityNamespace = $this->getProperty('entityNamespace');
        $repositoryNamespace = $this->getProperty('repositoryNamespace');
        if ($criteria) {
            $repository = $this->app->repository($entityName, $dbName, $entityFolder, $entityNamespace, $repositoryNamespace);
            $entityObject = $repository->findOneBy($criteria);
        } else {
            $entityObject = $this->app->entity($entityName, $entityNamespace);
        }
        if (!$entityObject) {
            throw new EntityNotFoundException('没有可用实体对象!');
        }
        return $entityObject;
    }

    /**
     * 验证数据或者对象
     *
     * @param array $rules 验证规则
     * @param int $type 验证类型
     * @return bool
     * @throws \Exception
     */
    protected function validate(array $rules = [], $type = Constants::MODEL_OBJECT)
    {
        $method = [Constants::MODEL_FIELD => 'verifyField', Constants::MODEL_OBJECT => 'verifyObject'];
        try {
            $validator = $this->app->component('biz_validator');
            $validateData = $type === Constants::MODEL_OBJECT ? $this->entityObject : $this->mergeParams($this->data);
            $ret = $validator->$method[$type]($validateData, $rules);
            if (!$ret) {
                throw new EntityValidateErrorException('数据验证失败!');
            }
            return $this->entityObject;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 合并请求参数数据与自定义参数数据
     *
     * @param array $data 需要验证的数据
     * @param array $mappingField 映射字段
     * @return array
     * @throws \Exception
     */
    protected function mergeParams(array $data = [], array $mappingField = [])
    {
        $data = array_merge($this->app->component('request')->getParams(), $data);
        if ($mappingField) {
            $combineData = [];
            foreach ($data as $key => $value) {
                isset($mappingField[$key]) ? $combineData[$mappingField[$key]] = $value : $combineData[$key] = $value;
            }
            return $combineData;
        }
        return $data;
    }

    /**
     * 切换数据库的链接
     *
     * @param int $shardId
     * @return boolean
     * @throws \Exception
     */
    protected function switchConnect($shardId)
    {
        try {
            return $this->em->getConnection()->connect((int)$shardId);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 获取PoolingShardManager实例，用于全局查询
     *
     * @return PoolingShardManager|null
     */
    protected function sharedManager()
    {
        if ($this->em) {
            return new PoolingShardManager($this->em->getConnection());
        }
        return null;
    }

    /**
     * 给对象新增属性
     *
     * @param $propertyName
     * @param $value
     * @return $this
     */
    protected function setProperty($propertyName, $value)
    {
        $this->$propertyName = $value;
        return $this;
    }

    /**
     * 获取对象属性
     *
     * @param $propertyName
     * @return mixed
     */
    protected function getProperty($propertyName)
    {
        if (isset($this->$propertyName)) {
            return $this->$propertyName;
        }
        return null;
    }

}