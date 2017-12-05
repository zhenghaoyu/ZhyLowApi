<?php
/**
 * Created by PhpStorm.
 */

namespace Remind\Api\ReMind\Resource;

use Remind\Api\ReMind\Api\SmsApi;

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
        $code = SmsApi::getCheckCodeInfo();

        if (!empty($code)) {
            $this->response->success(['code' => $code]);
        }
    }
}
