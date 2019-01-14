<?php

namespace app\common\model;

use think\Request;

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


    /**
     * 关联订单商品表
     */
    public function orderGoods()
    {
        return $this->hasOne('OrderGoods', 'order_goods_id', 'order_goods_id');
    }

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
        $data = $this->where([
            'deduct_time' => strtotime(date('Y-m-d', time())),
            'status' => 10
        ])->select();


    }












}
