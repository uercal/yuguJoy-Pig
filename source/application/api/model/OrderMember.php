<?php

namespace app\api\model;

use app\common\model\OrderMember as OrderMemberModel;

/**
 * 订单-成员模型
 * Class OrderGoods
 * @package app\store\model
 */
class OrderMember extends OrderMemberModel
{

    /**
     * 获得指定成员进行中订单数量
     */
    public function getOrderAfter($member_id)
    {
        $list = $this->with(['order', 'after'])->where(['member_id' => $member_id])->select()->toArray();
        $order_list = [];
        $after_list = [];
        foreach ($list as $key => $value) {
            if (!empty($value['order_id'])) {
                $order_list[] = $value;
            }
            if (!empty($value['after_id'])) {
                $after_list[] = $value;
            }
        }
        $order = [];
        $order['doing'] = [];
        $order['done'] = [];
        $after = [];
        $after['doing'] = [];
        $after['done'] = [];
        $done = [];
        foreach ($order_list as $key => $value) {
            if ($value['status'] == 10) {
                $order['doing'][] = $value;
            }
            if ($value['status'] == 20) {
                $order['done'][] = $value;
                $done[] = $value;
            }
        }
        $orderCount = count($order['doing']) - count($order['done']);
        foreach ($after_list as $key => $value) {
            if ($value['status'] == 10) {
                $after['doing'][] = $value;
            }
            if ($value['status'] == 20) {
                $after['done'][] = $value;
                $done[] = $value;
            }
        }
        $afterCount = count($after['doing']) - count($after['done']);

        return compact('orderCount', 'afterCount');
    }


    /**
     * 获取该员工所有订单列表和状态
     */
    public function getMemeberOrderList($member_id, $type, $page)
    {
        switch ($type) {
            case 1:
                return $this->with(['order' => ['address']])->where('order_id', 'NOT IN', function ($query) {
                    $query->name('order_member')->where('status', 20)->whereNotNull('order_id')->field('order_id');
                })->whereNull('after_id')->where('member_id', $member_id)->order('create_time', 'desc')->paginate(5, false, ['page' => $page, 'list_rows' => 5]);;
                break;
            case 2:
                return $this->with(['after' => ['order' => ['address']]])->where('after_id', 'NOT IN', function ($query) {
                    $query->name('order_member')->where('status', 20)->whereNotNull('after_id')->field('after_id');
                })->whereNull('order_id')->where('member_id', $member_id)->order('create_time', 'desc')->paginate(5, false, ['page' => $page, 'list_rows' => 5]);
                break;
            case 3:
                return $this->with(['order' => ['address'], 'after' => ['order' => ['address']]])->where(['status' => 20, 'member_id' => $member_id])
                    ->order('create_time', 'desc')->paginate(5, false, ['page' => $page, 'list_rows' => 5]);
                break;
        }
    }



    /**
     * 获取该ID的详情
     */
    public function getDetail($id, $type)
    {
        switch ($type) {
            case 'order':
                $data = $this->with(['order' => ['address', 'equip' => ['goods']]])->where('id', $id)->find();
                $equip = $data['order']['equip'];
                break;
            case 'after':
                $data = $this->with(['after' => ['order' => ['address', 'equip' => ['goods']]]])->where('id', $id)->find();
                $equip = $data['after']['order']['equip'];
                break;
        }
        
        // 按产品 把设备分类  spec_value TODO
        $arr = [];
        foreach ($equip as $key => $value) {
            $arr[$value['goods_id']]['goods'] = $value['goods'];
            $arr[$value['goods_id']]['equip'][] = $value;
        }
        // 
        $equip = array_values($arr);

        return compact('data', 'equip');
    }

}
