<?php

namespace app\common\model;

use think\Request;
use think\Db;

/**
 * 设备模型
 * Class Equip
 * @package app\common\model
 */
class EquipCheckLog extends BaseModel
{
    protected $name = 'equip_check_log';

    public function getCheckStatusTextAttr($value, $data)
    {
        $status = [10 => '修复中', 20 => '已修复', 30 => '停用'];
        return $status[$data['check_status']];
    }

    public function order()
    {
        return $this->belongsTo('Order', 'order_id', 'order_id');
    }

    public function equip()
    {
        return $this->hasOne('Equip', 'equip_id', 'equip_id');
    }


    public function member()
    {
        return $this->hasOne('Member', 'id', 'check_member_id');
    }

    // 
    public function getList()
    {
        $request = Request::instance();
        $map = $request->request();

        $_map = [];
        if (!empty($map['equip_id'])) $_map['equip_id'] = ['=', $map['equip_id']];
        if (!empty($map['check_status'])) $_map['check_status'] = ['=', $map['check_status']];
        if (!empty($map['order_no'])) {
            $order_id = Db::name('order')->where('order_no', $map['order_no'])->value('order_id');
            $_map['order_id'] = ['=', $order_id];
        }

        if (!empty($map['startDate']) && !empty($map['endDate'])) $_map['check_time'] = ['between', [strtotime($map['startDate']), strtotime($map['endDate'])]];

        $data = $this->where($_map)
            ->order(['create_time' => 'desc'])
            ->paginate(15, false, ['query' => $request->request()]);

        return ['data' => $data, 'map' => $map];
    }

    public static function detail($id)
    {
        return self::get($id);
    }


}
