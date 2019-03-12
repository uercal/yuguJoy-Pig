<?php

namespace app\api\model;

use app\common\model\OrderAfter as OrderAfterModel;
use app\api\model\PayLog;
use app\api\model\AccountMoney;
use app\common\model\Equip;
use app\api\model\EquipUsingLog;
use app\api\model\EquipCheckLog;
use app\api\model\OrderMember;
use app\common\model\Member;
use think\Db;


/**
 * 售后订单模型
 * Class OrderAddress
 * @package app\common\model
 */
class OrderAfter extends OrderAfterModel
{
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




    public function doneAfter($after)
    {
        unset($after['member_token']);
        unset($after['wxapp_id']);


        // order_member        
        $member_ids = $this->where('id', $after['after_id'])->value('member_ids');
        $member_ids = explode(',', $member_ids);
        $order_member = [];

        // 
        $after['type'] = $after['type'] == 1 ? 10 : 20;
        if ($after['back_equip_ids'] == '') {
            // 没有返修  当前结算
            $after['pay_status'] = 20;
            foreach ($member_ids as $key => $value) {
                $_order_member = [];
                $_order_member['member_id'] = $value;
                $_order_member['after_id'] = $after['after_id'];
                $_order_member['status'] = 20;
                $order_member[] = $_order_member;
            }
        } else {
            // 返修
            $after['status'] = 30;
        }
        // 设备变更
        $equip = [];
        $equip_using_log = [];
        $equip_check_log = [];

        // 当场修复完毕
        if ($after['checked_equip_ids'] != '') {
            $checked_equip_ids = explode(',', $after['checked_equip_ids']);
            foreach ($checked_equip_ids as $key => $value) {
                // using
                $_using_log = [];
                $_using_log['order_id'] = $after['order_id'];
                $_using_log['equip_id'] = $value;
                $_using_log['member_id'] = $after['member_id'];
                $_using_log['equip_status'] = 40;
                $_using_log['create_time'] = time();
                $equip_using_log[] = $_using_log;
                $_using_log['equip_status'] = 30;
                $_using_log['create_time'] = time() + 50;
                $equip_using_log[] = $_using_log;

                // check
                $_check_log = [];
                $_check_log['order_id'] = $after['order_id'];
                $_check_log['equip_id'] = $value;
                $_check_log['check_member_id'] = $after['member_id'];
                $_check_log['check_time'] = time();
                $_check_log['check_status'] = 10;
                $equip_check_log[] = $_check_log;
                $_check_log['check_time'] = time() + 50;
                $_check_log['check_status'] = 20;
                $equip_check_log[] = $_check_log;
            }
        }

        // 旧
        if ($after['exchange_equip_ids'] != '') {
            $exchange_equip_ids = explode(',', $after['exchange_equip_ids']);
            foreach ($exchange_equip_ids as $key => $value) {
                // using  变为维修中   
                $_using_log = [];
                $_using_log['order_id'] = null;
                $_using_log['equip_id'] = $value;
                $_using_log['member_id'] = $after['member_id'];
                $_using_log['equip_status'] = 40;
                $equip_using_log[] = $_using_log;

                // equip  解除订单
                $_equip = [];
                $_equip['equip_id'] = $value;
                $_equip['order_id'] = null;
                $_equip['service_ids'] = null;
                $_equip['status'] = 40;
                $equip[] = $_equip;
            }
        }

        // 新
        if ($after['new_equip_ids'] != '') {
            $new_equip_ids = explode(',', $after['new_equip_ids']);
            foreach ($new_equip_ids as $key => $value) {
                // using  变为使用中
                $_using_log = [];
                $_using_log['order_id'] = $after['order_id'];
                $_using_log['equip_id'] = $value;
                $_using_log['member_id'] = $after['member_id'];
                $_using_log['equip_status'] = 30;
                $equip_using_log[] = $_using_log;

                // equip  绑定订单
                $_equip = [];
                $_equip['equip_id'] = $value;
                $_equip['order_id'] = $after['order_id'];
                $_equip['status'] = 30;
                // 替换新设备 默认 不存在增值服务。
                $_equip['service_ids'] = null;
                $_equip['service_time'] = time();
                $equip[] = $_equip;
            }
        }

        // 返修
        if ($after['back_equip_ids'] != '') {
            $back_equip_ids = explode(',', $after['back_equip_ids']);
            foreach ($back_equip_ids as $key => $value) {
                // using  变为维修中
                $_using_log = [];
                $_using_log['order_id'] = $after['order_id'];
                $_using_log['equip_id'] = $value;
                $_using_log['member_id'] = $after['member_id'];
                $_using_log['equip_status'] = 30;
                $equip_using_log[] = $_using_log;

                //equip 变为维修中          
                $_equip = [];
                $_equip['equip_id'] = $value;
                $_equip['status'] = 40;
                $equip[] = $_equip;
            }
        }


        //DO
        Db::startTrans();
        try {
            // 保存售后信息                               
            $id = $after['after_id'];
            unset($after['after_id']);
            unset($after['member_id']);
            unset($after['update_time']);

            if ($after['back_equip_ids'] == '') {
                $after['pay_price'] = bcadd($after['server_price'], $after['source_price'], 2);
                if ($after['pay_price'] == 0) {
                    $after['pay_status'] = 30;
                    $after['pay_time'] = time();
                    $after['status'] = 40;
                }
            }
            $this->where('id', $id)->update($after);

            // 设备
            $equipModel = new Equip;
            $equipUsingModel = new EquipUsingLog;
            $equipCheckModel = new EquipCheckLog;
            if (!empty($equip)) {
                $equipModel->saveAll($equip);
            }
            if (!empty($equip_using_log)) {
                $equipUsingModel->saveAll($equip_using_log);
            }
            if (!empty($equip_check_log)) {
                $equipCheckModel->saveAll($equip_check_log);
            }

            // 员工
            $orderMemberModel = new OrderMember;
            if (!empty($order_member)) {
                $orderMemberModel->saveAll($order_member);
            }

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            $this->error = $e->getMessage();
            return false;
        }
    }


