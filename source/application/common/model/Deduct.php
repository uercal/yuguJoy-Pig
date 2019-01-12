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

    public function getDeductStatusTextAttr($value, $data)
    {
        $status = ['10' => '未扣款', '20' => '扣款成功'];
        return $status[$data['deduct_status']];
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



}
