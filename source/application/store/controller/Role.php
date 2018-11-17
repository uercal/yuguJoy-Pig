<?php

namespace app\store\controller;

use app\store\model\Role as RoleModel;

/**
 * 内部员工管理
 * Class User
 * @package app\store\controller
 */
class Role extends Controller
{
    /**
     * 角色列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $model = new RoleModel;        
        $list = $model->getList();        
        return $this->fetch('index', compact('list'));
    }


    


}
