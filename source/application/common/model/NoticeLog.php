<?php

namespace app\common\model;

use think\Hook;

/**
 * notice 
 * @package app\common\model
 */
class NoticeLog extends BaseModel
{
    protected $name = 'notice_log';

    protected $updateTime = false;

    protected $insert = ['wxapp_id' => 10001];

    public function notice()
    {
        return $this->belongsTo('Notice','notice_id','id');
    }

}
