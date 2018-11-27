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
    protected $updateTime = false;
    protected $insert = ['wxapp_id' => 10001];

    public function getQuotaTypeTextAttr($value, $data)
    {
        $type = [10 => '用户认证'];
        return $type[$data['quota_type']];
    }

    public function getQuotaMoneyAttr($value, $data)
    {
        return bcdiv($data['quota_money'], 100, 2);
    }

    public function option()
    {
        return $this->hasOne('QuotaOption', 'quota_type', 'quota_type');
    }


    public function exam()
    {
        return $this->belongsTo('Exam', 'quota_log_id', 'id');
    }

    /**
     * 关联用户表
     */
    public function user()
    {
        return $this->hasOne('User', 'user_id', 'user_id');
    }



}
