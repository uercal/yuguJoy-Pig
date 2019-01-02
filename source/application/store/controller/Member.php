<?php

namespace app\store\controller;

use app\store\model\Member as MemberModel;
use app\store\model\Role as RoleModel;
use app\common\model\Privilege as PrivilegeModel;
use think\Config;



/**
 * 内部员工管理
 * Class User
 * @package app\store\controller
 */
class Member extends Controller
{
    /**
     * 用户列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $model = new MemberModel;
        $list = $model->getList();
        return $this->fetch('index', compact('list'));
    }


    public function add()
    {
        if (!$this->request->isAjax()) {
            // 角色列表
            $role_list = RoleModel::getCacheTree();
            // halt($role_list);
            return $this->fetch('add', compact('role_list'));
        }
        $model = new MemberModel;
        if ($model->add($this->postData('member'))) {
            return $this->renderSuccess('添加成功', url('member/index'));
        }
        $error = $model->getError() ? : '添加失败';
        return $this->renderError($error);
    }


    public function edit($id)
    {

        $model = MemberModel::detail($id);
        if (!$this->request->isAjax()) {        
            // 角色列表
            $role_list = RoleModel::getCacheTree();
            return $this->fetch('member/edit', compact('model', 'role_list'));
        }
        // 更新记录
        if ($model->add($this->postData('role'))) {
            return $this->renderSuccess('更新成功', url('member/index'));
        }
        $error = $model->getError() ? : '更新失败';
        return $this->renderError($error);
    }

    public function delete($id)
    {
        $model = MemberModel::get($id);
        if (!$model->remove()) {
            return $this->renderError('删除失败');
        }
        return $this->renderSuccess('删除成功');
    }


    public function resetPass($id, $password)
    {
        $model = MemberModel::get($id);
        if (!$model->reset($password)) {
            return $this->renderError('更新失败');
        }
        return $this->renderSuccess('更新成功');
    }


    public function exchangeStatus($id)
    {
        $model = MemberModel::get($id);
        if (!$model->exchange()) {
            return $this->renderError('更新失败');
        }
        return $this->renderSuccess('更新成功');
    }



    // 角色管理
    public function role()
    {
        $model = new RoleModel;
        $list = $model->getList();
        return $this->fetch('role/index', compact('list'));
    }


    /**
     * 角色管理
     */
    public function role_add()
    {
        if (!$this->request->isAjax()) {
            $privilege_list = PrivilegeModel::getCacheTree();
            return $this->fetch('role/add', compact('privilege_list'));
        }
        $model = new RoleModel;
        if ($model->add($this->postData('role'))) {
            return $this->renderSuccess('添加成功', url('member/role'));
        }
        $error = $model->getError() ? : '添加失败';
        return $this->renderError($error);
    }

    public function role_edit($id)
    {

        $model = RoleModel::detail($id);
        if (!$this->request->isAjax()) {
            return $this->fetch('role/edit', compact('model'));
        }
        // 更新记录
        if ($model->add($this->postData('role'))) {
            return $this->renderSuccess('更新成功', url('member/role'));
        }
        $error = $model->getError() ? : '更新失败';
        return $this->renderError($error);
    }

    public function role_delete($id)
    {
        $model = RoleModel::get($id);
        if (!$model->remove()) {
            return $this->renderError('删除失败');
        }
        return $this->renderSuccess('删除成功');
    }





    /**
     * 权限
     */
    public function privilege()
    {
        $model = new PrivilegeModel;
        $list = $model->getList();
        return $this->fetch('privilege/index', compact('list'));
    }


    public function privilege_add()
    {
        if (!$this->request->isAjax()) {
            $menu = $this->menus();            
            return $this->fetch('privilege/add', compact('menu'));
        }
        $model = new PrivilegeModel;
        if ($model->add($this->postData('privilege'))) {
            return $this->renderSuccess('添加成功', url('member/privilege'));
        }
        $error = $model->getError() ? : '添加失败';
        return $this->renderError($error);
    }


    public function privilege_edit($id)
    {
        $model = PrivilegeModel::detail($id);
        $model['api_menu_id'] = json_decode($model['api_menu_id'], true);
        if (!$this->request->isAjax()) {
            $menu = $this->menus();
            $menu = $this->editMenu($menu, $model['api_menu_id']);
            return $this->fetch('privilege/edit', compact('model', 'menu'));
        }
        // 更新记录
        if ($model->add($this->postData('privilege'))) {
            return $this->renderSuccess('更新成功', url('member/privilege'));
        }
        $error = $model->getError() ? : '更新失败';
        return $this->renderError($error);
    }

    public function privilege_delete($id)
    {
        $model = PrivilegeModel::get($id);
        if (!$model->remove()) {
            return $this->renderError('删除失败');
        }
        return $this->renderSuccess('删除成功');
    }








    /**
     * editMenu
     */
    public function editMenu($menu, $api_menu)
    {

        foreach ($menu as $key => $value) {
            if (!isset($value['submenu'])) {
                in_array($value['index'], $api_menu) ? $menu[$key]['checked'] = true : $menu[$key]['checked'] = false;
            } else {
                foreach ($value['submenu'] as $k => $sub) {
                    if (isset($sub['index'])) {
                        in_array($sub['index'], $api_menu) ? $menu[$key]['submenu'][$k]['checked'] = true : $menu[$key]['submenu'][$k]['checked'] = false;
                    } else {
                        foreach ($sub['submenu'] as $_k => $c) {
                            in_array($c['index'], $api_menu) ? $menu[$key]['submenu'][$k]['submenu'][$_k]['checked'] = true : $menu[$key]['submenu'][$k]['submenu'][$_k]['checked'] = false;
                        }
                    }
                }
            }
        }

        return $menu;
    }
}
