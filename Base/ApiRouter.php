<?php
namespace Remind\Api\Base;

use Remind\Api\Base\Util\ParamHelper;

class ApiRouter {
    public function dispatch () {
        //加载通用错误配置信息
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $baseDispatcher = \FastRoute\simpleDispatcher(function(\FastRoute\RouteCollector $r) {
            $r->addRoute(array('GET', 'POST', 'PUT', 'DELETE'), '/{apiDir}/{resource}[{other:.+}]', 'commonRoute');
        });

        $routeInfo = $baseDispatcher->dispatch($_SERVER['REQUEST_METHOD'], $path);
        switch ($routeInfo[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                $this->_handleNotFound();
                break;
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $this->_methodNotAllowed();
                break;
            case \FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];

                if (method_exists($this, $handler)) {
                    $this->$handler($routeInfo[2]);
                } else {
                    $this->_handleNotFound();
                }
                break;
        }
    }

    /**
     * 具体路由
     * @param $params
     */
    private function commonRoute($params) {
        $params['apiDir'] = 'ReMind';
        $resourcePathTemp = REMIND_API . '/%s/Resource/%s.php';
        $resourceFile = sprintf($resourcePathTemp, ucfirst($params['apiDir']), ucfirst($params['resource']));

        if (!file_exists($resourceFile)) {
            $this->_handleNotFound();
        }

        ParamHelper::initPara();
        //加载资源
        $resource = sprintf('\\Remind\\Api\\%s\\Resource\\%s', ucfirst($params['apiDir']), ucfirst($params['resource']));
        if (!class_exists($resource)) {
            $this->_handleNotFound();
        }
        $resourceInstance = new $resource();
        $rules = $resourceInstance->setUriRules();
        $dispatcher = \FastRoute\simpleDispatcher(function(\FastRoute\RouteCollector $r) use ($rules) {
            foreach ($rules as $rule) {
                $r->addRoute($rule[0], $rule[1], $rule[2]);
            }
        });
        $routeInfo = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], '/' . trim($params['other'], '/'));
        switch ($routeInfo[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                $this->_handleNotFound();
                break;
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $this->_methodNotAllowed();
                break;
            case \FastRoute\Dispatcher::FOUND:
                //加载当前访问类目错误配置
                $errClass = ucfirst($params['apiDir']) . 'ErrCode';
                $errCodeConfigFile =  REMIND_API . '/Base/Status_code/' . $errClass . '.php';
                if (file_exists($errCodeConfigFile)) {
                    $errClass = "\\Remind\\Api\\Base\\Status_code\\{$errClass}";
                    ResourceBase::$ERR_MSG += $errClass::$err_msg;
                }
                $funcName = $routeInfo[1];
                if (method_exists($resourceInstance, $funcName)) {
                    ResourceBase::$ACTION_NAME = $funcName;
                    if (!empty($routeInfo[2])) {
                        foreach ($routeInfo[2] as $key => $value) {
                            ParamHelper::$param['get'][$key] = $value;
                        }
                    }
                    
                    //传入参数统一校验处理
                    $checkRes = $resourceInstance->validateParam();
                    if ($checkRes !== true) {       //参数处理失败，返回错误
                        ResourceBase::display($checkRes);
                        exit;
                    }
                    
                    
                    if (method_exists($resourceInstance, 'init')) {
                        $resourceInstance->init();
                    }
                    $resourceInstance->$funcName();
                } else {
                    $this->_handleNotFound();
                }
                break;
        }
    }

    //路由解析失败
    private function _handleNotFound() {
        $res = ResourceBase::formatRes(400, 'uri match failed.');
        ResourceBase::display($res);exit;
    }

    //Method不支持
    private function _methodNotAllowed() {
        $res = ResourceBase::formatRes(400, 'method not allowed.');
        ResourceBase::display($res);exit;
    }
}