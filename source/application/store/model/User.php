<?php

namespace app\store\model;

use app\common\model\User as UserModel;
use think\Request;
/**
 * 用户模型
 * Class User
 * @package app\store\model
 */
class User extends UserModel
{

    public function getListAjax()
    {
        $request = Request::instance();
        $get = $request->request();

        return $this->order(['user_id' => 'asc'])
            ->paginate($get['limit'], false, [
                'query' => $get
            ]);
    }
}
