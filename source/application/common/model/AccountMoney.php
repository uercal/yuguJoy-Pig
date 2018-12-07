<?php

namespace app\common\model;

use think\Request;
use think\Db;

/**
 * 用户余额模型
 * Class AccountMoney
 * @package app\common\model
 */
class AccountMoney extends BaseModel
{
    protected $name = 'account_money';
    protected $updateTime = false;
    protected $insert = ['wxapp_id' => 10001, 'open_id'];
    protected $append = ['actual_money','actual_quota'];

    public function getActualMoneyAttr($value, $data)
    {
        $money = $data['account_money'] - $data['freezing_account'];
        return bcdiv($money, 100, 2);
    }

    
    public function getActualQuotaAttr($value, $data)
    {
        $money = $data['quota_money'] - $data['freezing_quota'];
        return bcdiv($money, 100, 2);
    }


    public function getAccountMoneyAttr($value, $data)
    {
        return bcdiv($data['account_money'], 100, 2);
    }


    public function getFreezingAccountAttr($value, $data)
    {
        return bcdiv($data['freezing_account'], 100, 2);
    }

    public function getFreezingQuotaAttr($value, $data)
    {
        return bcdiv($data['freezing_quota'], 100, 2);
    }

    public function getQuotaMoneyAttr($value, $data)
    {
        return bcdiv($data['quota_money'], 100, 2);
    }


    /**
     * openID 自动写入
     */
    protected function setOpenIdAttr($value, $data)
    {
        return Db::name('user')->where('user_id', $data['user_id'])->value('open_id');
    }


    /**
     * 关联用户表
     */
    public function user()
    {
        return $this->hasOne('User', 'user_id', 'user_id');
    }


}
