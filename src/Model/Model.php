<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 2016/10/26
 * Time: 19:42
 */

namespace Polymer\Model;

use DI\Annotation\Inject;
use DI\Container;
use Doctrine\Common\Cache\Cache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Exception;
use Exeception;
use Polymer\Boot\Application;
use Polymer\Exceptions\EntityValidateErrorException;
use Polymer\Exceptions\ModelInstanceErrorException;
use Polymer\Support\Collection;
use Polymer\Validator\GXValidator;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Validator\Validator\RecursiveValidator;

class Model
{
    /**
     * 应用APP
     * @Inject
     *
     * @var Application
     */
    protected Application $application;

    /**
     * 验证组件
     * @Inject
     * @var RecursiveValidator
     */
    protected RecursiveValidator $validator;

    /**
     * 自定义
     * @Inject
     * @var GXValidator
     */
    protected GXValidator $gxValidator;

    /**
     * @Inject
     * @var Container
     */
    protected Container $diContainer;

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
     *
     * @throws ModelInstanceErrorException
     */
    public function __construct()
    {
        try {
            $schema = $this->getProperty('schema') ?: 'db1';
            $cache = new ArrayAdapter();
            $this->em = Application::getInstance()->getEntityManager($schema);
            if ($cache instanceof Cache) {
                $this->em->getConfiguration()->setMetadataCache($cache);
                $this->em->getConfiguration()->setQueryCacheImpl($cache);
                $this->em->getConfiguration()->setResultCacheImpl($cache);
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
    protected function getProperty($propertyName)
    {
        return $this->$propertyName ?? null;
    }

    /**
     * 生成数据库表的实体对象
     *
     * @param string $entityName 映射的实体全类名
     * @param array $data 自定义数据
     * @param array $criteria 获取对象的条件(用于更新数据)
     * @return Object|null
     * @throws EntityNotFoundException
     */
    protected function make(string $entityName, array $data = [], array $criteria = []): ?object
    {
        try {
            $this->entityObject = $this->obtainEObj($entityName, $criteria);
            $this->customerData = $data;
            foreach ($this->mergeParams($data) as $k => $v) {
                $setMethod = 'set' . getInflector()->classify($k);
                if (method_exists($this->entityObject, $setMethod)) {
                    $this->entityObject->$setMethod($v);
                }
            }
            return $this->entityObject;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 获取实体对象
     *
     * @param string $entityName
     * @param array $criteria
     * @return Object
     * @throws EntityNotFoundException
     */
    private function obtainEObj(string $entityName, array $criteria = []): object
    {
        if ($criteria) {
            $repository = $this->em->getRepository($entityName);
            $entityObject = $repository->findOneBy($criteria);
        } else {
            $entityObject = new $entityName();
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
     * @return array|object|null
     * @throws Exception
     */
    protected function validate(array $rules = [], array $groups = null, bool $returnErr = false)
    {
        $rules = $rules ?: $this->getProperty('rules');
        if ($rules) {
            $errorData = [];
            try {
                $validateResult = $this->gxValidator->validateObject($this->entityObject, $rules, $groups);
                if (count($validateResult)) {
                    foreach ($validateResult as $error) {
                        $tmpMappingField = array_flip($this->mappingField);
                        $propertyName = $error->getPropertyPath();
                        if (isset($tmpMappingField[$propertyName]) && array_key_exists($tmpMappingField[$propertyName], $this->customerData)) {
                            $propertyName = $tmpMappingField[$propertyName];
                        }
                        $errorData[$propertyName] = $error->getMessage();
                    }
                    if ($returnErr) {
                        return $errorData;
                    }
                    Application::getInstance()->get(Collection::class)->set($this->getProperty('table'), $errorData);
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
