<?php

namespace app\common\model;

use think\Db;


/**
 * 订单商品模型
 * Class OrderGoods
 * @package app\common\model
 */
class OrderMember extends BaseModel
{
    protected $name = 'order_member';
    protected $updateTime = false;
    // protected $append = ['service_info'];


    public function member()
    {
        return $this->hasOne('Member', 'id', 'member_id');
    }

    public function getTypeTextAttr($value, $data)
    {
        $type = [10 => '配送', 20 => '维修'];
        return $type[$data['type']];
    }

    public function getStatusTextAttr($value, $data)
    {
        $status = [10 => '进行中', '已完成'];
        return $status[$data['status']];
    }    
}
