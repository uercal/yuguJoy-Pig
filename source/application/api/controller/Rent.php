<?php

namespace app\api\controller;


use app\api\model\RentMode as RentModeModel;



class Rent extends Controller
{
    // 用户信息认证审核
    public function getList(){
        $model = new RentModeModel;
        $list = $model->getList();
        return $list;
    }
}
