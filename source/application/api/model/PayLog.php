<?php

namespace app\api\model;

use app\common\model\PayLog as PayLogModel;
use think\Request;

/**
 * 付款记录模型
 * Class Recharge 
 */
class PayLog extends PayLogModel
{

    public function getList($user_id, $page)
    {
        return $this->with(['order', 'after'])->where('user_id', $user_id)->order('create_time', 'desc')->paginate(15, false, ['page' => $page, 'list_rows' => 15])->append(['create_time_d']);
    }

}
