<?php

namespace app\api\controller\member;

use app\api\controller\Controller;
use app\api\model\User as UserModel;

/**
 * 员工端 用户列表
 * Class Index
 * @package app\api\controller\user
 */
class User extends Controller
{
    /**
     * 获取当员工订单
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function list()
    {
        $type = input('type');
        $page = input('page');            
        //用户列表
        $user = new UserModel;
        $list = $user->getTypeList($type, $page);

        return $this->renderSuccess(compact('list'));
    }

    /**
     * 
     */
    public function detail($user_id)
    {
        $model = new UserModel;
        $detail = $model->getDetail($user_id);
        $res = $detail->toArray();
        $res['create_time'] = $detail['create_time'];
        return $res;
    }


    /**
     * 
     */
    public function serach($phone)
    {
        $obj = UserModel::where(['phone' => $phone])->find();
        return $this->renderSuccess($obj);
    }

}
