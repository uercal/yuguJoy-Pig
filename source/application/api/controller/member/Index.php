<?php

namespace app\api\controller\member;

use app\api\controller\Controller;
use app\api\model\Order as OrderModel;
use app\api\model\Exam as ExamModel;


/**
 * 员工端个人中心主页
 * Class Index
 * @package app\api\controller\user
 */
class Index extends Controller
{
    /**
     * 获取当前员工信息
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function detail()
    {
        // 当前员工信息
        $memberInfo = $this->getMember();
        
        // 
        
        return $this->renderSuccess(compact('memberInfo'));
    }

}
