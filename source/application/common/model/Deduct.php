<?php

namespace app\common\model;

use app\common\model\AccountMoney;
use app\common\model\PayLog;
use app\common\model\DeductLog;
use app\common\model\Order;
use think\Request;
use think\Db;

/**
 * 扣款模型
 * Class Deduct
 * @package app\common\model
 */
class Deduct extends BaseModel
{
    protected $name = 'deduct';
    protected $insert = ['wxapp_id' => 10001];

    public function getStatusTextAttr($value, $data)
    {
        $status = ['10' => '进行中', '20' => '已完成'];
        return $status[$data['status']];
    }


    public function getDeductPriceAttr($value, $data)
    {
        return bcdiv($data['deduct_price'], 100, 2);
    }



    /**
     * 关联订单表
     */
    public function order()
    {
        return $this->hasOne('Order', 'order_id', 'order_id');
    }


    /**
     * 关联用户表
     */
    public function user()
    {
        return $this->hasOne('User', 'user_id', 'user_id');
    }


    // /**
    //  * 关联订单商品表
    //  */
    // public function orderGoods()
    // {
    //     return $this->hasOne('OrderGoods', 'order_goods_id', 'order_goods_id');
    // }

    /**
     * 关联租赁模式
     */
    public function rentMode()
    {
        return $this->hasOne('RentMode', 'id', 'rent_mode_id');
    }

    /**
     * 日志
     */
    public function deductLog()
    {
        return $this->hasMany('DeductLog', 'order_goods_id', 'order_goods_id');
    }




    public function checkDeduct()
    {
        //查询当天需要扣款的记录
        $data = $this->where([
            'deduct_time' => strtotime(date('Y-m-d', time())),
            'status' => 10
        ])->select()->toArray();

        $accountModel = new AccountMoney;
        $payLogModel = new PayLog;
        $deductLogModel = new DeductLog;
        // 
        $account = [];
        $pay_log = [];
        $deduct = [];
        $deduct_log = [];

        if (empty($data)) {
            return ['msg' => '今日无扣款订单', 'status' => false];
        }

        //事务进行
        Db::startTrans();
        try {

            foreach ($data as $key => $value) {                                

                // 租金  （元）
                $deduct_price = $value['deduct_price'];

                // 扣掉用户余额  （分）
                $accountModel->where('user_id', $value['user_id'])->setDec('account_money', $deduct_price * 100);

                // paylog 交易记录 租金扣款 （元）
                $_paylog = [];
                $_paylog['order_goods_id'] = $value['order_goods_id'];
                $_paylog['pay_price'] = $deduct_price / 100;
                $_paylog['pay_type'] = 30;
                $_paylog['user_id'] = $value['user_id'];
                $pay_log[] = $_paylog;

                
                //更新扣款信息                
                $this->where('id', $value['id'])->update([
                    'deduct_time' => strtotime('+1 month', $value['deduct_time']),
                    'status' => $value['rent_end'] <= strtotime('+1 month', $value['deduct_time']) ? 20 : 10
                ]);                

                // 更新扣款日志
                $_deduct_log = [];
                $_deduct_log['order_goods_id'] = $value['order_goods_id'];
                $_deduct_log['start_time'] = $value['deduct_time'];
                $_deduct_log['end_time'] = strtotime('+1 month', $value['deduct_time']);
                $deduct_log[] = $_deduct_log;

            }

            $payLogModel->saveAll($pay_log);
            $deductLogModel->saveAll($deduct_log);

            Db::commit();
            return ['msg' => '今日扣款订单成功', 'status' => true];
        } catch (\Exception $e) {
            Db::rollback();
            $this->error = $e->getMessage();
            return ['msg' => $e->getMessage(), 'status' => false];
        }




    }



    /**
     * 检查订单是否完结
     */
    public function checkOrder()
    {

    }







}
