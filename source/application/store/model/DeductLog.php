<?php

namespace app\store\model;

use app\common\model\DeductLog as DeductLogModel;
use think\Cache;
use think\Request;

class DeductLog extends DeductLogModel
{
    public function getList()
    {
        $request = Request::instance();
        $map = $request->request();

        $_map = [];
        if (!empty($map['user_id'])) $_map['user_id'] = ['=', $map['user_id']];
        if (!empty($map['order_id'])) $_map['order_id'] = ['=', $map['order_id']];


        $data = $this->with(['Deduct' => ['user', 'order_goods']])
            ->whereIn('order_goods_id', function ($query) use ($_map) {
                $query->name('deduct')->where($_map)->field('order_goods_id');
            })->order(['create_time' => 'desc'])
            ->paginate(15, false, ['query' => $request->request()]);

        return ['data' => $data, 'map' => $map];
    }
}
