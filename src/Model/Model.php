<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 2016/10/26
 * Time: 19:42
 */

namespace Polymer\Model;

use Doctrine\Common\Cache\Cache;
use Doctrine\DBAL\Sharding\PoolingShardManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Exception;
use Polymer\Boot\Application;
use Polymer\Exceptions\EntityValidateErrorException;
use Polymer\Exceptions\ModelInstanceErrorException;
use Symfony\Component\Validator\Validator\RecursiveValidator;

class Model
{
    /**
     * 应用APP
     *
     * @var Application|null
     */
    protected ?Application $application = null;

    /**
     * 验证组件
     *
     * @var RecursiveValidator|null
     */
    protected ?RecursiveValidator $validator = null;

    /**
     * 要验证的实体对象
     *
     * @var object|null
     */
    protected ?object $entityObject = null;

    /**
     * 需要映射的字段
     *
     * @var array
     */
    protected array $mappingField = [];

    /**
     * 需要排除掉的字段
     *
     * @var array
     */
    protected array $excludeField = [];

    /**
     * EntityManager实例
     *
     * @var EntityManager|null
     */
    protected ?EntityManager $em = null;

    /**
     * 保存自定义数据,供验证出错时显示提示信息用
     *
     * @var array
     */
    private array $customerData = [];

    /**
     * 模型构造函数
     * @param array $params
     * @throws ModelInstanceErrorException
     */
    public function __construct(array $params = [])
    {
        try {
            $this->application = app();
            $schema = $params['schema'] ?? $this->getProperty('schema');
            if ($schema) {
                $cache = $this->getProperty('cache') ?: null;
                $this->em = $this->application->db($schema);
                if ($cache instanceof Cache) {
                    $this->em->getConfiguration()->setMetadataCacheImpl($cache);
                    $this->em->getConfiguration()->setQueryCacheImpl($cache);
                    $this->em->getConfiguration()->setResultCacheImpl($cache);
                }
            }
        } catch (Exception $e) {
            throw new ModelInstanceErrorException('模型实例化错误' . $e->getMessage());
        }
    }

    /**
     * 获取对象属性
     *
     * @param $propertyName
     * @return mixed
     */
    protected function getProperty($propertyName): mixed
    {
        return $this->$propertyName ?? null;
    }

    /**
     * 生成数据库表的实体对象
     *
     * @param array $data 自定义数据
     * @param array $criteria 获取对象的条件(用于更新数据)
     * @param bool $returnEObj 是否返回实体对象
     * @return mixed
     * @throws Exception
     */
    protected function make(array $data = [], array $criteria = [], bool $returnEObj = false): mixed
    {
        try {
            $this->entityObject = $this->obtainEObj($criteria);
            $this->customerData = $data;
            foreach ($this->mergeParams($data) as $k => $v) {
                $setMethod = 'set' . Application::getInstance()->getInflector()->classify($k);
                if (method_exists($this->entityObject, $setMethod)) {
                    $this->entityObject->$setMethod($v);
                }
            }
            return $returnEObj ? $this->entityObject : $this;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 获取实体对象
     *
     * @param array $criteria
     * @return Object
     * @throws EntityNotFoundException | Exception
     */
    private function obtainEObj(array $criteria = []): object
    {
        $entityName = $this->getProperty('table');
        $entityFolder = $this->getProperty('entityFolder');
        $schema = $this->getProperty('schema');
        $entityNamespace = $this->getProperty('entityNamespace');
        $repositoryNamespace = $this->getProperty('repositoryNamespace');
        if ($criteria) {
            $repository = $this->application->repository($entityName, $schema, $entityFolder, $entityNamespace,
                $repositoryNamespace);
            $entityObject = $repository->findOneBy($criteria);
        } else {
            $entityObject = $this->application->entity($entityName, $entityNamespace);
        }
        if (!$entityObject) {
            throw new EntityNotFoundException('没有可用实体对象!');
        }
        return $entityObject;
    }

    /**
     * 合并请求参数数据与自定义参数数据
     *
     * @param array $data 需要验证的数据
     * @return array
     * @throws Exception
     */
    protected function mergeParams(array $data = []): array
    {
        $excludeField = $this->getProperty('excludeField');
        $mappingField = $this->getProperty('mappingField');
        $data = array_merge($this->application->component('request')->getParams(), $data);
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
     * 验证数据或者对象
     *
     * @param array $rules 验证规则
     * @param array|null $groups 验证组
     * @param bool $returnErr 是否返回错误信息
     * @return object|bool|array|null
     * @throws EntityValidateErrorException
     */
    protected function validate(array $rules = [], array $groups = null, bool $returnErr = false): object|bool|array|null
    {
        $rules = $rules ?: $this->getProperty('rules');
        if ($rules) {
            $errorData = [];
            try {
                $validator = $this->application->component('biz_validator');
                $validateResult = $validator->validateObject($this->entityObject, $rules, $groups);
                if (count($validateResult)) {
                    foreach ($validateResult as $error) {
                        $tmpMappingField = array_flip($this->mappingField);
                        $propertyName = $error->getPropertyPath();
                        if (isset($tmpMappingField[$propertyName]) && array_key_exists($tmpMappingField[$propertyName], array_merge($this->application->component('request')->getParams(), $this->customerData))) {
                            $propertyName = $tmpMappingField[$propertyName];
                        }
                        $errorData[$propertyName] = $error->getMessage();
                    }
                    if ($returnErr) {
                        return $errorData;
                    }
                    $this->application->component('error_collection')->set($this->getProperty('table'), $errorData);
                    throw new EntityValidateErrorException('数据验证失败!');
                }
                return $this->entityObject;
            } catch (Exception $e) {
                throw $e;
            }
        }
        return $this->entityObject;
    }

    /**
     * 切换数据库的链接
     *
     * @param int $shardId
     * @return bool|null
     * @throws Exception
     */
    protected function switchConnect(int $shardId): ?bool
    {
        try {
            return $this->em->getConnection()->connect($shardId);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 获取PoolingShardManager实例，用于全局查询
     *
     * @return PoolingShardManager|null
     */
    protected function sharedManager(): ?PoolingShardManager
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
    protected function setProperty($propertyName, $value): self
    {
        $this->$propertyName = $value;
        return $this;
    }
}
