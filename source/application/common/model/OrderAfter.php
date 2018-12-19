<?php

namespace app\common\model;

use app\common\model\UploadApiFile;
use app\common\model\Equip;
use app\common\model\Member;

/**
 * 售后订单模型
 * Class OrderAddress
 * @package app\common\model
 */
class OrderAfter extends BaseModel
{
    protected $name = 'order_after';
    protected $updateTime = 'update_time';
    protected $insert = ['wxapp_id' => 10001];

    protected $append = ['request_pics', 'checked_equip', 'exchange_equip', 'new_equip', 'back_equip', 'check_pics', 'members'];

    /**
     * append attr
     */
    public function getRequestPicsAttr($value, $data)
    {
        return UploadApiFile::whereIn('file_id', $data['request_pics_ids'])->select()->toArray();
    }

    public function getCheckedEquipAttr($value, $data)
    {
        return Equip::whereIn('equip_id', $data['checked_equip_ids'])->select()->toArray();
    }

    public function getExchangeEquipAttr($value, $data)
    {
        return Equip::whereIn('equip_id', $data['exchange_equip_ids'])->select()->toArray();
    }

    public function getNewEquipAttr($value, $data)
    {
        return Equip::whereIn('equip_id', $data['new_equip_ids'])->select()->toArray();
    }

    public function getBackEquipAttr($value, $data)
    {
        return Equip::whereIn('equip_id', $data['back_equip_ids'])->select()->toArray();
    }

    public function getCheckPicsAttr($value, $data)
    {
        return UploadApiFile::whereIn('file_id', $data['check_pics_ids'])->select()->toArray();
    }

    public function getMembersAttr($value, $data)
    {
        return Member::with(['roleNameAttr'])->whereIn('id', $data['member_ids'])->select()->toArray();
    }



    /**
     * 订单关联
     */
    public function order()
    {
        return $this->belongsTo('Order', 'order_id', 'order_id');
    }

    /**
     *  用户关联
     */
    public function user()
    {
        return $this->hasOne('User', 'user_id', 'user_id');
    }

    /**
     * 用户订单记录 关联
     */
    public function order_log()
    {
        return $this->hasMany('OrderMember', 'after_id', 'id');
    }





    /**
     * 售后发起成员
     */
    public function launch_member()
    {
        return $this->hasOne('Member', 'id', 'launch_member_id');
    }



    public function getTypeTextAttr($value, $data)
    {
        $type = [10 => '售后维修', 20 => '其他'];
        return $type[$data['type']];
    }


    public function getStatusTextAttr($value, $data)
    {
        $status = [10 => '已发起', 20 => '进行中', 30 => '返修中', 40 => '已完成'];
        return $status[$data['status']];
    }



    // 
    public static function getDetail($after_id)
    {
        $obj = self::find($after_id);
        return $obj->append(['request_pics', 'checked_equip', 'exchange_equip', 'new_equip', 'back_equip', 'check_pics', 'members']);
    }





}
