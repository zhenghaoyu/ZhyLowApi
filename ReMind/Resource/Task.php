<?php
/**
 * Created by PhpStorm.
 * User: zhenghaoyu
 * Date: 2017/12/8
 * Time: 上午12:01
 */

namespace Remind\Api\ReMind\Resource;


use Remind\Api\ReMind\Api\SmsApi;

class Task extends PageBase
{
    public function setUriRules()
    {
        return [
            ['POST', '/addTask', 'addTask'],
            ['GET', '/taskInfo', 'getTaskInfo'],
        ];
    }
    public function addTask()
    {
        $token = $this->request->header('HTTP_TOKEN', '');
        $phone = $this->request->get('phone_str', '');
        $sendTime = $this->request->get('send_time', 0);
        $sendContent = $this->request->get('send_content', '');
        if (!SmsApi::isLogin($token, $phone)) {
            $this->response->error('未登录', '', -101);
        }
        if (intval($sendTime) < time() || $sendTime%300 != 0) {
            $this->response->error('选择时间错误', '', -102);
        }
        if (mb_strlen($sendContent) > 30 || empty($sendContent)) {
            $this->response->error('内容长度过长', '', 103);
        }
        $addRes = SmsApi::addSendContent($phone, $sendTime, $sendContent);
        if ($addRes) {
            $this->response->success();
        } else {
            $this->response->error('添加失败');
        }
    }
    public function getTaskInfo()
    {
        $token = $this->request->header('HTTP_TOKEN', '');
        $phone = $this->request->get('phone_str', '');
        if (!SmsApi::isLogin($token, $phone)) {
            $this->response->error('未登录', '', -101);
        }
        $taskInfo = SmsApi::getTaskInfoByPhone($phone);
        $this->response->success($taskInfo);
    }
}