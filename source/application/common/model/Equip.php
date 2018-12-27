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
    protected $append = ['services', 'status_text'];

    public function getStatusTextAttr($value, $data)
    {
        $status = [10 => '在库', 20 => '运送中', 30 => '使用中', 40 => '维修中', 50 => '停用'];
        return $status[$data['status']];
    }

    public function order()
    {
        return $this->hasMany('Order', 'order_id', 'order_id');
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
    
    // 關聯使用記錄表
    public function equipUsingLog()
    {
        return $this->hasMany('EquipUsingLog');
    }

    // 关联维修记录表
    public function equipCheckLog()
    {
        return $this->hasMany('EquipCheckLog', 'equip_id', 'equip_id');
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
        return self::with(['goodsGetName', 'specValue', 'order'])->where('equip_id', $equip_id)->find()->append(['services'])->toArray();
    }




    /**
     * 统计
     */
    public function getAll()
    {
        return $this->with(['goods' => ['category'], 'specValue'])->select()->toArray();
    }


    // 
    public static function ecryptdString($str)
    {
        $keys = 'uercal,';
        $iv = ',xiaocaizhu';
        $encrypted_string = base64_encode($keys . $str . $iv);
        $encrypted_string = substr($encrypted_string, 0, -2);
        // halt([$encrypted_string,$this->decryptStrin($encrypted_string)]);
        return $encrypted_string;
    }

    public static function decryptStrin($str)
    {
        $str .= "==";
        $decrypted_string = base64_decode($str);
        $data = explode(',', $decrypted_string);
        return $data[1];
    }
}
