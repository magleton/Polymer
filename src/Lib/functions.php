<?php
use \Polymer\Boot\Application;

if (!function_exists('convert')) {
    /**
     * 字节转换
     *
     * @param $size
     * @return string
     */
    function convert($size)
    {
        $unit = array('b', 'KB', 'MB', 'GB', 'TB', 'PB');
        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
    }
}

if (!function_exists('urlSafeBase64Code')) {
    /**
     * URL参数安全base64
     *
     * @author macro chen <macro_fengye@163.com>
     * @param string $string
     * @param string $operation ENCODE|DECODE
     * @return string
     */
    function urlSafeBase64Code($string, $operation = 'ENCODE')
    {
        $searchKws = array('+', '/', '=');
        $replaceKws = array('-', '_', '');
        if ($operation == 'DECODE') {
            $ret = base64_decode(str_replace($replaceKws, $searchKws, $string));
        } else {
            $ret = str_replace($searchKws, $replaceKws, base64_encode($string));
        }
        return $ret;
    }
}

if (!function_exists('getIP')) {
    /**
     * 获取客户端真实IP
     *
     * @author macro chen <macro_fengye@163.com>
     */
    function getIP()
    {
        if (isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP'])
            $IP = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'])
            $IP = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']) && $_SERVER['HTTP_X_FORWARDED'])
            $IP = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']) && $_SERVER['HTTP_FORWARDED_FOR'])
            $IP = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']) && $_SERVER['HTTP_FORWARDED'])
            $IP = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'])
            $IP = $_SERVER['REMOTE_ADDR'];
        else
            $IP = '0.0.0.0';
        return $IP;
    }
}

if (!function_exists('checkFromIPValidity')) {
    /**
     * 验证来源IP合法性，是否在允许IP列表内
     * checkFromIpValidity('127.0.0.1', array('127.0.0.1', '192.168.0.'))
     * 允许IP列表支持不完全匹配
     *
     * @author fengxu
     * @param string $fromIp 来源IP
     * @param array $allowIps 允许IP列表
     * @return boolean
     */
    function checkFromIPValidity($fromIp = '', array $allowIps = array())
    {
        $fromIp = $fromIp ? $fromIp : getIp();
        $res = false;
        if ($allowIps) {
            foreach ($allowIps as $allowIp) {
                if (!strncmp($fromIp, $allowIp, strlen($allowIp))) {
                    $res = true;
                    break;
                }
            }
        }
        return $res;
    }
}


if (!function_exists('verifyPwdComplexity')) {
    /**
     *
     * 验证密码复杂度
     *
     * @author fengxu
     * @param string $password
     * @param integer $minPwdLen 密码最小长度
     * @return integer 密码复杂度等级，安位求或
     */
    function verifyPwdComplexity($password, $minPwdLen = 6)
    {
        $complexity = 0;
        if (strlen($password) >= (int)$minPwdLen) {
            $complexity = 1;
            if (preg_match('@[a-zA-Z]+@', $password)) {
                $complexity |= 2;
            }
            if (preg_match('@[0-9]+@', $password)) {
                $complexity |= 4;
            }
            if (preg_match('@[A-Z]+@', $password)) {
                $complexity |= 8;
            }
            if (preg_match('@[\W]+@', $password)) { // 字母数字外的其他字符
                $complexity |= 16;
            }
        }
        return $complexity;
    }
}

if (!function_exists('filterInvisibleCharacter')) {
    /**
     * 过滤不可见(不可打印)的字符
     *
     * @param $str
     * @return mixed
     */
    function filterInvisibleCharacter($str)
    {
        return preg_replace('/[^[:print:]]/', '', $str);
    }
}