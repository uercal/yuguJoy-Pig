<?php

namespace app\api\controller\member;

use app\api\controller\Controller;
use app\api\model\Order as OrderModel;
use app\api\model\Exam as ExamModel;
use app\api\model\OrderMember;

/**
 * 员工端个人中心主页
 * Class Index
 * @package app\api\controller\user
 */
class Order extends Controller
{
    /**
     * 获取当员工订单
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function list()
    {
        // 当前员工信息
        $memberInfo = $this->getMember();       
        //订单
        $orderMember = new OrderMember;            
        $order = $orderMember->getMemeberOrderList($memberInfo['id']);                

        return $this->renderSuccess(compact('order'));
    }

}
