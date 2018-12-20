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
    protected $insert = ['wxapp_id' => 10001];


    public function member()
    {
        return $this->hasOne('Member', 'id', 'member_id');
    }

    public function order()
    {
        return $this->hasOne('Order', 'order_id', 'order_id');
    }

    public function getTypeTextAttr($value, $data)
    {
        $type = [10 => '配送', 20 => '维修'];
        return $type[$data['type']];
    }

    public function getStatusTextAttr($value, $data)
    {
        $status = [10 => '进行中', 20 => '已完成'];
        return $status[$data['status']];
    }

    public function after()
    {
        return $this->hasOne('OrderAfter', 'id', 'after_id');
    }

    

    /**
     * 获取该员工状态 0:空闲 10：配送 20：维修     
     */
    public function getMemberStatus($member_id)
    {
        $data = $this->getMemeberOrderList($member_id);
        usort($data, function ($a, $b) {
            return $a['create_time'] > $b['create_time'];
        });
        $_data = array_filter($data, function ($e) {
            return !isset($e['done']);
        });

        if (!empty($_data)) {
            $_data = array_values($_data);
            $status = $_data[0]['type'];
        } else {
            $status = 0;//空闲
        }

        return $status;
    }













}
