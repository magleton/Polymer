<?php
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
        if ($operation === 'DECODE') {
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
        $IP = '0.0.0.0';
        if (isset($_SERVER['HTTP_CDN_SRC_IP']) && $_SERVER['HTTP_CDN_SRC_IP']) {
            $IP = $_SERVER['HTTP_CDN_SRC_IP'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP']) {
            $IP = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
            $IP = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED']) && $_SERVER['HTTP_X_FORWARDED']) {
            $IP = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR']) && $_SERVER['HTTP_FORWARDED_FOR']) {
            $IP = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED']) && $_SERVER['HTTP_FORWARDED']) {
            $IP = $_SERVER['HTTP_FORWARDED'];
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR']) {
            $IP = $_SERVER['REMOTE_ADDR'];
        }
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
        $fromIp = $fromIp ?: getIP();
        $res = false;
        if ($allowIps) {
            foreach ($allowIps as $allowIp) {
                if (0 === strpos($fromIp, $allowIp)) {
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
            if (preg_match('@[\d]+@', $password)) {
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

if (!function_exists('authCode')) {
    /**
     * 加解密数据
     *
     * @param $string
     * @param string $operation
     * @param string $key
     * @param int $expiry
     * @return string
     */
    function authCode($string, $operation = 'DECODE', $key = '', $expiry = 0)
    {
        $cKeyLength = 4;
        $key = md5($key ?: 'polymerKey');
        $keyA = md5(substr($key, 0, 16));
        $keyB = md5(substr($key, 16, 16));
        $keyC = '';
        if ($cKeyLength) {
            if ($operation === 'DECODE') {
                $keyC = substr($string, 0, $cKeyLength);
            } else {
                $keyC = substr(md5(microtime()), -$cKeyLength);
            }
        }
        $cryptKey = $keyA . md5($keyA . $keyC);
        $keyLength = strlen($cryptKey);
        $string = $operation === 'DECODE' ? base64_decode(substr($string, $cKeyLength)) : sprintf('%010d',$expiry ? $expiry + time() : 0) . substr(md5($string . $keyB), 0, 16) . $string;
        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);
        $rndKey = array();
        for ($i = 0; $i <= 255; $i++) {
            $rndKey[$i] = ord($cryptKey[$i % $keyLength]);
        }
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndKey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if ($operation === 'DECODE') {
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && 0 === strcmp(substr($result, 10, 16), substr(md5(substr($result, 26) . $keyB), 0, 16))) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyC . str_replace('=', '', base64_encode($result));
        }
    }
}