<?php

namespace app\api\model;

use app\common\model\OrderGoods as OrderGoodsModel;

/**
 * 订单商品模型
 * Class OrderGoods
 * @package app\api\model
 */
class OrderGoods extends OrderGoodsModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'content',
        'wxapp_id',
        'create_time',
    ];

    protected $append = ['rent_dately'];

    public function getRentDatelyAttr($value, $data)
    {
        return date('Y-m-d', $data['rent_date']);
    }
}
