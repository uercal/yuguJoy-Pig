<?php

namespace app\api\controller;

use app\api\model\Category as CategoryModel;
use app\api\model\Goods as GoodsModel;

/**
 * 商品分类控制器
 * Class Goods
 * @package app\api\controller
 */
class Category extends Controller
{
    /**
     * 全部分类
     * @return array
     */
    public function lists($category_id)
    {
        $list = array_values(CategoryModel::getCacheTree());
        $data = [];
        foreach ($list as $key => $value) {
            $data[$value['category_id']] = $value;
            $data[$value['category_id']]['child'] = [];
        }
        $goods_model = new GoodsModel;
        $allgoods = $goods_model->getAllGoods()->toArray();
        foreach ($allgoods as $key => $value) {
            $data[$value['category_id']]['child'][] = $value;
        }
        $list = array_values($data);
        if ($category_id != 0) {
            $choose_list = array_filter($list, function ($a) use ($category_id) {
                return $a['category_id'] == $category_id;
            });
            $choose_list = array_values($choose_list)[0];
        } else {
            $child = [];
            foreach ($list as $key => $value) {
                $child = array_merge($child,$value['child']);                
            }
            $_list = [
                'category_id' => 0,
                'name' => '全部',
                'parent_id' => 0,
                'sort' => 100,
                'child' => $child
            ];
            $choose_list = $_list;
        }
        return $this->renderSuccess(compact('list', 'choose_list'));
    }
}
