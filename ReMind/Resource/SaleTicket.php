<?php
/**
 * Created by PhpStorm.
 * User: zhenghaoyu
 * Date: 2017/12/8
 * Time: 上午12:01
 */

namespace Remind\Api\ReMind\Resource;


use Remind\Api\ReMind\Api\SaleTicketApi;
use Remind\Api\ReMind\Api\SmsApi;
use Remind\Api\ReMind\Util\RemindPhoneUtil;

class SaleTicket extends PageBase
{
    public function setUriRules()
    {
        return [
            ['POST', '/addSaleTicket', 'addSaleTicket'],
            ['GET', '/SaleInfoById', 'getSaleInfoById'],
            ['POST', '/addTicket', 'addTicketInfo'],
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
        $saleApi = new SaleTicketApi();
        $insertId = $saleApi->addSaleTicketInfo($phone);
        if (!$insertId) {
            $this->response->error('查询失败,添加');
        }
        //发送短信给后台
        $sendFlag = $saleApi->sendFindSms($insertId, $urlStr);
        if (!$sendFlag) {
            $this->response->error('查询失败,发送');
        }
        $this->response->success('已查询');
    }

    /**
     * 根据id查询查找信息
     */
    public function getSaleInfoById()
    {
        $saleId = $this->request->get('id', '');
        if (empty($saleId)) {
            $this->response->error('入参错误');
        }
        $saleApi = new SaleTicketApi();
        $saleInfo = $saleApi->getSaleInfoById($saleId);
        if (empty($saleInfo)) {
            $this->response->error('查询失败');
        }
        $this->response->success($saleInfo);
    }

    /**
     * 添加优惠券信息
     */
    public function addTicketInfo()
    {
        $id = $this->request->get('id', '');
        $ticketUrl = $this->request->get('sale_ticket', '');
        $recommendUrl = $this->request->get('rec_ticket', '');
        if (empty($id) || (empty($ticketUrl) && empty($recommendUrl)) || (!empty($ticketUrl) && !empty($recommendUrl))) {
            $this->response->error('入参错误');
        }
        $saleApi = new SaleTicketApi();
        $saleInfo = $saleApi->getSaleInfoById($id);
        if (empty($saleInfo)) {
            $this->response->error('id不存在');
        }
        if (!empty($saleInfo['sale_ticket']) || !empty($saleInfo['reco_ticket'])) {
            $this->response->error('优惠信息已提交过');
        }
        //添加优惠券信息
        $res = $saleApi->updateTicketInfo($id, $ticketUrl, $recommendUrl);
        if (!$res) {
            $this->response->error('发送失败');
        }
        //给用户发送优惠短信
        $saleApi->sendTicketToUser($saleInfo['phone'], $ticketUrl, $recommendUrl);
        $this->response->success('发送成功');
    }
}