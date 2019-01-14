<?php

namespace app\common\model;

use think\Request;

/**
 * 扣款日志模型
 * Class Deduct
 * @package app\common\model
 */
class DeductLog extends BaseModel
{
    protected $name = 'deduct_log';
    protected $insert = ['wxapp_id' => 10001];
    protected $updateTime = false;

    public function Deduct()
    {
        return $this->belongsTo('Deduct', 'order_goods_id', 'order_goods_id');
    }


}
