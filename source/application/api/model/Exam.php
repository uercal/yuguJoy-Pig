<?php

namespace app\api\model;

use app\common\model\Exam as ExamModel;

use think\Db;

/**
 * 审核模型
 * Class Goods
 * @package app\api\model
 */
class Exam extends ExamModel
{
    public function add($user, $data)
    {
        $content = json_encode(
            [
                'user_name' => $data['name'],
                'company' => $data['company'],
                'other_content' => $data['other_content'],
                'license_id' => $data['license_id'],
                'idcard_ids' => $data['idcard_ids'],
                'other_ids' => $data['other_ids']
            ]
        );
        Db::startTrans();
        // 
        // $obj = $this->where(['user_id' => $user['user_id'], 'type' => $data['type']])->find();
        // if ($obj) {
        //     $res = $this->save([
        //         'content' => $content,
        //         'type' => $data['type'],
        //         'status' => 10,
        //         'wxapp_id' => $data['wxapp_id'],
        //     ], [
        //         'user_id' => $user['user_id']
        //     ]);
        // } else {
        $res = $this->save([
            'user_id' => $user['user_id'],
            'content' => $content,
            'type' => $data['type'],
            'status' => 10,
            'wxapp_id' => $data['wxapp_id'],
        ]);
        // }
        Db::commit();
        return $res;
    }



    // 获取审核情况
    public function getStatus($user_id, $type = 10)
    {
        $obj = $this->where(['user_id' => $user_id, 'type' => $type, 'status' => 20])->find();
        if ($obj) {
            return ['code' => 1, 'status' => $obj['status'], 'status_text' => $obj['status_text']];
        } else {
            return ['code' => 0];
        }

    }

}
