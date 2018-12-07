<?php

namespace app\api\model;

use app\common\model\OrderMember as OrderMemberModel;

/**
 * 订单-成员模型
 * Class OrderGoods
 * @package app\store\model
 */
class OrderMember extends OrderMemberModel
{

    /**
     * 获得指定成员进行中订单数量
     */
    public function getDoingCount($member_id)
    {
        $doing = $this->where(['member_id' => $member_id, 'status' => 10])->group('order_id')->count();
        $done = $this->where(['member_id' => $member_id, 'status' => 20])->group('order_id')->count();
        $count = $doing - $done;
        return $count;
    }


}
