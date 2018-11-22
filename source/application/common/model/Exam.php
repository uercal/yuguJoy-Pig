<?php

namespace app\common\model;

use think\Request;
use think\Db;

/**
 * 设备模型
 * Class Exam
 * @package app\common\model
 */
class Exam extends BaseModel
{
    protected $name = 'exam';


    public function user()
    {
        return $this->hasOne('User', 'user_id', 'user_id');
    }



    public function getStatusTextAttr($value, $data)
    {
        $status = [10 => '审核中', 20 => '已通过', 30 => '已驳回'];
        return $status[$data['status']];
    }

    public function getTypeTextAttr($value, $data)
    {
        $type = [10 => '用户认证'];
        return $type[$data['type']];
    }

    public function getList()
    {
        $request = Request::instance();
        $map = $request->request();

        $_map = [];
        if (!empty($map['user_id'])) $_map['user_id'] = ['=', $map['user_id']];
        if (!empty($map['status'])) $_map['status'] = ['=', $map['status']];
        if (!empty($map['type'])) $_map['type'] = ['=', $map['type']];

        $data = $this->with(['user'])->where($_map)
            ->order(['update_time' => 'desc'])
            ->paginate(15, false, ['query' => $request->request()]);

        return ['data' => $data, 'map' => $map];
    }




    /**
     * 额度发放表 是否存在用户认证发放额度记录
     */
    public function isExistQuotaUser($user_id)
    {
        $obj = Db::name('quota_log')->where([
            'user_id' => $user_id,
            'quota_type' => 10
        ])->find();
        if ($obj) {
            return true;
        }
        return false;
    }

}
