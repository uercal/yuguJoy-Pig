<?php

namespace app\store\controller;

use app\store\model\Order as OrderModel;
use app\store\model\Equip as EquipModel;
use app\store\model\RentMode as RentModeModel;
use app\store\model\Goods as GoodsModel;
use think\Url;
use app\common\model\Region;
use app\store\model\Category;
use app\api\model\Order as OrderApi;
use app\store\model\User as UserModel;
use app\store\model\Member as MemberModel;
use app\store\model\OrderMember;
// 
use app\store\model\OrderAfter as OrderAfterModel;


/**
 * 订单管理
 * Class Order
 * @package app\store\controller
 */
class Order extends Controller
{
    /**
     * 待发货订单列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function delivery_list()
    {
        return $this->getList('待发货订单列表', [
            'pay_status' => 20,
            'delivery_status' => 10
        ]);
    }

    /**
     * 待收货订单列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function receipt_list()
    {
        return $this->getList('待收货订单列表', [
            'pay_status' => 20,
            'delivery_status' => 20,
            'receipt_status' => 10
        ]);
    }

    /**
     * 待付款订单列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function pay_list()
    {
        return $this->getList('待付款订单列表', ['pay_status' => 10, 'order_status' => 10]);
    }

    /**
     * 租赁中订单列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function rent_list()
    {
        return $this->getList('租赁中订单列表', ['order_status' => 30, 'done_status' => 10]);
    }


    /**
     * 已完成订单列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function complete_list()
    {
        return $this->getList('已完成订单列表', ['done_status' => 20]);
    }

    /**
     * 已取消订单列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function cancel_list()
    {
        return $this->getList('已取消订单列表', ['order_status' => 20]);
    }

    /**
     * 全部订单列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function all_list()
    {
        return $this->getList('全部订单列表');
    }

    /**
     * 订单列表
     * @param $title
     * @param $filter
     * @return mixed
     * @throws \think\exception\DbException
     */
    private function getList($title, $filter = [])
    {
        $model = new OrderModel;
        $res = $model->getList($filter);
        $list = $res['data'];
        $map = $res['map'];
        return $this->fetch('index', compact('title', 'list', 'map'));
    }

    /**
     * 订单详情
     * @param $order_id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function detail($order_id)
    {
        $detail = OrderModel::detail($order_id);
        // halt($detail['done_status']);
        // 订单配送人员列表
        $member_ids = OrderMember::where('order_id', $detail['order_id'])->column('member_id');
        $member = new MemberModel;
        $member_list = $member->getList(['id' => ['in', array_unique($member_ids)]]);
        // halt($member_list);
        return $this->fetch('detail', compact('detail', 'member_list'));
    }



    /**
     * 自主新增订单
     */
    public function add()
    {
        if ($this->request->isAjax()) {
            $post = input();
            $model = new OrderModel;
            $order = $model->addSelf($post);
            if ($model->add($order['address']['user_id'], $order)) {
                // 发起微信支付
                return $this->renderSuccess('新增成功', url('order/detail', ['order_id' => $model['order_id']]), [
                    'code' => 1,
                    'msg' => '新增成功',
                    'order_id' => $model['order_id']
                ]);
            }
            $error = $model->getError() ?: '订单创建失败';
            return $this->renderError($error, '', ['code' => 0, 'msg' => $error]);
        }
        return $this->fetch('add');
    }



    public function addAjax()
    {
        // 产品列表                
        $goods_list = Category::with(['goods' => ['specRel', 'spec', 'service.service']])->select();
        // 租赁模式    
        $rent_list = RentModeModel::field(['id', 'name', 'is_static', 'map'])->select();

        $regionData = Region::getCacheTree();
        foreach ($regionData as $key => $value) {
            $regionData[$key]['children'] = [];
            $regionData[$key]['value'] = $value['id'];
            $regionData[$key]['label'] = $value['name'];
            foreach ($value['city'] as $k => $v) {
                $v['label'] = $v['name'];
                $v['value'] = $v['id'];
                $v['children'] = [];
                foreach ($v['region'] as $_k => $_v) {
                    $_v['label'] = $_v['name'];
                    $_v['value'] = $_v['id'];
                    $v['children'][] = $_v;
                }
                $regionData[$key]['children'][] = $v;
            }
        }
        $regionData = array_values($regionData);
        return compact('goods_list', 'rent_list', 'regionData');
    }



    public function checkUser()
    {
        $input = input();
        $user_id = $input['user_id'];
        $detail = UserModel::detail(['user_id', $user_id]);
        if ($detail) {
            return $this->renderSuccess('', '', ['code' => 1, 'detail' => $detail]);
        } else {
            $error = '用户不存在';
            return $this->renderError($error, '', ['code' => 0, 'msg' => $error]);
        }
    }


