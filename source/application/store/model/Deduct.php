<?php

namespace app\store\model;

use app\common\model\Deduct as DeductModel;
use think\Cache;
use think\Request;

class Deduct extends DeductModel
{
    public function getList()
    {
        $request = Request::instance();
        $map = $request->request();

        $_map = [];
        if (!empty($map['user_id'])) $_map['user_id'] = ['=', $map['user_id']];
        if (!empty($map['order_id'])) $_map['order_id'] = ['=', $map['order_id']];


        $data = $this->with(['user', 'orderGoods'])->where($_map)
            ->group('order_goods_id')
            ->order(['create_time' => 'desc'])
            ->paginate(15, false, ['query' => $request->request()]);

        return ['data' => $data, 'map' => $map];
    }
}
