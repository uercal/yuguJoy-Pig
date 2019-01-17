<?php

namespace app\store\model;

use app\common\model\Exam as ExamModel;
use app\store\model\Quota;
use app\common\model\AccountMoney;
use app\common\model\User;
use app\common\model\PayLog;

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
            // 用户认证
            'user_name' => '个人姓名',
            'company' => '公司名称',
            'org_code_id' => '组织机构代码证',
            'license_id' => '营业执照',
            'idcard_ids' => '身份证正反面',
            'other_content' => '其他信息',
            'other_ids' => '其他图片',
            // 送达审批
            'order_id' => '查看订单',
            'send_content' => '送达说明',
            'send_pic_ids' => '送达图片说明',
            //线下提现
            'cash_price' => '提现金额',
        ];
        return $data;
    }


    public function updateStatus($data)
    {                                
        // 
        $obj = $this->where('id', $data['id'])->find();

        $content = $data['content'];
        // 筛选空值 content
        foreach ($content as $key => $value) {
            if (empty($value)) unset($content[$key]);
            if ($key == 'idcard_ids') {
                if ($value == "0,0") unset($content[$key]);
            }
            if ($key == 'other_ids') {
                if ($value == "0,0,0,0,0,0") unset($content[$key]);
            }
        }                
        
        // halt($data);
        // 开启事务
        Db::startTrans();
        try {            
            // $type==10 用户认证 获得额度
            if ($obj['type'] == 10) {
                $model = new Quota;
                $value = bcmul($data['quota_money'], 100, 0);
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

                
                // 用户认证 更新用户资料
                $user = new User;
                $user->save($content, ['user_id' => $obj['user_id']]);
            }
            // $type==30 线下提现 确认
            if ($obj['type'] == 30) {

                if ($data['status'] == 20) {
                    // 扣掉余额。
                    $price = $content['cash_price'];
                    $account = new AccountMoney;
                    $account_obj = $account::get($obj['user_id']);
                    $account_obj->setDec('account_money', $price * 100);
                    // payLog 添加                    
                    $payLog = new PayLog;
                    $payLog->save([
                        'pay_type' => 40,//提现                        
                        'pay_price' => $price,
                        'user_id' => $obj['user_id']
                    ]);

                }

                $this->where('id', $data['id'])->update([
                    'status' => $data['status']
                ]);

            }
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            halt($e);
        }
        return false;
    }



}
