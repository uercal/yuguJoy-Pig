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
    protected $insert = ['wxapp_id' => 10001];
    protected $append = ['pay_status_text', 'status_text', 'create_time_d'];
    protected $updateTime = false;




    public function getCreateTimeDAttr($value, $data)
    {
        return date('Y-m-d', $data['create_time']);
    }



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

    public function getStatusTextAttr($value, $data)
    {
        $status = [10 => '有效', 20 => '已取消'];
        return $status[$data['status']];
    }




    /**
     * 关联用户表
     */
    public function user()
    {
        return $this->hasOne('User', 'user_id', 'user_id');
    }


    /**
     * 生成订单号
     */
    protected function orderNo()
    {
        return date('Ymd') . substr(implode(null, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }
}
