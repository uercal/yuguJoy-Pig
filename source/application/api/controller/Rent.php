<?php

namespace app\api\controller;


use app\api\model\RentMode as RentModeModel;



class Rent extends Controller
{
    // 用户信息认证审核
    public function getList($goods_spec_id)
    {
        $model = new RentModeModel;
        $list = $model->getList($goods_spec_id);
        return $list;
    }
}
