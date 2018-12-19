<?php

namespace app\store\model;

use app\common\model\OrderAfter as OrderAfterModel;
use app\common\model\Equip;
use app\common\model\Member;
use think\Request;
use think\Db;
// 


/**
 * 售后订单模型
 * Class OrderAddress
 * @package app\common\model
 */
class OrderAfter extends OrderAfterModel
{

    public function getList()
    {
        $request = Request::instance();
        $map = $request->request();

        $_map = [];
        if (!empty($map['status'])) $_map['status'] = ['=', $map['status']];
        if (!empty($map['type'])) $_map['type'] = ['=', $map['type']];
        if (!empty($map['order_no'])) {
            $order_id = Db::name('order')->where('order_no', $map['order_no'])->value('order_id');
            $_map['order_id'] = ['=', $order_id];
        }
        if (!empty($map['startDate']) && !empty($map['endDate'])) $_map['create_time'] = ['between', [strtotime($map['startDate']), strtotime($map['endDate'])]];

        $data = $this->with(['user', 'order'])->where($_map)
            ->order(['create_time' => 'desc'])
            ->paginate(15, false, ['query' => $request->request()]);

        return ['data' => $data, 'map' => $map];
    }



    /**
     * 发起售后
     */
    public function addAfter($after)
    {
                
        // 售后单号
        $after_no = date('Ymd') . substr(implode(null, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        $after['after_no'] = $after_no;

        // 开启事务
        Db::startTrans();
        try {
            $this->allowField(true)->save($after);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            $this->error = $e->getMessage();
            return false;
        }
    }


    /**
     * 派发
     */
    public function sendAfter($after)
    {
        $session = session('yoshop_store');
        $launch_member_id = $session['user']['member_id'];        
        // 
        $after['status'] = 20;
        $after['launch_member_id'] = $launch_member_id;
        // 
        $member_arr = explode(',', $after['member_ids']);
        $order_member = [];
        foreach ($member_arr as $key => $value) {
            $_order_member = [];
            $_order_member['member_id'] = $value;            
            $_order_member['status'] = 10;
            $order_member[] = $_order_member;
        }
        
        // 开启事务
        Db::startTrans();
        try {            
            $this->isUpdate(true)->save($after);
            // 
            $this->order_log()->saveAll($order_member);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            $this->error = $e->getMessage();
            return false;
        }
    }
}
