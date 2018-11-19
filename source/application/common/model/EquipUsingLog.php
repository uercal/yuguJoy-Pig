<?php

namespace app\common\model;

use think\Request;
use think\Db;

/**
 * 设备模型
 * Class Equip
 * @package app\common\model
 */
class EquipUsingLog extends BaseModel
{
    protected $name = 'equip_using_log';
    protected $updateTime = false;

    public function getEquipStatusTextAttr($value, $data)
    {
        $status = [10 => '在库', 20 => '运送中', 30 => '使用中', 40 => '维修中', 50 => '停用'];
        return $status[$data['equip_status']];
    }


    public function order()
    {
        return $this->belongsTo('Order', 'order_id', 'order_id');
    }


    public function equip()
    {
        return $this->belongsTo('Equip', 'equip_id', 'equip_id');
    }


    // 操作人
    public function member()
    {
        return $this->belongsTo('Member', 'member_id', 'id');
    }

    // 
    public function getList()
    {
        $request = Request::instance();
        $map = $request->request();
        $_map = [];
        if (!empty($map['equip_id'])) $_map['equip_id'] = ['=', $map['equip_id']];               

        if (!empty($map['startDate']) && !empty($map['endDate'])) $_map['Using_time'] = ['between', [strtotime($map['startDate']), strtotime($map['endDate'])]];

        $data = $this->with(['order','member'])
            ->where($_map)
            ->order(['create_time' => 'desc'])
            ->paginate(15, false, ['query' => $request->request()]);

        return ['data' => $data, 'map' => $map];
    }

    public static function detail($id)
    {
        return self::get($id);
    }


}
