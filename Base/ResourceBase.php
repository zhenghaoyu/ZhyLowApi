<?php
/**
 * api资源基类
 */


namespace Remind\Api\Base;
use Remind\Api\Base\Util\ParamHelper;

abstract class ResourceBase {
    public static $ERR_MSG = array();    //存放所有可使用的错误码
    public static $ACTION_NAME = '';     //当前执行的方法
    protected $_param = array();         //存放所有请求参数

    /**
     * 以固定格式返回数据，200之外的统一返回
     * @param $errCode int
     * @param $errMsg string    错误提示
     * @param $data 返回数据
     */
    public static function formatRes($errCode, $errMsg = '', $data = array()) {
        $httpStatus = 400;
        $errMsg     = empty($errMsg) ? self::$ERR_MSG[$errCode]['1'] : $errMsg;
        $errCode = $errCode > 0 ? 0 : $errCode;

        $returnArr = array(
            'http_status' => $httpStatus,
            'code' => $errCode,
            'message' => $errMsg,
            'data' => $data,
        );
        return $returnArr;
    }

    /**
     * 输出需要返回数据
     * @param $data array
     * @param $disType enum(json)
     */
    public static function display($data = array(), $josnForceObject = false, $disType = 'json') {
        $httpStatus = empty($data['http_status']) ? 400 : $data['http_status'];
        unset($data['http_status']);
        header('HTTP/1.1 ' . $httpStatus);

        //低版本nginx 兼容，手动添加header 返回
        header('Content-Type:application/json');

        switch ($disType) {
            case 'json':
                $type = empty($data['data']) && $josnForceObject ? JSON_FORCE_OBJECT : 0;
                echo json_encode($data, $type);
                break;
            default:
                header('HTTP/1.0 500');
                $data = array('message' => 'error echo type.', 'detail' => 'function display() error param $disType', 'code' => -1);
                echo json_encode($data);
                break;
        }
    }

    //分发参数
    public function validateParam() {
        $allConfig = $this->setParamConfig();
        if (empty($allConfig)) {
            return true;
        }
        //给了参数配置，就限定所有函数都要定义key 值
        if (!isset($allConfig[self::$ACTION_NAME])) {
            return ResourceBase::formatRes(400, '', 'action:' . self::$ACTION_NAME . ' not set param config');
        }
        $paramConfig = $allConfig[self::$ACTION_NAME];

        if (is_array(current($paramConfig))) {      //如果是action_type 分类的，参数配置是两层
            $this->_param['action_type'] = ParamHelper::getArg('action_type');
            if (empty($this->_param['action_type'])) {
                return ResourceBase::formatRes(400, '', 'action_type unavailable or server config error.');
            }
            $paramConfig = $paramConfig[$this->_param['action_type']];
        }
        if (is_array($paramConfig) && !empty($paramConfig)) {
            foreach ($paramConfig as $paramKey => $paramType) {
                switch ($paramType) {
                    case 'string':
                        $this->_param[$paramKey] = (string)ParamHelper::getArg($paramKey);
                        break;

                    case 'int':
                        $this->_param[$paramKey] = (int)ParamHelper::getArg($paramKey);
                        break;

                    case 'float':
                        $this->_param[$paramKey] = (float)ParamHelper::getArg($paramKey);
                        break;

                    case 'bool':
                    case 'boolean':
                        $this->_param[$paramKey] = (bool)ParamHelper::getArg($paramKey);
                        break;

                    case 'array':
                        $this->_param[$paramKey] = json_decode(ParamHelper::getArg($paramKey), true);
                        break;

                    default:
                        $this->_param[$paramKey] = ParamHelper::getArg($paramKey);
                        break;
                }
                //过参数校验
                $checkRes = $this->checkParam($paramKey);
                if (is_array($checkRes)) {
                    return $checkRes;
                }
            }
        }
        return true;
    }

    abstract function setUriRules();
    public function setParamConfig() {
        return array();
    }
    public function checkParam($paramKey)
    {
        return true;
    }
}