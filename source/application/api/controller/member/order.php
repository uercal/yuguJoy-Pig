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
        $type = input('type');
        $page = input('page');
        // 当前员工信息
        $memberInfo = $this->getMember();        
        //订单
        $orderMember = new OrderMember;
        $list = $orderMember->getMemeberOrderList($memberInfo['id'], $type, $page);

        return $this->renderSuccess(compact('list'));
    }

    /**
     * 获取单
     */
    public function detail($id, $type)
    {
        $orderMember = new OrderMember;
        $detail = $orderMember->getDetail($id, $type);
        return $this->renderSuccess(compact('detail'));
    }

}
