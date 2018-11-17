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
    public function lists()
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
        return $this->renderSuccess(compact('list'));
    }
    
}
