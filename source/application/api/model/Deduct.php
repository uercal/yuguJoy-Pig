<?php

namespace app\api\model;

use app\common\model\Deduct as DeductModel;
use think\Cache;


class Deduct extends DeductModel
{


    public function getRentStartAttr($value, $data)
    {
        return date('Y-m-d', $data['rent_start']);
    }

    public function getRentEndAttr($value, $data)
    {
        return date('Y-m-d', $data['rent_end']);
    }

    public function getDeductTimeAttr($value, $data)
    {
        return date('Y-m-d', $data['deduct_time']);
    }
}
