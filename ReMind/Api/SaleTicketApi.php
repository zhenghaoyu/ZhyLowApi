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
        $url = "http://remind.zhengoh.cn/#/list?id=".$id;
        $content = "查询优惠券".$url. " ";
        $phone = '18601940399';
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
        $field = "id,phone,url_str";
        $saleInfo = SaleTicketInfoModel::getInstance()->getRow(['id' => $id], $field);
        if (!$saleInfo) {
            return [];
        }
        return $saleInfo;
    }
}
