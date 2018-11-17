<?php

namespace app\common\model;

use think\Hook;

/**
 * 订单模型
 * Class Order
 * @package app\common\model
 */
class Order extends BaseModel
{
    protected $name = 'order';

    /**
     * 订单模型初始化
     */
    public static function init()
    {
        parent::init();
        // 监听订单处理事件
        $static = new static;
        Hook::listen('order', $static);
    }

    /**
     * 订单商品列表
     * @return \think\model\relation\HasMany
     */
    public function goods()
    {
        return $this->hasMany('OrderGoods');
    }


    /**
     * 订单 对应 已发货设备
     */
    public function equip()
    {
        return $this->hasMany('Equip', 'order_id', 'order_id');
    }

    /**
     * 关联订单收货地址表
     * @return \think\model\relation\HasOne
     */
    public function address()
    {
        return $this->hasOne('OrderAddress', 'order_id', 'order_id');
    }

    /**
     * 关联用户表
     * @return \think\model\relation\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('User');
    }

    /**
     * 付款状态
     * @param $value
     * @return array
     */
    public function getPayStatusAttr($value)
    {
        $status = [10 => '待付款', 20 => '已付款'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 发货状态
     * @param $value
     * @return array
     */
    public function getDeliveryStatusAttr($value)
    {
        // $status = [10 => '待发货', 20 => '已发货'];
        // 已送达 跟 使用中  绑定
        $status = [10 => '待发货', 20 => '已送达'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 收货状态
     * @param $value
     * @return array
     */
    public function getReceiptStatusAttr($value)
    {
        // $status = [10 => '待收货', 20 => '已收货'];
        $status = [10 => '待收货', 20 => '使用中'];
        return ['text' => $status[$value], 'value' => $value];
    }


    /**
     * 维修状态
     * @param $value
     * @return array
     */
    public function getCheckStatusAttr($value)
    {
        $status = [10 => '正常', 20 => '维修中'];
        return ['text' => $status[$value], 'value' => $value];
    }



    /**
     * 订单状态
     * @param $value
     * @return array
     */
    public function getOrderStatusAttr($value)
    {
        $status = [10 => '进行中', 20 => '取消', 30 => '已完成'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 生成订单号
     */
    protected function orderNo()
    {
        return date('Ymd') . substr(implode(null, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }

    /**
     * 订单详情
     * @param $order_id
     * @return null|static
     * @throws \think\exception\DbException
     */
    public static function detail($order_id)
    {
        return self::get($order_id, ['goods' => ['spec', 'specValueName', 'equip', 'image', 'rentMode'], 'address', 'equip' => ['goodsGetName', 'specValue']]);
    }

}
