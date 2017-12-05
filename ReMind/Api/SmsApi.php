<?php
/**
 * Created by PhpStorm.
 * User: zhenghaoyu
 * Date: 2017/12/5
 * Time: 下午4:29
 */

namespace ReMind\Api\ReMind\Api;


class SmsApi
{
    /**
     * 获取短信接口
     * @return int
     */
    static public function getCheckCodeInfo()
    {
        return 1243;
    }

    /**
     * 校验登录，返回token
     * @param $phone
     * @param $code
     * @return bool
     */
    static public function checkLogin($phone, $code)
    {
        return true;
    }
}