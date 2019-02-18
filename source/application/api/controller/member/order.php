<?php

namespace app\api\controller\member;

use app\api\controller\Controller;
use app\api\model\Order as OrderModel;
use app\api\model\Exam as ExamModel;
use app\api\model\OrderMember;
use app\api\model\OrderAfter;

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
     * 获取 库管订单管理 列表
     * param @type 1 配送 2售后 @page 当前页数
     */
    public function orderList($type, $page)
    {
        switch ($type) {
            case 1:
                $model = new OrderModel;
                $list = $model->getSendOrderList($page);
                break;

            case 2:
                $model = new OrderAfter;
                $list = $model->getSendList($page);
                break;
        }
        return $this->renderSuccess(compact('list'));
    }


    public function sendOrderDetail($id)
    {
        $model = new OrderModel;
        $detail = $model->detail($id);
        return $this->renderSuccess(compact('detail'));
    }


    public function sendAfterDetail($id)
    {
        $model = new OrderAfter;
        $detail = $model::getDetail($id);
        return $this->renderSuccess(compact('detail'));
    }


    /**
     * 派送发起
     */
    public function delivery()
    {
        halt('1');
        $input = input();
        $model = OrderModel::detail($input['order_id']);
        $memberInfo = $this->getMember();        
        if ($model->delivery($input, $memberInfo['id'])) {
            return $this->renderSuccess('success');
        } else {            
            return $this->renderError($model->error);
        }
    }




    /**
     * 维修派送发起
     */
    public function sendAfter()
    {
        $model = OrderAfter::getDetail(input()['after_id']);
        $memberInfo = $this->getMember();
        if ($model->sendAfter(input(), $memberInfo['id'])) {
            return $this->renderSuccess('success');
        } else {
            return $this->renderError($model->error);
        }
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


    /**
     * 确认派送
     */
    public function sendDone()
    {
        $orderMember = new OrderMember;
        $memberInfo = $this->getMember();
        if ($orderMember->sendDone(input(), $memberInfo['id'])) {
            return $this->renderSuccess('success');
        } else {
            return $this->renderError('error');
        }
    }



    /**
     * 售后单编辑
     */
    public function afterDone()
    {
        $after = new OrderAfter;
        $memberInfo = $this->getMember();
        $post = input();
        $post['member_id'] = $memberInfo['id'];
        if ($after->doneAfter($post)) {
            return $this->renderSuccess('success');
        } else {
            return $this->renderError('error');
        }
    }



    /**
     * 售后单返修结单
     */
    public function afterReback($order_member_id)
    {
        $after_id = OrderMember::where('id', $order_member_id)->value('after_id');
        $after = new OrderAfter;
        $detail = $after->getDetail($after_id);
        $detail['check_pics_ids'] = explode(',', $detail['check_pics_ids']);
        return $this->renderSuccess($detail);
    }


    /**
     * 返修结单
     */
    public function rebackDone()
    {
        $after = new OrderAfter;
        $memberInfo = $this->getMember();
        $post = input();
        $post['member_id'] = $memberInfo['id'];
        if ($after->doneReback($post)) {
            return $this->renderSuccess('success');
        } else {
            return $this->renderError('error');
        }
    }

}
