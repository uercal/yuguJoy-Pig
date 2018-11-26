<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\Order as OrderModel;
use app\api\model\Exam as ExamModel;


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

        // 个人资料是否认证  以前status
        $examModel = new ExamModel;
        $infoStatus = $examModel->getStatus($userInfo['user_id']);
        return $this->renderSuccess(compact('userInfo', 'orderCount', 'infoStatus'));
    }

}
