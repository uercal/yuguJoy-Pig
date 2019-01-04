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

    public function resetPass($member_id, $password)
    {
        return $this->save(['password' => yoshop_hash($password)], [
            'id' => $member_id
        ]);
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


    public function getTypeList($type, $page)
    {
        switch ($type) {
            case 1:
                $filter = 'empty';
                break;
            case 2:
                $filter = 'check';
                break;
            case 3:
                $filter = 'send';
                break;
        }

        $origin = $this->with(['role', 'orderLog'])->order(['id' => 'desc'])
            ->select()->toArray();
        foreach ($origin as $key => $value) {
            $res = $this::getStatusApiAttr($value);
            if (!$res) {
                unset($origin[$key]);
            } else {
                if ($res != $filter) {
                    unset($origin[$key]);
                }
            }
        }

        $data = array_values($origin);

        $curpage = $page;//当前第x页，

        $rows = 5;//每页显示几条记录

        $dataTo = array();

        $dataTo = array_chunk($data, $rows);
        $showdata = array();

        if ($dataTo) {
            $showdata = $dataTo[$curpage - 1];
        } else {
            $showdata = null;
        }

        $response = [
            'current_page' => $page,
            'data' => $showdata,
            'last_page' => count($dataTo),
            'per_page' => 5,
            'total' => count($data)
        ];


        return $response;

    }


    public function getSearchList($phone, $page)
    {
        $data = MemberModel::with(['order_log', 'role'])->where('phone|name', 'like', "%$phone%")->select()->toArray();
        foreach ($data as $key => $value) {
            $data[$key]['status'] = MemberModel::getStatusApiAttr($value);
        }


        $data = array_values($data);

        $curpage = $page;//当前第x页，

        $rows = 5;//每页显示几条记录

        $dataTo = array();

        $dataTo = array_chunk($data, $rows);
        $showdata = array();

        if ($dataTo) {
            $showdata = $dataTo[$curpage - 1];
        } else {
            $showdata = null;
        }

        $response = [
            'current_page' => $page,
            'data' => $showdata,
            'last_page' => count($dataTo),
            'per_page' => 5,
            'total' => count($data)
        ];

        return $response;
    }
}
