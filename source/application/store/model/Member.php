<?php

namespace app\store\model;

use app\common\model\Member as MemberModel;
use think\Db;
use think\Model;
use think\Request;

/**
 * 内部员工
 * Class StoreUser
 * @package app\store\model
 */
class Member extends MemberModel
{

    public function getList()
    {
        $request = Request::instance();
        return $this->with(['role'])->order(['id' => 'desc'])
            ->paginate(15, false, ['query' => $request->request()]);
    }

    public function add($data)
    {
        $data = input();
        $data = $data['member'];
        $data['wxapp_id'] = self::$wxapp_id;
        // 开启事务
        Db::startTrans();
        try {
            // 添加
            $obj = $this->where('phone', '=', $data['phone'])->find();
            if ($obj) {
                if (isset($data['id']) && $data['id'] == $obj['id']) {
                    // 
                } else {
                    $this->error = '手机号重复!!';
                    return false;
                }
            }
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

    public function reset($password)
    {
        // 开启事务处理
        Db::startTrans();
        try {
            // 删除当前商品
            $this->password = yoshop_hash($password);
            $this->save();
            // 事务提交
            Db::commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            Db::rollback();
            return false;
        }
    }

    public function getOne($map)
    {
        return $this->with('role')->where($map)->find();
    }
}
