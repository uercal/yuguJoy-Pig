<?php

namespace app\api\model;

use app\common\model\GoodsSpec as GoodsSpecModel;

use app\common\model\RentMode;

/**
 * 商品规格模型
 * Class GoodsSpec
 * @package app\api\model
 */
class GoodsSpec extends GoodsSpecModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'wxapp_id',
        'create_time',
        'update_time'
    ];

    protected $append = ['default_rent'];



    public function getDefaultRentAttr($value, $data)
    {
        $model = new RentMode;
        $list = $model->getList($data['goods_spec_id']);
        $res = array_values(array_filter($list, function ($e) {
            return $e['is_default']  == 1;
        }));
        return $res;
    }
}
