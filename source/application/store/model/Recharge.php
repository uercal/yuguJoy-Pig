<?php

namespace app\store\model;

use app\common\model\Recharge as RechargeModel;
use think\Cache;
use think\Request;

class Recharge extends RechargeModel
{
    public function getList()
    {
        $request = Request::instance();
        $map = $request->request();

        $_map = [];
        $_map['pay_status'] = ['=', 20];
        if (!empty($map['user_id'])) $_map['user_id'] = ['=', $map['user_id']];

        $data = $this->with(['user'])->where($_map)
            ->order(['create_time' => 'desc'])
            ->paginate(15, false, ['query' => $request->request()]);

        return ['data' => $data, 'map' => $map];
    }
}
