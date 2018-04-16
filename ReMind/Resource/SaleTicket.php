<?php
/**
 * Created by PhpStorm.
 * User: zhenghaoyu
 * Date: 2017/12/8
 * Time: 上午12:01
 */

namespace Remind\Api\Remind\Resource;


use Remind\Api\ReMind\Api\SaleTicketApi;
use Remind\Api\ReMind\Api\SmsApi;
use Remind\Api\ReMind\Util\RemindPhoneUtil;

class SaleTicket extends PageBase
{
    public function setUriRules()
    {
        return [
            ['POST', '/addSaleTicket', 'addSaleTicket'],
        ];
    }
    public function addSaleTicket()
    {
        $phone = $this->request->get('phone', '');
        $code = $this->request->get('code', '');
        $urlStr = $this->request->get('urlStr', '');
        if (empty($urlStr) || empty($code) || !RemindPhoneUtil::checkPhoneStr($phone)) {
            $this->response->error('入参错误');
        }
        //校验手机号
        $checkRes = SmsApi::checkLogin($phone, $code);
        if (!$checkRes) {
            $this->response->error('验证码错误');
        }
        //添加查询信息
        $urlStr = 'http:addfasdf 发多少阿斯顿离开家费劲啊是的弗兰克爱上的看法静安寺';
        $saleApi = new SaleTicketApi();
        $insertId = $saleApi->addSaleTicketInfo($phone, $urlStr);
        if (!$insertId) {
            $this->response->error('查询失败,添加');
        }
        //发送短信给后台
        $sendFlag = $saleApi->sendFindSms($insertId);
        if (!$sendFlag) {
            $this->response->error('查询失败,发送');
        }
        $this->response->success('已查询');
    }
}