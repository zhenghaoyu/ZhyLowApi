<?php
namespace Remind\Api\Base\Util;


class ParamHelper {
    public static $param = array();

    /**
     * 初始化参数
     */
    public static function initPara() {
        self::$param['get'] = self::getAllGetParams();
        self::$param['post'] =self::getAllPostParams();
        
        if (!empty($_SERVER['CONTENT_TYPE']) && strstr('application/json', $_SERVER['CONTENT_TYPE'])) {
            $dataInput = file_get_contents('php://input');
            if ($_SERVER['HTTP_CONTENT_ENCODING'] == 'gzip') {
                $dataInput = gzdecode($dataInput);
            }
            //这里只能读取一次，php 5.6 后可以多次读取。
            $postJsonData = json_decode($dataInput, true);
            self::$param['post'] = !empty($postJsonData) ? $postJsonData : array();
        }
        self::$param['header'] = array();
    }

    public static function getAllGetParams()
    {
        $params = array();
        if ($_GET) {
            foreach ($_GET as $key => $value) {
                $params[$key] = $_GET[$key];
            }
        }

        return $params;
    }

    public static function getAllPostParams()
    {
        $params = array();
        if ($_POST) {
            foreach ($_POST as $key => $value) {
                $params[$key] = $_POST[$key];
            }
        }

        return $params;
    }
    /**
     * 获取变量
     * @param $key
     * @return bool|int|null
     */
    public static function getArg($key) {
        $value = '';
        if(array_key_exists($key, self::$param['post'])) {
            $value = self::$param['post'][$key];
        } else if (array_key_exists($key, self::$param['get'])) {
            $value = self::$param['get'][$key];
        } else {
            if(isset(self::$param['header'][$key])) {
                $value = self::$param['header'][$key];
            }
        }
        return $value;
    }
}