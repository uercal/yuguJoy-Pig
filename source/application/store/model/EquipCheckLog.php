<?php

namespace app\store\model;

use app\common\model\EquipCheckLog as EquipCheckLogModel;
use think\Db;
use think\Request;

/**
 * 设备维修模型
 * Class equipchecklog
 * @package app\store\model
 */
class EquipCheckLog extends EquipCheckLogModel
{
    public function add($data)
    {        
        $data['wxapp_id'] = self::$wxapp_id;
        $data['check_time'] = strtotime($data['check_time']);
        // halt($data);        
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
