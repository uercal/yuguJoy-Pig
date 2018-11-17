<?php

namespace app\store\model;

use app\common\model\Role as RoleModel;
use think\Request;
use think\Db;


/**
 * 角色权限
 * Class StoreUser
 * @package app\store\model
 */
class Role extends RoleModel
{
    public function getList()
    {
        $request = Request::instance();
        return $this
            ->order(['id' => 'desc'])
            ->paginate(15, false, ['query' => $request->request()]);
    }


    public function add()
    {
        $data = input();
        $data = $data['role'];
        $data['privilege_ids'] = implode(',', $data['privilege_ids']);
        $data['wxapp_id'] = self::$wxapp_id;
        // 开启事务
        Db::startTrans();
        try {
            // 添加商品
            $this->allowField(true)->save($data);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
        }
        return false;
    }


    public function remove()
    {
        // 开启事务处理
        Db::startTrans();
        try {          
            // 删除当前商品
            $this->delete();
            // 事务提交
            Db::commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            Db::rollback();
            return false;
        }
    }
}
