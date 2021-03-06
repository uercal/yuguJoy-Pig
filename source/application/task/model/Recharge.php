<?php

namespace app\task\model;

use app\common\model\Recharge as RechargeModel;
use app\common\model\AccountMoney;
use think\Db;

/**
 * 订单模型
 * Class Order
 * @package app\common\model
 */
class Recharge extends RechargeModel
{

    public $error;

    /**
     * 待支付订单详情
     * @param $order_no
     * @return null|static
     * @throws \think\exception\DbException
     */
    public function payDetail($order_no)
    {
        return self::get(['order_no' => $order_no, 'pay_status' => 10]);
    }

    /**
     * 更新付款状态
     * @param $transaction_id
     * @return false|int
     * @throws \Exception
     */
    public function updatePayStatus($transaction_id)
    {
        Db::startTrans();
        // self::get(['pay_status' => 10]);
        try {
           // 更新订单状态
            $this->save([
                'pay_status' => 20,
                'pay_time' => time(),
                'transaction_id' => $transaction_id,
            ]);
            AccountMoney::where('user_id', $this->user_id)->setInc('account_money', $this->pay_price * 100);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            $this->error = $e->getMessage();
            return false;
        }
    }

}
