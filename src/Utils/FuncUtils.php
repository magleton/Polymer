<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 2016/10/16
 * Time: 18:40
 */

namespace Polymer\Utils;

use DI\DependencyException;
use DI\NotFoundException;
use Exception;
use Polymer\Boot\Application;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class FuncUtils
{
    /**
     * 生成用户的salt加密串
     *
     * @param int $len
     * @param int $type (1=>数字 , 2=>字母 , 3=>混合)
     *
     * @return string
     * @throws Exception
     */
    public static function generateSalt(int $len = 32, int $type = 3): string
    {
        $arr[1] = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        $arr[2] = ['b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'z'];
        $arr[3] = ['b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'z', '2', '3', '4', '5', '6', '7', '8', '9'];
        $word = '';
        $cnt = count($arr[$type]) - 1;
        mt_srand((float)microtime() * 1000000);
        shuffle($arr[$type]);
        for ($i = 0; $i < $len; $i++) {
            $word .= $arr[$type][random_int(0, $cnt)];
        }
        if (strlen($word) > $len) {
            $word = substr($word, 0, $len);
        }
        return $word;
    }

    /**
     * 用于Symfony的Callback验证器
     *
     * @param                           $object
     * @param ExecutionContextInterface $context
     * @param                           $payload
     */
    public static function validate($object, ExecutionContextInterface $context, $payload): void
    {
        $context->buildViolation('This name sounds totally fake!')->atPath('firstName')->addViolation();
    }

    /**
     * 将实体对象转换为数组
     *
     * @param mixed $entity 实体对象
     * @return array|object|scalar
     * @throws DependencyException
     * @throws ExceptionInterface
     * @throws NotFoundException
     */
    public static function entityToArray($entity)
    {
        return self::getSerializer()->normalize($entity);
    }

    /**
     *  获取序列化器
     * @return Serializer
     *
     * @throws DependencyException
     * @throws NotFoundException
     * @version  2018年11月12日
     * @author   zj chen <britton@126.com>
     * @license  PHP Version 7.x.x {@link http://www.php.net/license/3_0.txt}
     */
    private static function getSerializer(): Serializer
    {
        return Application::getInstance()->get(Serializer::class);
    }

    /**
     *  对象转JSON
     * @param       $entity
     * @return string
     *
     * @throws DependencyException
     * @throws NotFoundException
     * @version  2018年11月12日
     * @author   zj chen <britton@126.com>
     * @license  PHP Version 7.x.x {@link http://www.php.net/license/3_0.txt}
     */
    public static function entityToJson($entity): string
    {
        return self::getSerializer()->serialize($entity, 'json');
    }

    /**
     * 对象转XML
     * @param       $entity
     * @return string
     *
     * @throws DependencyException
     * @throws NotFoundException
     * @version  2018年11月12日
     * @author   zj chen <britton@126.com>
     * @license  PHP Version 7.x.x {@link http://www.php.net/license/3_0.txt}
     */
    public static function entityToXML($entity): string
    {
        return self::getSerializer()->serialize($entity, 'xml');
    }
}