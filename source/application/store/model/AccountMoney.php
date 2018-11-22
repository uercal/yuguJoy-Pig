<?php

namespace app\store\model;

use app\common\model\AccountMoney as AccountMoneyModel;
use think\Request;
use think\Cache;


class AccountMoney extends AccountMoneyModel
{
    public function getList()
    {
        $request = Request::instance();
        $map = $request->request();

        $_map = [];
        if (!empty($map['user_id'])) $_map['user_id'] = ['=', $map['user_id']];                

        $data = $this->with(['user'])->where($_map)
            ->order(['user_id' => 'desc'])
            ->paginate(15, false, ['query' => $request->request()]);

        return ['data' => $data, 'map' => $map];
    }
}
