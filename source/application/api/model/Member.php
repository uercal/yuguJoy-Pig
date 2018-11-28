<?php

namespace app\api\model;

use app\common\model\Member as MemberModel;
use think\Cache;


class Member extends MemberModel
{

    private $token;


    public function login($data)
    {
        $member = self::where([
            'phone' => $data['phone'],
            'password' => yoshop_hash($data['password'])
        ])->find();
        if ($member) {

        } else {
            $this->error = '登录失败, 未找到小程序信息';
            return false;
        }

        // 生成token (session3rd)
        $this->token = $this->token($member['id']);
        // 记录缓存, 7天
        Cache::set($this->token, $member['id'], 86400 * 7);
        return $member;
    }

    /**
     * 获取员工信息
     * @param $token
     * @return null|static
     * @throws \think\exception\DbException
     */
    public static function getMember($token)
    {
        return self::detail(['member_id' => Cache::get($token)]);
    }



    /**
     * 获取token
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }



    /**
     * 生成用户认证的token
     * @param $openid
     * @return string
     */
    private function token($member_id)
    {
        return md5('member' . $member_id . 'token_salt');
    }




}
