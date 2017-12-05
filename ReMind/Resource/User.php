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
        $code = SmsApi::getCheckCodeInfo(); //获取验证码
        if (!empty($code)) {
            $this->response->success(['code' => $code]);
        } else {
            $this->response->error('发送失败');
        }
    }
}
