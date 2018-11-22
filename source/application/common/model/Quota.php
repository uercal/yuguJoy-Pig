<?php

namespace app\common\model;

use think\Request;

/**
 * 押金额度发放模型
 * Class Recharge
 * @package app\common\model
 */
class Quota extends BaseModel
{
    protected $name = 'quota_log';
    protected $updateTime = true;
    protected $insert = ['wxapp_id' => 10001];


    public function option()
    {
        return $this->hasOne('QuotaOption', 'quota_type', 'quota_type');
    }


    /**
     * 关联用户表
     */
    public function user()
    {
        return $this->hasOne('User', 'user_id', 'user_id');
    }



}
