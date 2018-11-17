<?php

namespace app\common\model;

use think\Request;
use think\Db;

/**
 * 设备模型
 * Class Equip
 * @package app\common\model
 */
class Equip extends BaseModel
{
    protected $name = 'equip';
    protected $append = ['services'];

    public function getStatusTextAttr($value, $data)
    {
        $status = [10 => '在库', 20 => '运送中', 30 => '使用中', 40 => '维修中', 50 => '停用'];
        return $status[$data['status']];
    }

    public function order()
    {
        return $this->belongsTo('Order','order_id','order_id');
    }

    public function goods()
    {
        return $this->belongsTo('Goods');
    }

    public function goodsGetName()
    {
        return $this->belongsTo('Goods')->bind('goods_name');
    }


    public function specValue()
    {
        return $this->hasOne('SpecValue', 'spec_value_id', 'spec_value_id');
    }


    public function getServicesAttr($value, $data)
    {
        return implode(',', Db::name('goods_service')->whereIn('service_id', $data['service_ids'])->column('service_name'));
    }
    


    // 
    public function getList()
    {
        $request = Request::instance();
        $map = $request->request();

        $_map = [];
        if (!empty($map['status'])) $_map['status'] = ['=', $map['status']];
        if (!empty($map['type'])) $_map['type'] = ['=', $map['type']];
        if (!empty($map['goods_id'])) $_map['goods_id'] = ['=', $map['goods_id']];
        if (!empty($map['service_time'])) $_map['service_time'] = ['=', $map['service_time']];

        $data = $this->where($_map)
            ->order(['create_time' => 'desc'])
            ->paginate(15, false, ['query' => $request->request()]);

        return ['data' => $data, 'map' => $map];

    }

    
    public static function detail($equip_id)
    {
        return self::get($equip_id);
    }


}
