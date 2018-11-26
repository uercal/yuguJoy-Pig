<?php

namespace app\api\controller;


use app\api\model\Member as MemberModel;



class Member extends Controller
{
    // 
    public function login()
    {
        $model = new MemberModel;
        $res = $model->login($this->request->post());
        if($res){            
            $member_id = $res['id'];
            return $this->renderSuccess(compact('member_id'));
        }
        return $this->renderError('手机号或密码错误',[]);
    }
}
