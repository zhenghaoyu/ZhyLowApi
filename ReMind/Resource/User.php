<?php
/**
 * Created by PhpStorm.
 */

namespace Remind\Api\ReMind\Resource;

use Remind\Api\ReMind\Api\SmsApi;
use Remind\Api\ReMind\Util\RemindPhoneUtil;

class User extends PageBase
{
    public function init()
    {

    }

    public function setUriRules()
    {
        return [
            ['GET', '/phoneCode', 'getPhoneCode'],
            ['GET', '/checkLogin', 'checkLogin'],
        ];
    }

    /**
     * 获取短信验证码
     */
    public function getPhoneCode()
    {
        $phone = $this->request->get('phone', '');
        if (empty($phone)) {
            $this->response->error('电话为空');
        }
        if (!RemindPhoneUtil::checkPhoneStr($phone)) {
            $this->response->error('手机号格式错误');
        }
        $code = SmsApi::getCheckCodeInfo($phone); //获取验证码
        if (!empty($code)) {
            $this->response->success();
        } else {
            $this->response->error('一分钟内只发一次');
        }
    }

    /**
     * 验证登录情况
     */
    public function checkLogin()
    {
        $phone = $this->request->get('phone', '');
        $code = $this->request->get('code', '');
        if (empty($code) || !RemindPhoneUtil::checkPhoneStr($phone)) {
            $this->response->error('入参错误');
        }
        $checkRes = SmsApi::checkLogin($phone, $code);
        if ($checkRes) {
            $this->response->success(['token' => $checkRes]);
        } else {
            $this->response->error('校验失败');
        }
    }
}
