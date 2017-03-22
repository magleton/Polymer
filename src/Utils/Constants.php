<?php
/**
 * Created by PhpStorm.
 * User: macro
 * Date: 16-10-19
 * Time: 上午10:10
 */

namespace Polymer\Utils;


class Constants
{
    /**
     * 日志记录相关
     */
    const LOG = 'log';    // 一般日志
    const ERROR = 'error';  // 错误日志
    const INFO = 'info';   // 信息日志
    const WARN = 'warn';   // 警告日志
    const TRACE = 'trace';  // 输入日志同时会打出调用栈
    const ALERT = 'alert';  // 将日志以alert方式弹出
    const LOG_CSS = 'log';    // 自定义日志的样式，第三个参数为css样式

    /**
     * 数据存储相关
     */
    const ENTITY = 'entityManager';
    const REDIS = 'redis';
    const MEMCACHE = 'memcache';
    const MEMCACHED = 'memcached';

    /**
     * 与IP限制相关的常量
     */
    const DENY = 0;
    const ALLOW = 1;

    /**
     * 模型对象要验证的目标
     */
    const MODEL_FIELD = 1;   //验证字段
    const MODEL_OBJECT = 2;  //验证对象
}