    public function doneReback($after)
    {
        unset($after['member_token']);
        unset($after['wxapp_id']);

        // order_member        
        $member_ids = $this->where('id', $after['after_id'])->value('member_ids');
        $member_ids = explode(',', $member_ids);
        $order_member = [];

        // 返修结算
        $after['pay_status'] = 20;
        foreach ($member_ids as $key => $value) {
            $_order_member = [];
            $_order_member['member_id'] = $value;
            $_order_member['after_id'] = $after['after_id'];
            $_order_member['status'] = 20;
            $order_member[] = $_order_member;
        }

        //DO
        Db::startTrans();
        try {
            // 保存售后信息                               
            $id = $after['after_id'];
            unset($after['after_id']);
            unset($after['member_id']);
            unset($after['update_time']);

            $after['pay_price'] = bcadd($after['server_price'], $after['source_price'], 2);
            if ($after['pay_price'] == 0) {
                $after['pay_status'] = 30;
                $after['pay_time'] = time();
                $after['status'] = 40;
            }
            $this->where('id', $id)->update($after);

            // 员工
            $orderMemberModel = new OrderMember;
            if (!empty($order_member)) {
                $orderMemberModel->saveAll($order_member);
            }
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            $this->error = $e->getMessage();
            return false;
        }
    }





    /**
     * sendAfter
     */
    /**
     * 派发
     */
    public function sendAfter($after, $member_id)
    {
        $launch_member_id = $member_id;
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
            // halt($after);
            $this->allowField(true)->save($after, ['after_id' => $after['after_id']]);
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






    /**
     * getPayCount
     */
    public function getPayCount($user_id)
    {
        return $this->where(['user_id' => $user_id, 'pay_status' => 20])->count();
    }


    /**
     * list
     */
    public function getList($user_id, $type)
    {
        // 筛选条件
        $filter = [];
        // 订单数据类型
        switch ($type) {
            case 'doing':
                $filter['status'] = ['<', 40];
                $filter['pay_status'] = ['=', 10];
                break;
            case 'unPay':
                $filter['status'] = ['<', 40];
                $filter['pay_status'] = ['=', 20];
                break;
            case 'done':
                $filter['status'] = ['=', 40];
                $filter['pay_status'] = ['=', 30];
                break;
        }
        return $this->with(['order' => ['goods.image']])
            ->where('user_id', '=', $user_id)
            ->where($filter)
            ->order(['create_time' => 'desc'])
            ->select();
    }

    public function getSendList($page)
    {
        $list = $this->with(['order' => ['address']])->where([
            'pay_status' => 10,
            'status' => 10
        ])->order('create_time', 'desc')->paginate(5, false, ['page' => $page, 'list_rows' => 5]);
        return $list;
    }



    /**
     * doPay
     * 更改售后单状态 + 扣款 + payLog
     */
    public function doPay($after_id, $user_id)
    {
        $pay_price = $this->where('id', $after_id)->value('pay_price');
        $price = $pay_price * 100;
        //DO
        Db::startTrans();
        try {
            // 扣款  分
            $account = new AccountMoney;
            $account->where('user_id', $user_id)->setDec('account_money', $price);

            // payLog
            $payLog = new PayLog;
            $payLog->save([
                'pay_type' => 20, //售后
                'after_id' => $after_id,
                'pay_price' => $pay_price,
                'user_id' => $user_id
            ]);


            // 售后订单
            $this->where('id', $after_id)->update([
                'status' => 40,
                'pay_status' => 30,
                'pay_time' => time()
            ]);

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            $this->error = $e->getMessage();
            return false;
        }
    }
}
