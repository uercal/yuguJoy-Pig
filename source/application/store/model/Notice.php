<?php

namespace app\store\model;

use app\common\model\Notice as NoticeModel;
use think\Db;
use think\Model;
use think\Request;

/**
 * 通知模块
 * Class Notice
 * @package app\store\model
 */
class Notice extends NoticeModel
{

    public function getList()
    {
        $request = Request::instance();
        return $this->paginate(15, false, ['query' => $request->request()]);
    }



    public function add($data)
    {  
        // 开启事务
        Db::startTrans();
        try {
            // 添加            
            $this->allowField(true)->save($data);
            // 
            $member_ids = explode(',', $data['member_ids']);
            $log = [];
            foreach ($member_ids as $key => $value) {
                $_log = [];
                $_log['member_id'] = $value;
                $_log['is_read'] = 0;
                $log[] = $_log;
            }
            $this->NoticeLog()->saveAll($log);

            Db::commit();
            return true;
        } catch (\Exception $e) {
            halt($e);
            Db::rollback();
        }
        return false;
    }


    public function remove()
    {
        // 开启事务处理
        Db::startTrans();
        try {
            $this->NoticeLog()->delete();
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
