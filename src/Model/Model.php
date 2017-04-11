<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 2016/10/26
 * Time: 19:42
 */
namespace Polymer\Model;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Inflector\Inflector;
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
     * 需要映射的字段
     *
     * @var array
     */
    protected $mappingField = [];

    /**
     * 需要排除掉的字段
     *
     * @var array
     */
    protected $excludeField = [];

    /**
     * EntityManager实例
     *
     * @var EntityManager
     */
    protected $em = null;

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
                $cache = $this->getProperty('cache') ?: null;
                $this->em = $this->app->db($schema);
                if ($cache instanceof Cache) {
                    $this->em->getConfiguration()->setMetadataCacheImpl($cache);
                    $this->em->getConfiguration()->setQueryCacheImpl($cache);
                    $this->em->getConfiguration()->setResultCacheImpl($cache);
                }
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
     * @return mixed
     * @throws \Exception
     */
    protected function make(array $data = [], array $criteria = [], $returnEObj = false)
    {
        try {
            $this->entityObject = $this->obtainEObj($criteria);
            foreach ($this->mergeParams($data) as $k => $v) {
                $setMethod = 'set' . Inflector::classify($k);
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
     * @throws EntityNotFoundException | \Exception
     * @param array $criteria
     * @return Object
     */
    private function obtainEObj(array $criteria = [])
    {
        $entityName = $this->getProperty('table');
        $entityFolder = $this->getProperty('entityFolder');
        $schema = $this->getProperty('schema');
        $entityNamespace = $this->getProperty('entityNamespace');
        $repositoryNamespace = $this->getProperty('repositoryNamespace');
        if ($criteria) {
            $repository = $this->app->repository($entityName, $schema, $entityFolder, $entityNamespace,
                $repositoryNamespace);
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
     * @param array $groups 验证组
     * @return bool
     * @throws \Exception
     */
    protected function validate(array $rules = [], array $groups = null)
    {
        $rules = $rules ?: $this->getProperty('rules');
        if ($rules) {
            try {
                $validator = $this->app->component('biz_validator');
                $validateResult = $validator->verifyObject($this->entityObject, $rules, $groups);
                if (count($validateResult)) {
                    foreach ($validateResult as $error) {
                        $tmpMappingField = array_flip($this->mappingField);
                        $errorData[$tmpMappingField[$error->getPropertyPath()]] = $error->getMessage();
                    }
                    $this->app->component('error_collection')->set($this->getProperty('table'), $errorData);
                    throw new EntityValidateErrorException('数据验证失败!');
                }
                return $this->entityObject;
            } catch (\Exception $e) {
                throw $e;
            }
        }
        return $this->entityObject;
    }

    /**
     * 合并请求参数数据与自定义参数数据
     *
     * @param array $data 需要验证的数据
     * @return array
     * @throws \Exception
     */
    protected function mergeParams(array $data = [])
    {
        $excludeField = $this->getProperty('excludeField');
        $mappingField = $this->getProperty('mappingField');
        $data = array_merge($this->app->component('request')->getParams(), $data);
        $excludeField && $data = array_diff_key($data, array_flip($excludeField));
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
