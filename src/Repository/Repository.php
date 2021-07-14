<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 16-12-12
 * Time: 上午8:55
 */

namespace Polymer\Repository;

use DI\Annotation\Inject;
use DI\Container;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Exception;
use Polymer\Boot\Application;
use Polymer\Exceptions\PresenterException;
use Polymer\Providers\GXValidatorProvider;

class Repository extends EntityRepository
{
    /**
     * View presenter instance
     *
     * @var mixed
     */
    protected $presenterInstance;

    /**
     * 全局应用实例
     * @Inject
     * @var ?Application
     */
    protected Application $application;

    /**
     * @var Container
     */
    protected Container $diContainer;

    /**
     * 验证规则
     *
     * @var array
     */
    protected array $rules = [];

    /**
     * Initializes a new <tt>EntityRepository</tt>.
     *
     * @param EntityManager $em The EntityManager to use.
     * @param Mapping\ClassMetadata $class The class descriptor.
     */
    public function __construct(EntityManager $em, Mapping\ClassMetadata $class)
    {
        parent::__construct($em, $class);
        $this->application = app();
    }

    /**
     * @return Container
     */
    public function getDiContainer(): Container
    {
        return $this->application->getDiContainer();
    }

    /**
     * 验证查询字段的值
     *
     * @param array $data 需要验证的数据
     * @param array $rules 验证数据的规则
     * @param array $groups 验证组
     * @param string $key 存储错误信息的键
     * @return $this
     * @throws Exception
     */
    public function validate(array $data = [], array $rules = [], array $groups = [], string $key = 'error'): self
    {
        try {
            $rules = $rules ?: $this->getProperty('rules');
            $this->diContainer->get(GXValidatorProvider::class)->validateField($data, $rules, $groups, $key);
        } catch (Exception $e) {
            throw $e;
        }
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
        return $this->$propertyName ?? null;
    }

    /**
     * Prepare a new or cached presenter instance
     *
     * @param $entity
     * @return mixed
     * @throws PresenterException
     */
    public function present($entity)
    {
        if (!$this->getProperty('presenter') || !class_exists($this->getProperty('presenter'))) {
            throw new PresenterException('Please set the $presenter property to your presenter path.');
        }
        if (!$this->presenterInstance) {
            $cls = $this->getProperty('presenter');
            $this->presenterInstance = new $cls($entity);
        }
        return $this->presenterInstance;
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
