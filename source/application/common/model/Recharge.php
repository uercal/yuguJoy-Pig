<?php

namespace app\common\model;

use think\Request;

/**
 * 充值记录模型
 * Class Recharge
 * @package app\common\model
 */
class Recharge extends BaseModel
{
    protected $name = 'recharge';


    public function getSourceTextAttr($value, $data)
    {
        $source = ['10' => '充值'];
        return $source[$data['source']];
    }

    public function getPayStatusTextAttr($value, $data)
    {
        $status = ['10' => '未充值', '20' => '已充值'];
        return $status[$data['pay_status']];
    }

    /**
     * 关联用户表
     */
    public function user()
    {
        return $this->hasOne('User', 'user_id', 'user_id');
    }



}
