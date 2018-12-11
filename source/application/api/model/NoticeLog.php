<?php

namespace app\api\model;

use app\common\model\NoticeLog as NoticeLogModel;
use think\Db;
use think\Model;
use think\Request;

/**
 * 通知模块
 * Class Notice
 * @package app\api\model
 */
class NoticeLog extends NoticeLogModel
{
    public function getUnReadCount($member_id)
    {
        return $this->where(['member_id' => $member_id, 'is_read' => 0])->count();
    }


    public function getMemeberNoticeList($member_id, $page)
    {
        return $this->with(['notice'])->where(['member_id' => $member_id])->order('create_time', 'desc')->paginate(5, false, ['page' => $page, 'list_rows' => 5]);
    }

    public function getDetail($notice_log_id)
    {
        return $this->with(['notice'])->where('id', $notice_log_id)->find();
    }
}
