<?php

namespace app\api\model;

use app\common\model\Category as CategoryModel;

/**
 * 商品分类模型
 * Class Category
 * @package app\common\model
 */
class Category extends CategoryModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'wxapp_id',
//        'create_time',
        'update_time'
    ];

    public function getIndexList()
    {
        $list = $this->with(['image', 'goods' => function ($query) {
            $query->with(['image.file', 'spec'])->order('goods_sort', 'asc');
        }])->select()->toArray();
        foreach ($list as $k => $cate) {
            foreach ($cate['goods'] as $key => $value) {
                if ($key > 3) {
                    unset($list[$k]['goods'][$key]);
                }
            }
        }
        return $list;
    }

}
