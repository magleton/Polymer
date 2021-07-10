<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 16-10-26
 * Time: 上午11:44
 */

namespace Polymer\Middleware;

use Closure;
use Exception;
use Polymer\Utils\Constants;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class IpFilterMiddleware
{
    /**
     * @var array
     */
    protected array $addresses = [];

    /**
     * @var int|null
     */
    protected ?int $mode = null;

    /**
     * @var bool
     */
    protected boolean $allowed;

    /**
     * @var Closure|null
     */
    protected ?Closure $handler = null;

    /**
     * @var array
     */
    protected array $patterns = [];

    /**
     * IpFilterMiddleware constructor.
     *
     * @param array $addresses
     * @param int $mode
     */
    public function __construct(array $addresses = [], int $mode = Constants::ALLOW)
    {
        foreach ($addresses as $address) {
            if (is_array($address)) {
                $this->addIpRange($address[0], $address[1]);
            } else {
                $this->addIp($address);
            }
        }
        $this->patterns = $addresses;
        $this->mode = $mode;
        $this->handler = static function (ServerRequestInterface $request, ResponseInterface $response) {
            try {
                $response = $response->withStatus(403);
                $response->getBody()->write(' 403 Forbidden');
                return $response;
            } catch (Exception $e) {
                return null;
            }
        };
    }

    /**
     * 添加IP段
     *
     * @param $start
     * @param $end
     * @return $this
     */
    public function addIpRange($start, $end): IpFilterMiddleware
    {
        foreach (range(ip2long($start), ip2long($end)) as $address) {
            $this->addresses[] = $address;
        }
        return $this;
    }

    /**
     * 添加IP地址
     *
     * @param $ip
     * @return $this
     */
    public function addIp($ip): IpFilterMiddleware
    {
        $this->addresses[] = ip2long($ip);
        return $this;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param $next
     * @return mixed|ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        if ($this->mode === Constants::ALLOW) {
            $this->allowed = $this->allow($request);
        }
        if ($this->mode === Constants::DENY) {
            $this->allowed = $this->deny($request);
        }
        if (!$this->allowed) {
            $handler = $this->handler;
            return $handler($request, $response);
        }
        return $next($request, $response);
    }

    /**
     * 允许访问的请求
     *
     * @param ServerRequestInterface $request
     * @return bool
     */
    public function allow(ServerRequestInterface $request): bool
    {
        $clientAddress = ip2long($request->getHeader('REMOTE_ADDR')[0]);
        if (in_array($clientAddress, $this->addresses, true)) {
            return true;
        }
        return false;
    }

    /**
     * 拒绝访问的请求
     *
     * @param ServerRequestInterface $request
     * @return bool
     */
    public function deny(ServerRequestInterface $request): bool
    {
        $clientAddress = ip2long($request->getHeader('REMOTE_ADDR')[0]);
        if (in_array($clientAddress, $this->addresses, true)) {
            return false;
        }
        return true;
    }

    /**
     * 设置处理器
     *
     * @param $handler
     */
    public function setHandler($handler): void
    {
        $this->handler = $handler;
    }
}
