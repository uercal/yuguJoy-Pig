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
    protected $insert = ['wxapp_id' => 10001];


    protected $append = ['order'];


    public function getOrderAttr($value, $data)
    {
        if ($data['type'] == 20) {
            $content_arr = json_decode($data['content'], true);
            return Db::name('order')->where('order_id', $content_arr['order_id'])->find();
        }
        return [];
    }

    public function member()
    {
        return $this->hasOne('Member', 'id', 'member_id');
    }

    public function user()
    {
        return $this->hasOne('User', 'user_id', 'user_id');
    }

    public function quota()
    {
        return $this->hasOne('Quota', 'exam_id', 'id');
    }



    public function getStatusTextAttr($value, $data)
    {
        $status = [10 => '审核中', 20 => '已通过', 30 => '已驳回'];
        return $status[$data['status']];
    }

    public function getTypeTextAttr($value, $data)
    {
        $type = [10 => '用户认证', 20 => '员工送达审批', 30 => '线下提现'];
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
        // 默认
        if (empty($map['type'])) {
            $_map['type'] = ['=', 10];
            $map['type'] = 10;
        }

        if ($map['type'] == 20) {
            $data = $this->with(['member'])->where($_map)
                ->order(['update_time' => 'desc'])
                ->paginate(15, false, ['query' => $request->request()])->append(['order']);
        } else {
            $data = $this->with(['user'])->where($_map)
                ->order(['update_time' => 'desc'])
                ->paginate(15, false, ['query' => $request->request()]);
        }


        return ['data' => $data, 'map' => $map];
    }



}
