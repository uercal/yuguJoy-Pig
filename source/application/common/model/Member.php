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


    public static function getStatusText($data)
    {
        if ($data['status'] == 40) {
            return '休息';
        } else {
            return getMemeberStatus($data['order_log'])['msg'];
        }
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
        return self::with(['role', 'orderLog' => ['order' => ['goods' => ['image']]]])->find($id);
    }



    // 获取空闲 员工列表
    public static function getReadyMember()
    {
        $data = self::with(['role', 'roleNameAttr', 'orderLog'])->where('status', '<>', 40)->select()->toArray();

        foreach ($data as $key => $value) {            
            $data[$key]['status_text'] = self::getStatusText($value);
        }

        return $data;

    }









}
