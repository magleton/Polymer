<?php
/**
 * Created by PhpStorm.
 * User: macro
 * Date: 17-1-4
 * Time: 上午10:46
 */

namespace Polymer\Utils;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\Configuration;
use Gedmo\DoctrineExtensions;
use Symfony\Component\Yaml\Parser;

class DoctrineExtConfigLoader
{
    const MYSQL = 'mysql';
    const ORACLE = 'oracle';
    const POSTGRES = 'postgres';
    const SQLITE = 'sqlite';

    /**
     * 导出自定义的函数
     * @param Configuration $configuration
     * @param string $database
     */
    public static function loadFunctionNode(Configuration $configuration, $database)
    {
        $parser = new Parser();
        // Load the corresponding config file.
        $configFiles = ROOT_PATH . '/vendor/beberlei/DoctrineExtensions/config/' . $database . '.yml';
        $config = $parser->parse(file_get_contents($configFiles));
        $parsed = $config['doctrine']['orm']['dql'];

        // Load the existing function classes.
        if (array_key_exists('datetime_functions', $parsed)) {
            foreach ($parsed['datetime_functions'] as $key => $value) {
                $configuration->addCustomDatetimeFunction(strtoupper($key), $value);
            }
        }
        if (array_key_exists('numeric_functions', $parsed)) {
            foreach ($parsed['numeric_functions'] as $key => $value) {
                $configuration->addCustomNumericFunction(strtoupper($key), $value);
            }
        }
        if (array_key_exists('string_functions', $parsed)) {
            foreach ($parsed['string_functions'] as $key => $value) {
                $configuration->addCustomStringFunction(strtoupper($key), $value);
            }
        }
    }

    /**
     * 导入Doctrine的扩展组件
     */
    public static function load()
    {
        $reader = new AnnotationReader();
        AnnotationReader::addGlobalIgnoredName('dummy');
        $driverChain = new MappingDriverChain();
        DoctrineExtensions::registerAbstractMappingIntoDriverChainORM($driverChain, $reader);
    }
}