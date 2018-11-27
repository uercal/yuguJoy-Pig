<?php

namespace app\store\model;

use app\common\model\Quota as QuotaModel;
use think\Request;
use think\Cache;

/**
 * 模型
 * Class OrderGoods
 * @package app\store\model
 */
class Quota extends QuotaModel
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
