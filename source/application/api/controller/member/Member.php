<?php

namespace app\api\controller\member;

use app\api\controller\Controller;
use app\api\model\Member as MemberModel;
use app\api\model\OrderMember;

/**
 * 员工端个人中心主页
 * Class Index
 * @package app\api\controller\user
 */
class Member extends Controller
{
    /**
     * 获取当前员工信息
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function list($type, $page)
    {
        $model = new MemberModel;
        $list = $model->getTypeList($type, $page);
        return $this->renderSuccess(compact('list'));
    }



    public function search($phone)
    {
        $data = MemberModel::with(['order_log', 'role'])->where('phone|name', 'like', "%$phone%")->select()->toArray();
        if (!empty($data)) {
            return $this->renderSuccess('SUCCESS');
        } else {
            return $this->renderError('员工不存在');
        }
    }


    public function getSearch($phone, $page)
    {
        $model = new MemberModel;
        $list = $model->getSearchList($phone, $page);
        return $this->renderSuccess(compact('list'));
    }


    /**
     * 修改密码
     */
    public function resetPass($origin, $password)
    {
        $memberInfo = $this->getMember()->toArray();
        if (yoshop_hash($origin) == $memberInfo['password']) {
            $model = new MemberModel;
            if ($model->resetPass($memberInfo['id'], $password)) {
                return $this->renderSuccess('SUCCESS');
            } else {
                return $this->renderError('更新失败');
            }
        } else {
            return $this->renderError('原密码错误');
        }

    }

}
