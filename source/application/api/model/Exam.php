<?php

namespace app\api\model;

use app\common\model\Exam as ExamModel;
use app\api\model\User;
use app\api\model\AccountMoney;
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
        $obj = $this->where(['user_id' => $user_id, 'type' => $type])->find();
        if ($obj) {
            return ['code' => 1, 'status' => $obj['status'], 'status_text' => $obj['status_text']];
        } else {
            return ['code' => 0];
        }

    }





    //线下提现 审批申请
    public function examCash($user_id, $price)
    {
        $account = new AccountMoney;
        $data = $account->where('user_id', $user_id)->find();
        $actual_money = $data['actual_money'];
        if ($price <= $actual_money) {
            $content = json_encode(
                [
                    'cash_price' => $price,
                ]
            );
            Db::startTrans();
            try {
                $res = $this->save([
                    'user_id' => $user_id,
                    'content' => $content,
                    'type' => 30, //线下提现
                    'status' => 10
                ]);
                Db::commit();
                return true;
            } catch (\Throwable $th) {
                return false;
            }

        }
    }



    // 判断线下提现 进程
    public function applyCashState($user_id)
    {
        $obj = $this->where([
            'user_id' => $user_id,
            'type' => 30,
            'status' => 10
        ])->find();        
        if ($obj) {
            return false;
        } else {
            return true;
        }
    }

}
