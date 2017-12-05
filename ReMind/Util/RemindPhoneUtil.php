<?php
/**
 * Created by PhpStorm.
 * User: zhenghaoyu
 * Date: 2017/12/5
 * Time: 下午5:52
 */

namespace ReMind\Api\ReMind\Util;


class RemindPhoneUtil
{
    /**
     * 手机号校验
     * @param string $phone
     * @return bool
     */
    static public function checkPhoneStr($phone = '')
    {
        if (preg_match("/^1[34578]{1}\d{9}$/", $phone)) {
            return true;
        } else {
            return false;
        }
    }
}