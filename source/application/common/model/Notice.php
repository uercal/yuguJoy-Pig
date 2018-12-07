<?php

namespace app\common\model;

use think\Hook;

/**
 * notice 
 * @package app\common\model
 */
class Notice extends BaseModel
{
    protected $name = 'notice';

    protected $insert = ['wxapp_id' => 10001];

    public function NoticeLog()
    {
        return $this->hasMany('NoticeLog', 'notice_id', 'id');
    }
    

}
