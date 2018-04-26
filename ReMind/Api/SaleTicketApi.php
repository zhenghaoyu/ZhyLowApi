<?php
/**
 * Created by PhpStorm.
 * User: zhenghaoyu
 * Date: 2018/4/15
 * Time: 下午8:53
 */
namespace Remind\Api\ReMind\Api;

use Remind\Api\Model\SaleTicketInfoModel;
use Remind\Api\ReMind\Util\HttpUtil;

class SaleTicketApi
{
    /**
     * 添加查询信息
     * @param $phone
     * @param $urlStr
     * @return int
     */
    public function addSaleTicketInfo($phone, $urlStr)
    {
        $param = [
            'phone' => $phone,
            'url_str'   => $urlStr,
            'addtime'   => time(),
        ];
        $insertId = SaleTicketInfoModel::getInstance()->insert($param);
        return $insertId;
    }

    /**
     * 给卖家发送查询短信
     * @param $id
     * @return bool
     */
    public function sendFindSms($id)
    {
        $url = "http://www.zhengoh.cn/coupon-admin/?id=".$id;
        $content = "查询优惠券".$url. " ";
        $phone = '18601940399';
        $res = HttpUtil::sendDeMolSms($phone, $content);
        $phone = '18665556015';
        $res = HttpUtil::sendDeMolSms($phone, $content);
        return $res;
    }

    /**
     * 根据id获取用户提交信息
     * @param $id
     * @return array|mixed
     */
    public function getSaleInfoById($id)
    {
        $field = "*";
        $saleInfo = SaleTicketInfoModel::getInstance()->getRow(['id' => $id], $field);
        if (!$saleInfo) {
            return [];
        }
        return $saleInfo;
    }

    /**
     * 添加更新优惠券信息
     * @param $id
     * @param $saleTicket
     * @param $recTicket
     * @return bool|mixed
     */
    public function updateTicketInfo($id, $saleTicket, $recTicket)
    {
        $data = [
            'sale_ticket'   => $saleTicket,
            'reco_ticket'   => $recTicket,
            'sendtime'      => time(),
        ];
        $res = SaleTicketInfoModel::getInstance()->update("id=$id", $data);
        return $res;
    }

    /**
     * 给用户发送优惠券信息
     * @param $phone
     * @param $saleTicket
     * @param $recTicket
     */
    public function sendTicketToUser($phone, $saleTicket, $recTicket)
    {
        $sendStr = '';
        $type = '';
        if (!empty($saleTicket)) {
            $type = 'sale';
            $sendStr = $saleTicket;
        }
        if ((!empty($recTicket))) {
            $type = 'reco';
            $sendStr = $recTicket;
        }
        $formatSendStr = $this->formatSendStr($sendStr);
        $sendRes = HttpUtil::sendSaleSms($phone, $formatSendStr, $type);
        return $sendRes;
    }

    /**
     * 格式化字符串
     * @param $str
     * @return bool|string
     */
    public function formatSendStr($str)
    {
        if (strpos($str, "￥") === false) {
            return '';
        }
        $str = substr($str, strpos($str, "复制"));
        return $str;
    }
}
