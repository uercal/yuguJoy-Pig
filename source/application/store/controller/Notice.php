<?php
namespace app\store\controller;


use app\store\model\Notice as NoticeModel;
use app\store\model\Member as MemberModel;
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


    public function detail($id)
    {
        $notice = NoticeModel::find($id)->append(['member'])->toArray();
        return $this->fetch('detail', compact('notice'));
    }


    // 获取员工列表
    public function getMemberAjax()
    {
        $model = new MemberModel;
        $list = $model->getListAjax();
        $list = $list->toArray();           
        return [
            'code' => 0,
            'msg' => '',
            'count' => $list['total'],
            'data' => $list['data']
        ];

    }



    // 删除
    public function delete($id)
    {
        $model = NoticeModel::get($id);
        if (!$model->remove()) {
            return $this->renderError('删除失败');
        }
        return $this->renderSuccess('删除成功');
    }
}
