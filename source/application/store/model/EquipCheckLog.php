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
        // 开启事务
        Db::startTrans();
        try {
            // 添加           
            $this->allowField(true)->save($data);
            Db::name('equip')->where('equip_id', $data['equip_id'])->update(
                ['status' => $data['equip_status']]
            );
            $this->addEquipLog($data['equip_id'], isset($data['order_id']) ? $data['order_id'] : null, $data['equip_status']);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
        }
        return false;
    }



    public function EquipDetail()
    {
        $request = Request::instance();
        $map = $request->request();
        $_map = [];
        $_map['equip_id'] = ['=', $map['equip_id']];

        if (!empty($map['startDate'])) $_map['create_time'] = ['>=', strtotime($map['startDate'])];
        if (!empty($map['endDate'])) $_map['create_time'] = ['<=', strtotime($map['endDate'] . ' 23:59:59')];

        $data = $this->with(['order', 'member', 'equip' => ['goods', 'specValue']])
            ->where($_map)
            ->order(['create_time' => 'asc'])
            ->select();

        return ['data' => $data, 'map' => $map];
    }


}
