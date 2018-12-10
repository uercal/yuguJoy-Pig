<?php

namespace app\api\controller\member;

use app\api\controller\Controller;
use app\api\model\Order as OrderModel;
use app\api\model\Exam as ExamModel;
use app\api\model\NoticeLog;
use app\api\model\OrderMember;

/**
 * 员工端个人中心主页
 * Class Index
 * @package app\api\controller\user
 */
class Index extends Controller
{
    /**
     * 获取当前员工信息
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function detail()
    {
        // 当前员工信息
        $memberInfo = $this->getMember();
        // 权限接口json
        $role = $memberInfo['role'];
        $menu = [];
        foreach ($role['api_menu'] as $key => $value) {
            $role = json_decode($value);
            $header = explode('/', $role[0])[0];
            $menu[$header] = $role;
        }         
        //订单条数(正在进行)    
        $orderMember = new OrderMember;            
        $orderCount = $orderMember->getDoingCount($memberInfo['id']);
        // 通知
        $noticeLog = new Noticelog;
        $noticeCount = $noticeLog->getUnReadCount($memberInfo['id']);
        // 
        $memberInfo = [
            'name' => $memberInfo['name'],
            'phone' => $memberInfo['phone'],
            'function' => $memberInfo['function']
        ];
        return $this->renderSuccess(compact('memberInfo', 'menu','orderCount','noticeCount'));
    }

}
