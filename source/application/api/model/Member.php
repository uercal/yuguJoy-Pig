<?php

namespace app\api\model;

use app\common\model\Member as MemberModel;
use think\Cache;


class Member extends MemberModel
{
    public function login($data)
    {
        if ($member = self::with('role')->where([
            'phone' => $data['phone'],
            'password' => yoshop_hash($data['password'])
        ])->find()) {
            return $member;
        } else {
            $this->error = '登录失败, 未找到小程序信息';
            return false;
        }
    }
}
