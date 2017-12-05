<?php
namespace Remind\Api\ReMind\Resource;

use Remind\Api\ReMind\Base\Request;
use Remind\Api\ReMind\Base\Response;

abstract class Base {

    //增强IDE兼容性, 方便依赖互相调用
    // auth 要依赖debug, debug依赖request
    //use Auth;

    /**
     * @var Response
     */
    protected $response;

    protected $request;

    protected $config;

    protected $_param;

    protected $userInfo;

    protected $debug;

    protected $auth;

    /**
     * 初始化
     * @return mixed
     */
    abstract function init();


    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
    }


    /**
     */
    public function identify()
    {
    }

    /**
     * @deprecated 应该已经停用了
     */
    public function validate()
    {
    }

    protected function debug()
    {
    }

    /**
     * 获取
     */
    public function auth()
    {
    }

    //分发参数
    public function validateParam() {
        return true;
    }


    public function setParamConfig()
    {
        return [];
    }

    public function checkParam($paramKey)
    {
        return true;
    }
}
