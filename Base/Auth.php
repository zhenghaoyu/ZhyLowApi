<?php
/**
 * Created by PhpStorm.
 */
namespace Remind\Api\Base;

class Auth {
    //C端app_secret
    const HAOCHE_C_APP_SECRET = 'd9628ffb2557c1e9362fd7e88604c3be'; //haochec20151130
    
    public static $COOP_KEY_SECRET = array(
        'red' => '123212321',
    );
    
    public static function getCoopAuthResult() {
        
    }

    /**
     * 内部app加密校验
     * @param $appSrcret
     * @param $params
     * @return bool
     */
    public static function getAppAuthResult($appSrcret, $params) {
        if (isset($params['__Debug']) && $params['__Debug'] == '1') {
            return true;
        }
        $clientSign = $params['sign'];
        if (!$clientSign) {
            return false;
        }
        unset($params['sign']);

        if (!$params) {
            //为空的话加默认
            $params = array(1 => 1);
        }

        //按照key进行排序
        ksort($params);

        $paramStr = "";
        foreach ($params as $key => $val) {
            if ($val === false) {
                $val = 0;
            }
            $val = trim($val);
            $key = trim($key);
            $val = str_replace(array(' ' , "\t" , "\n"), array('' , '' , ''), $val);
            $paramStr .= $key . "=" . urlencode($val);
        }
        $paramStr = md5($paramStr . $appSrcret);
        $sign = md5($paramStr . $appSrcret);

        return $sign != $clientSign ? false : true;
    }
}