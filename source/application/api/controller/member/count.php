<?php

namespace app\api\controller\member;

use app\api\controller\Controller;
use app\api\model\Order as OrderModel;
use app\api\model\Equip as EquipModel;
use app\api\model\Category;

/**
 * 
 * Class Index
 * @package app\api\controller\user
 */
class Count extends Controller
{
    public function getCateList()
    {
        $list = Category::select()->toArray();
        return $this->renderSuccess(compact('list'));
    }


    public function getDetail($cate_id)
    {
        $model = new Category;
        $data = $model->getAllList($cate_id);
        return $this->renderSuccess($data);
    }



}
