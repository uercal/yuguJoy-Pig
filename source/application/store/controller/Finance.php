<?php

namespace app\store\controller;

use app\store\model\AccountMoney as AccountMoneyModel;
use app\store\model\Deduct as DeductModel;
use app\store\model\Recharge as RechargeModel;
use app\store\model\Quota as QuotaModel;


/**
 * 财政管理控制器
 * Class 
 * @package app\store\controller
 */
class Finance extends Controller
{

    /**
     *  用户余额
     */
    public function account()
    {
        $account = new AccountMoneyModel;
        $res = $account->getList();
        $map = $res['map'];
        $list = $res['data'];
        // halt($list->toArray());
        return $this->fetch('account_index', compact('map', 'list'));
    }







    /**
     * 订单租赁扣款列表
     */
    public function deduct()
    {

    }



    /**
     * 额度发放记录
     */
    public function quota()
    {
        $quota = new QuotaModel;
        $res = $quota->getList();
        $map = $res['map'];
        $list = $res['data'];
        // halt($list->toArray());
        return $this->fetch('quota_index', compact('map', 'list'));
    }

}