    /**
     * 修改订单
     * @param $order_id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function edit($order_id)
    {
        if ($this->request->isAjax()) {
            $model = new OrderModel;
            $input = input();
            // 更新记录
            if ($model->edit($input)) {
                return $this->renderSuccess('更新成功', url("order/edit", ['order_id' => $order_id]));
            }
            $error = $model->getError() ?: '更新失败';
            return $this->renderError($error);
        }
        $detail = OrderModel::detail($order_id);         
        // 租赁模式 list
                
        // 订单配送人员列表
        $member_ids = OrderMember::where('order_id', $detail['order_id'])->column('member_id');
        $member = new MemberModel;
        $member_list = $member->getList(['id' => ['in', array_unique($member_ids)]]);

        // 
        return $this->fetch('edit', compact('detail', 'member_list'));
    }



    // 通过rent_mode——id取选择html
    public function getRentHtml($rent_id, $order_goods_id)
    {
        $rent_model = new RentModeModel;
        $html = $rent_model->getHtml($rent_id, $order_goods_id);
        return $html;
    }





    /**
     * 变更订单状态
     */
    public function changeStatus($state)
    {
        $model = new OrderModel;
        if ($model->chgStatus($this->postData('state'))) {
            return $this->renderSuccess('变更成功', url('order/edit', ['order_id' => $state['order_id']]));
        }
        $error = $model->getError() ?: '变更失败';
        return $this->renderError($error);
    }



    /**
     * 变更设备状态
     */
    public function changeEquipState($equip_id, $state)
    {
        $model = new EquipModel;
        if ($model->chgStatus($equip_id, $state)) {
            return $this->renderSuccess('变更成功', url('order/edit', ['order_id' => $state['order_id']]));
        }
        $error = $model->getError() ?: '变更失败';
        return $this->renderError($error);
    }



    /**
     * 获取在库设备列表 表格ajax
     */
    public function addEquipList($ids)
    {
        $model = new EquipModel;
        // 获取在库状态      
        $res = $model->getEquipList($ids);
        return $res;
    }



    /**
     * 自主新增订单AJAX
     */
    public function getMemberAjax()
    {
        $data = MemberModel::getReadyMember();

        return [
            'code' => 0,
            'msg' => '',
            'count' => $data['total'],
            'data' => $data['data']
        ];
    }




    /**
     * 确认发货
     * @param $order_id
     * @return array
     * @throws \think\exception\DbException
     */
    public function delivery($order_id)
    {
        $model = OrderModel::detail($order_id);
        if ($model->delivery(input())) {
            return $this->renderSuccess('发货成功', url('order/edit', ['order_id' => $order_id]));
        }
        $error = $model->getError() ?: '发货失败';
        return $this->renderError($error);
    }






    /**
     * 删除订单
     */
    public function delete($order_id)
    {
        $model = new OrderModel;
        $res = $model->deleteAll($order_id);
        return $res;
    }






    /**
     * 售后
     */

    public function order_after()
    {
        $model = new OrderAfterModel;
        $res = $model->getList();
        $list = $res['data'];
        $map = $res['map'];
        return $this->fetch('order_after', compact('list', 'map'));
    }



    public function order_after_add()
    {
        if ($this->request->isAjax()) {
            $post = input();
            $model = new OrderAfterModel;
            // 
            $order_id = $post['order_id'];
            $after = $post['after'];
            $after['order_id'] = $order_id;
            if ($model->addAfter($after)) {
                return $this->renderSuccess('发起成功', url('order/order_after'));
            }
            $error = $model->getError() ?: '发起失败';
            return $this->renderError($error);
        } else {
            $order_id = input()['order_id'];
            $detail = OrderModel::detail($order_id);
            return $this->fetch('order_after_add', compact('detail'));
        }
    }

    public function after_detail()
    {
        if ($this->request->isAjax()) {
            $post = input();
            if (empty($post['after']['member_ids'])) {
                $error = '派遣人员不能为空';
                return $this->renderError($error);
            }
            $post['after']['id'] = $post['id'];
            // 
            $model = new OrderAfterModel;
            if ($model->sendAfter($post['after'])) {
                return $this->renderSuccess('派发成功', url('order/order_after'));
            }
            $error = $model->getError() ?: '派发失败';
            return $this->renderError($error);
        } else {
            $after_id = input()['id'];
            $after = OrderAfterModel::getDetail($after_id);
            $detail = OrderModel::detail($after['order_id']);
            return $this->fetch('after_detail', compact('detail', 'after'));
        }
    }





    public function order_end($order_id)
    {
        $model = OrderModel::get($order_id);
        if ($model->endOrder($order_id)) {
            return $this->renderSuccess('完结成功');
        }
        $error = $model->error;
        return $this->renderError($error);
    }


    /**
     * 订单迁移
     */
    public function order_migrate()
    {
        $model = new OrderModel;
        $res = $model->getOne();
        $list = $res['data'];
        $map = $res['map'];
        return $this->fetch('order_migrate', compact('list', 'map'));
    }


    public function migrate($order_id)
    {
        // 
        $detail = OrderModel::detail($order_id);

        if ($this->request->isAjax()) {
            $post = input();
            if (empty($post['order_id'])) {
                $error = '订单不存在';
                return $this->renderError($error);
            }
            if (empty($post['user_id'])) {
                $error = '用户选择不能为空';
                return $this->renderError($error);
            }
            //             
            if ($detail->migrateOrder($post)) {
                return $this->renderSuccess('迁移成功', url('order/all_list'));
            }
            $error = $detail->getError() ?: '迁移失败';
            return $this->renderError($error);
        } else {
            return $this->fetch('migrate', compact('detail'));
        }
    }

    // 获取员工列表
    public function getUserAjax()
    {
        $model = new UserModel;
        $list = $model->getListAjax();
        $list = $list->toArray();
        return [
            'code' => 0,
            'msg' => '',
            'count' => $list['total'],
            'data' => $list['data']
        ];
    }
}
