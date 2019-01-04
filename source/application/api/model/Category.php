<?php

namespace app\api\model;

use app\common\model\Category as CategoryModel;
use app\api\model\Equip;

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


    public function getAllList($cate_id)
    {
        $equip_model = new Equip;
        $equip_list = $equip_model->with(['goods' => ['image' => ['file']]])->whereIn('goods_id', function ($query) use ($cate_id) {
            $query->name('goods')->where('category_id', $cate_id)->field('goods_id');
        })->select()->toArray();

        $detail_list = [];
        foreach ($equip_list as $key => $value) {
            $detail_list[$value['goods_id']]['goods'] = $value['goods'];
            $detail_list[$value['goods_id']]['data'][$value['status']]['status_text'] = $value['status_text'];
            $detail_list[$value['goods_id']]['data'][$value['status']]['data'][] = $value;
        }
        foreach ($detail_list as $key => $value) {
            foreach ($value['data'] as $k => $v) {
                $detail_list[$key]['data'][$k]['count'] = count($v['data']);
            }
        }
        foreach ($detail_list as $key => $value) {
            $count = 0;
            foreach ($value['data'] as $k => $v) {
                $count += $v['count'];
            }
            $detail_list[$key]['count'] = $count;
        }




        $all_list = [];
        foreach ($equip_list as $key => $value) {
            $all_list[$value['status']]['status_text'] = $value['status_text'];
            $all_list[$value['status']]['data'][] = $value;
        }
        foreach ($all_list as $key => $value) {
            $all_list[$key]['count'] = count($value['data']);
        }

        $equip_count = count($equip_list);
        return compact('all_list', 'detail_list', 'equip_count');
    }






}
