<?php

namespace app\store\model;

use app\common\model\Exam as ExamModel;
use app\store\model\Quota;
use app\common\model\AccountMoney;
use app\common\model\User;

use think\Db;


/**
 * 审核模型
 * Class Goods
 * @package app\store\model
 */
class Exam extends ExamModel
{
    public static function attrTextMap()
    {
        $data = [
            'user_name' => '个人姓名',
            'company' => '公司名称',
            'org_code_id' => '组织机构代码证',
            'license_id' => '营业执照',
            'idcard_ids' => '身份证正反面',
        ];
        return $data;
    }


    public function updateStatus($data)
    {                        
        // 
        $obj = $this->where('id', $data['id'])->find();
        
        // 开启事务
        Db::startTrans();
        try {            
            // $type==10 用户认证 获得额度
            if ($obj['type'] == 10) {
                $model = new Quota;
                $value = bcmul($data['quota_money'], 100, 2);
                $quota_log = $model->allowField(true)->save([
                    'quota_type' => 10,
                    'quota_money' => $value,
                    'user_id' => $obj['user_id'],
                    'exam_id' => $data['id']
                ]);
                $account = new AccountMoney;
                if (!$account::get($obj['user_id'])) {
                    $account->save([
                        'user_id' => $obj['user_id']
                    ]);
                }
                $account_obj = $account::get($obj['user_id']);
                $account_obj->setInc('quota_money', $value);
                // 
                $this->where('id', $data['id'])->update([
                    'status' => $data['status']
                ]);
            }
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
        }
        return false;
    }



}
