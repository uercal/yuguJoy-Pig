<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\Order as OrderModel;
use app\api\model\Exam as ExamModel;
use app\api\model\OrderAfter;
use app\api\model\AccountMoney;
use app\api\model\Exam;
use app\api\model\User;


/**
 * 个人中心主页
 * Class Index
 * @package app\api\controller\user
 */
class Index extends Controller
{
    /**
     * 获取当前用户信息
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function detail()
    {
        // 当前用户信息
        $userInfo = $this->getUser();
        // 订单总数
        $model = new OrderModel;
        $orderCount = [
            'payment' => $model->getCount($userInfo['user_id'], 'payment'),
            'delivery' => $model->getCount($userInfo['user_id'], 'delivery'),
            'received' => $model->getCount($userInfo['user_id'], 'received'),
            'doing' => $model->getCount($userInfo['user_id'], 'doing')
        ];

        // 售后需要支付的总数
        $_model = new OrderAfter;
        $afterCount = $_model->getPayCount($userInfo['user_id']);

        // 个人资料是否认证  以前status
        $examModel = new ExamModel;
        $infoStatus = $examModel->getStatus($userInfo['user_id']);
        return $this->renderSuccess(compact('userInfo', 'orderCount', 'infoStatus', 'afterCount'));
    }



    public function quotaDetail()
    {
        // 当前用户信息
        $userInfo = $this->getUser();
        // 
        $model = new AccountMoney;
        $account = $model->where(['user_id' => $userInfo['user_id']])->find()->toArray();

        return $this->renderSuccess(compact('account'));
    }


    public function apply($page)
    {
        // 当前用户信息
        $userInfo = $this->getUser();
        // 
        $model = new Exam;
        $list = $model->with('quota')->where(['user_id' => $userInfo['user_id'], 'type' => 10])->order('create_time', 'desc')->paginate(5, false, ['page' => $page, 'list_rows' => 5]);
        return $this->renderSuccess(compact('list'));
    }


    public function getApplyDetail()
    {
        // 当前用户信息
        $userInfo = $this->getUser();
        // 
        $user = new User;
        $applyInfo = $user->with('accountMoney')->where('user_id', $userInfo['user_id'])->find()->append(['license', 'idcard', 'other']);

        return $this->renderSuccess(compact('applyInfo'));
    }


    public function isDoingExam()
    {
        // 当前用户信息
        $userInfo = $this->getUser();
        // 
        $model = new Exam;
        $log = $model->where(['user_id' => $userInfo['user_id'], 'type' => 10])->order('create_time', 'desc')->find();
        if ($log['status'] == 10) {
            return $this->renderError('有正在审核的申请');
        } else {
            return $this->renderSuccess('success');
        }
    }


}
