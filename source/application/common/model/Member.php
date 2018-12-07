<?php

namespace app\common\model;

use think\Hook;

/**
 * member 
 * @package app\common\model
 */
class Member extends BaseModel
{
    protected $name = 'store_member';


    public function getStatusTextAttr($value, $data)
    {
        $status = [10 => '空闲', 20 => '配送中', 30 => '维修中', 40 => '休息'];
        return $status[$data['status']];
    }



    public function roleNameAttr()
    {
        return $this->hasOne('Role', 'id', 'role_id')->bind('role_name');
    }


    // 关联权限表
    public function role()
    {
        return $this->hasOne('Role', 'id', 'role_id');
    }

    // 关联订单表
    public function orderLog()
    {
        return $this->hasMany('OrderMember', 'member_id', 'id');
    }




    public function wxapp()
    {
        return $this->belongsTo('Wxapp');
    }




    public static function detail($id)
    {
        return self::with(['role', 'orderLog' => ['order'=>['goods'=>['image']]]])->find($id);
    }



    // 获取空闲 员工列表
    public static function getReadyMember()
    {
        return self::with(['role', 'roleNameAttr'])->where('status', 10)->select()->append(['status_text'])->toArray();

    }









}
