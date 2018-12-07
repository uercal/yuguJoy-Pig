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
        $data = input();                
        // 开启事务
        Db::startTrans();
        try {
            // 添加            
            $this->allowField(true)->save($data);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
        }
        return false;
    }
}
