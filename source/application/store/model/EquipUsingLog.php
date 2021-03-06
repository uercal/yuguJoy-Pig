<?php

namespace app\store\model;

use app\common\model\EquipUsingLog as EquipUsingLogModel;
use think\Db;
use think\Request;

/**
 * 设备使用记录模型
 * Class EquipUsingLog
 * @package app\store\model
 */
class EquipUsingLog extends EquipUsingLogModel
{

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

    public function getStatics()
    {
        $request = Request::instance();
        $map = $request->request();

        $_map = [];
        if (!empty($map['startDate']) && !empty($map['endDate'])) $_map['create_time'] = ['between', [strtotime($map['startDate']), strtotime($map['endDate'])]];

        $data = $this->with(['equip' => ['goodsGetName', 'specValue']])->where($_map)
            ->order(['create_time' => 'desc'])
            ->select();

        return ['data' => $data, 'map' => $map];
    }
}
