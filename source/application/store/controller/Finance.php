<?php

namespace app\store\controller;

use app\store\model\AccountMoney as AccountMoneyModel;
use app\store\model\Deduct as DeductModel;
use app\store\model\DeductLog as DeductLogModel;
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
    public function deduct_log()
    {
        $deduct_log = new DeductLogModel;
        $res = $deduct_log->getList();
        $map = $res['map'];
        $list = $res['data'];
        return $this->fetch('deduct_log_index', compact('map', 'list'));

    }



    public function deduct()
    {
        $deduct = new DeductModel;
        $res = $deduct->getList();
        $map = $res['map'];
        $list = $res['data'];
        return $this->fetch('deduct_index', compact('map', 'list'));
    }













    /**
     * 充值记录
     */
    public function recharge()
    {
        $recharge = new RechargeModel;
        $res = $recharge->getList();
        $map = $res['map'];
        $list = $res['data'];
        return $this->fetch('recharge_index', compact('map', 'list'));
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
