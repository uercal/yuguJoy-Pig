<?php
namespace app\store\controller;


use app\store\model\Notice as NoticeModel;
use think\Request;


/**
 * 通知模块
 * Class Index
 * @package app\store\controller
 */
class Notice extends Controller
{
    public function index()
    {
        $notice = new NoticeModel;
        $list = $notice->getList();
        return $this->fetch('index', compact('list'));
    }

    public function add()
    {
        if (!$this->request->isAjax()) {
            return $this->fetch('add');
        }
        $model = new NoticeModel;
        if ($model->add($this->postData('notice'))) {
            return $this->renderSuccess('添加成功', url('notice/index'));
        }
        $error = $model->getError() ? : '添加失败';
        return $this->renderError($error);

    }


    // 获取员工列表
    public function getMemeberAjax()
    {

    }
}
