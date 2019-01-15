<?php

namespace app\common\model;

use think\Cache;
use think\Hook;
use think\Request;
use think\Db;

/**
 * member 
 * @package app\common\model
 */
class PayLog extends BaseModel
{
    protected $name = 'pay_log';
    protected $updateTime = false;
    protected $insert = ['wxapp_id' => 10001];
    protected $append = ['create_time_d'];


    public function order()
    {
        return $this->hasOne('Order', 'order_id', 'order_id');
    }


    public function after()
    {
        return $this->hasOne('OrderAfter', 'id', 'after_id');
    }


    public function orderGoods()
    {
        return $this->hasOne('Deduct', 'order_goods_id', 'order_goods_id');
    }


    public function getCreateTimeDAttr($value, $data)
    {
        return date('Y-m-d', $data['create_time']);
    }


    public function getPayTypeAttr($value, $data)
    {
        $type = [10 => '订单支付', 20 => '售后支付', 30 => '订单租金扣款'];
        return $type[$data['pay_type']];
    }

}
