<?php
/**
 */

namespace Remind\Api\ReMind\Base;

use Remind\Api\Base\Util\ParamHelper;

class Request
{

    /**
     * @var
     */
    protected   $request=[];

    /**
     * @var
     */
    protected   $header = [];


    /**
     * @var
     */
    protected   $cookie = [];


    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->formatRequest();
        $this->formatHeaders();
        $this->formatCookie();
    }

    /**
     * 格式化
     */
    public function formatRequest()
    {
        $requestInfo  = $this->method() == 'get' ? $_GET : $_POST;

        $this->formatParams($requestInfo, $this->request);

    }

    /**
     * 格式化header头
     */
    public function formatHeaders()
    {
        if (function_exists('apache_request_headers')) {
            $headers        = apache_request_headers();
        } else {
            $headers        = $_SERVER;
        }

        $this->formatParams($headers, $this->header);
    }


    /**
     * 格式化cookie
     */
    public function formatCookie()
    {
        if (is_array($_COOKIE) && !empty($_COOKIE)) {
            $this->formatParams($_COOKIE, $this->cookie);
        }
    }

    /**
     * 格式化
     */
    private function formatParams($params, &$data)
    {

        if (is_array($params)) {
            foreach ($params as $key => $val) {
                if (!is_array($val)) {
                    // 转义
                    if (!get_magic_quotes_gpc()) {
                        $data[$key]    = addslashes($val);
                    }
                    $data[$key]    = htmlspecialchars(strip_tags($val), ENT_QUOTES);
                } else {
                     $this->formatParams($val, $data[$key]);
                }
            }
        }

    }


    /**
     * @return mixed
     */
    public function all()
    {
        return $this->request;
    }

    /**
     * 获取请求的参数
     * @param string $key
     * @param string $default
     * @return string
     */
    public function get($key= '', $default = '')
    {
        $this->request  = array_merge($this->request, ParamHelper::$param['get']);

        if (array_key_exists($key, $this->request)) {
            return isset($this->request[$key]) ? $this->request[$key] : '';
        }

        return $default;
    }


    /**
     * 请求方式
     * @return string
     */
    public function method()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }


    /**
     * 获取头部信息
     * @param string $name
     * @param string $default
     */
    public function header($key='', $default= '')
    {
        if (array_key_exists($key, $this->header)) {
            return isset($this->header[$key]) ? $this->header[$key] : '';
        }

        return $default;

    }


    /**
     * 获取cookie中的
     * @param string $name
     * @param string $default
     */
    public function cookie($name='', $default='')
    {
        if (array_key_exists($name, $this->cookie)) {
            return isset($this->cookie[$name]) ? $this->cookie[$name] : '';
        }

        return $default;

    }


    /**
     * 返回数据
     */
    private function input()
    {

    }
}