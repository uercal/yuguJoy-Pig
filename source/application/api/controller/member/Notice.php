<?php

namespace app\api\controller\member;

use app\api\controller\Controller;
use app\api\model\Notice as NoticeModel;
use app\api\model\NoticeLog as NoticeLogModel;

/**
 * 员工端通知
 * Class Index
 * @package app\api\controller\user
 */
class Notice extends Controller
{
    /**
     * 获取当员工通知列表
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function list()
    {
        // 当前员工信息
        $memberInfo = $this->getMember();       
        // 通知列表
        $noticeLog = new NoticeLogModel;
        $page = input('page');
        $data = $noticeLog->getMemeberNoticeList($memberInfo['id'], $page);

        return $this->renderSuccess(compact('data'));
    }

    public function detail($id)
    {
        $noticeLog = new NoticeLogModel;
        $detail = $noticeLog->getDetail($id);
        return $this->renderSuccess(compact('detail'));
    }


    public function read($id)
    {
        $noticeLog = new NoticeLogModel;
        $res = $noticeLog->save(['is_read' => 1], ['id' => $id]);
        if ($res !== false) {
            return $this->renderSuccess('success');
        } else {
            return $this->renderError('error');
        }
    }
}
