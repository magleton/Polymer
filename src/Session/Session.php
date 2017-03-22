<?php
/**
 * Created by PhpStorm.
 * User: macro
 * Date: 16-9-8
 * Time: 上午8:27
 */

namespace Polymer\Session;


final class Session
{
    /**
     * 设置session的值
     *
     * @var array
     */
    protected $options = [
        'name' => 'macro_chen',
        'lifetime' => 7200,
        'path' => null,
        'domain' => null,
        'secure' => false,
        'httponly' => true,
        'cache_limiter' => 'nocache',
    ];

    /**
     * Session constructor.
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        $keys = array_keys($this->options);
        foreach ($keys as $key) {
            if (array_key_exists($key, $options)) {
                $this->options[$key] = $options[$key];
            }
        }
    }

    /**
     * 启动session
     */
    public function start()
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            return;
        }
        $options = $this->options;
        $current = session_get_cookie_params();
        $lifetime = (int)($options['lifetime'] ?: $current['lifetime']);
        $path = $options['path'] ?: $current['path'];
        $domain = $options['domain'] ?: $current['domain'];
        $secure = (bool)$options['secure'];
        $httponly = (bool)$options['httponly'];
        session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly);
        session_name($options['name']);
        session_cache_limiter($options['cache_limiter']);
        session_start();
    }

    /**
     * 重新生成session_id
     */
    public function regenerate()
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    /**
     * 销毁session
     */
    public function destroy()
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    /**
     * 通过key获取session的值
     *
     * @param $key
     * @param null $default
     * @return null
     */
    public function get($key, $default = null)
    {
        if (array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        }
        return $default;
    }

    /**
     * 向session设置值
     *
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * 删除指定的key
     *
     * @param $key
     */
    public function delete($key)
    {
        if (array_key_exists($key, $_SESSION)) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * 清空session
     */
    public function clearAll()
    {
        $_SESSION = [];
    }

    /**
     * is utilized for reading data from inaccessible members.
     *
     * @param $name string
     * @return mixed
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
     */
    function __get($name)
    {
        return $this->get($name);
    }

    /**
     * run when writing data to inaccessible members.
     *
     * @param $name string
     * @param $value mixed
     * @return void
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
     */
    function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * is triggered by calling isset() or empty() on inaccessible members.
     *
     * @param $name string
     * @return bool
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
     */
    function __isset($name)
    {
        return array_key_exists($name, $_SESSION);
    }

    /**
     * is invoked when unset() is used on inaccessible members.
     *
     * @param $name string
     * @return void
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
     */
    function __unset($name)
    {
        $this->delete($name);
    }
}