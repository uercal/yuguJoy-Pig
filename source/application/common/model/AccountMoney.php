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
