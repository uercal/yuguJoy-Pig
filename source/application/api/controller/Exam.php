<?php

namespace app\api\controller;


use app\api\model\Exam as ExamModel;



class Exam extends Controller
{
    // 用户信息认证审核
    public function userInfoExam(){
        $model = new ExamModel;
        if ($model->add($this->getUser(), $this->request->post())) {
            return $this->renderSuccess([], '添加成功');
        }
        return $this->renderError('添加失败');
    }
}
