<?php

namespace app\store\model;

use app\common\model\Exam as ExamModel;

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
        // 开启事务
        Db::startTrans();
        try {
            $this->where('id',$data['id'])->update([
                'status'=>$data['status']
            ]);           
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
        }
        return false;
    }



}
