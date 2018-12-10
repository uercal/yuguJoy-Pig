<?php

namespace app\common\model;

use think\Hook;
use app\common\model\Member;
/**
 * notice 
 * @package app\common\model
 */
class Notice extends BaseModel
{
    protected $name = 'notice';

    protected $insert = ['wxapp_id' => 10001];

    protected $append = ['member'];

    public function NoticeLog()
    {
        return $this->hasMany('NoticeLog', 'notice_id', 'id');
    }


    public function getMemberAttr($value,$data)
    {
        return Member::whereIn('id',$data['member_ids'])->with(['role'])->select()->toArray();
    }
}
