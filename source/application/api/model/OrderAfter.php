<?php

namespace app\api\model;

use app\common\model\OrderAfter as OrderAfterModel;
use app\common\model\Equip;
use app\common\model\Member;
use think\Db;


/**
 * 售后订单模型
 * Class OrderAddress
 * @package app\common\model
 */
class OrderAfter extends OrderAfterModel
{
    /**
     * 发起售后
     */
    public function addAfter($after)
    {                
        // 售后单号
        $after_no = date('Ymd') . substr(implode(null, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        $after['after_no'] = $after_no;

        // 开启事务
        Db::startTrans();
        try {
            $this->allowField(true)->save($after);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            $this->error = $e->getMessage();
            return false;
        }
    }

}
