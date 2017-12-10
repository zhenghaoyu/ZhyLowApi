<?php
/**
 * Created by PhpStorm.
 * User: zhenghaoyu
 * Date: 2017/12/5
 * Time: 下午4:29
 */

namespace ReMind\Api\ReMind\Api;

use Remind\Api\Model\SendInfoModel;
use Remind\Api\Model\UserInfoModel;
use Remind\Api\Model\RedisModel;
use Remind\Api\ReMind\Util\HttpUtil;
use ReMind\Api\ReMind\Util\RemindPhoneUtil;

class SmsApi
{
    static public $cacheKey = "code_";
    /**
     * 获取短信接口
     * @return int
     */
    static public function getCheckCodeInfo($phone)
    {
        $cacheKey = self::$cacheKey.$phone;
        if (RedisModel::get($cacheKey)) {
            return false;
        }
        $code = rand(1000, 9999);
        //发送验证码
        $sendRes = HttpUtil::aliDaYu($code, $phone);
        if ($sendRes) {
            RedisModel::set($cacheKey, $code, 120); //添加缓存
            return true;
        }
        return false;
    }

    /**
     * 校验登录，返回token
     * @param $phone
     * @param $code
     * @return bool
     */
    static public function checkLogin($phone, $code)
    {
        $cacheKey = self::$cacheKey.$phone;
        $cacheCode = RedisModel::get($cacheKey);
        self::addUser($phone);
        if (intval($cacheCode) === intval($code)) {
            RedisModel::del($cacheKey);
            $token = RemindPhoneUtil::addToken($phone);
            RedisModel::set($token, $phone, 3600*24*2); //两天过期
            self::addUser($phone);
            return $token;
        } else {
            return false;
        }
    }

    /**
     * 不存在就新增手机号
     * @param $phone
     */
    static public function addUser($phone)
    {
        $userInfo = UserInfoModel::getInstance()->getList("*", "phone=".$phone);
        if (empty($userInfo)) {
            UserInfoModel::getInstance()->insert(['phone' => $phone]);
        }
    }

    /**
     * 判断是否为登录状态
     * @param $token
     * @param $phone
     * @return bool
     */
    static public function isLogin($token, $phone)
    {
        $loginCache = RedisModel::get($token);
        if ($loginCache && intval($loginCache) === intval($phone)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 新增发送信息
     * @param $phone
     * @param $sendTime
     * @param $sendContent
     * @return bool
     */
    static public function addSendContent($phone, $sendTime, $sendContent)
    {
        $field['content'] = $sendContent;
        $field['phone'] = $phone;
        $field['send_time'] = $sendTime;
        $res = SendInfoModel::getInstance()->insert($field);
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取发送任务列表
     * @param $phone
     * @return array|mixed
     */
    static public function getTaskInfoByPhone($phone)
    {
        $where['phone'] = $phone;
        $data = SendInfoModel::getInstance()->getList("content,send_time,created_at", $where, "created_at DESC");
        $res = [];
        foreach ($data as $oneInfo) {
            if ($oneInfo['send_time'] < time()) {
                $res['has_send'][] = $oneInfo;
            } else {
                $res['no_send'][] = $oneInfo;
            }
        }
        return $res;
    }
}