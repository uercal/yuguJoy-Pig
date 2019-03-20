<?php

namespace app\store\controller;

use app\store\model\Category;
use app\store\model\Delivery;
use app\store\model\Goods as GoodsModel;
use app\store\model\GoodsService as GoodsServiceModel;


/**
 * 设备管理控制器
 * Class Goods
 * @package app\store\controller
 */
class Goods extends Controller
{
    /**
     * 设备列表(出售中)
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $model = new GoodsModel;
        $list = $model->getList();
        return $this->fetch('index', compact('list'));
    }

    /**
     * 添加设备
     * @return array|mixed
     */
    public function add()
    {
        if (!$this->request->isAjax()) {
            // 设备分类
            $catgory = Category::getCacheTree();
            // 服务列表
            $service = GoodsServiceModel::getAll();
            return $this->fetch('add', compact('catgory', 'service'));
        }
        $model = new GoodsModel;
        if ($model->add($this->postData('goods'))) {
            return $this->renderSuccess('添加成功', url('goods/index'));
        }
        $error = $model->getError() ? : '添加失败';
        return $this->renderError($error);
    }

    /**
     * 删除设备
     * @param $goods_id
     * @return array
     * @throws \think\exception\DbException
     */
    public function delete($goods_id)
    {
        $model = GoodsModel::get($goods_id);
        if (!$model->remove()) {
            return $this->renderError('删除失败');
        }
        return $this->renderSuccess('删除成功');
    }

    /**
     * 设备编辑
     * @param $goods_id
     * @return array|mixed
     * @throws \think\exception\DbException
     */
    public function edit($goods_id)
    {
        // 设备详情
        $model = GoodsModel::detail($goods_id);
        if (!$this->request->isAjax()) {
            // 设备分类
            $catgory = Category::getCacheTree();    
            // 服务列表
            $service = GoodsServiceModel::getAll();
            $choose_services = array_map(function ($data) {
                return $data['service_id'];
            }, $model['service']->toArray());
            $service = array_map(function ($data) use ($choose_services) {
                in_array($data['service_id'], $choose_services) ? $data['selected'] = true : $data['selected'] = false;
                return $data;
            }, $service);
            // halt($model['spec_rel']);
            // 多规格信息
            $specData = 'null';
            if ($model['spec_type'] === 20)
                $specData = json_encode($model->getManySpecData($model['spec_rel'], $model['spec']));                               
            return $this->fetch('edit', compact('model', 'catgory', 'specData', 'service'));
        }
        // 更新记录        
        if ($model->edit($this->postData('goods'))) {            
            return $this->renderSuccess('更新成功', url('goods/index'));
        }
        $error = $model->getError() ? : '更新失败';
        return $this->renderError($error);
    }







    /**
     * 商品服务
     */
    public function service()
    {
        $model = new GoodsServiceModel;
        $list = $model->getList();
        return $this->fetch('service', compact('list'));
    }


    public function service_add()
    {
        if (!$this->request->isAjax()) {
            return $this->fetch('service_add');
        }
        $model = new GoodsServiceModel;
        if ($model->add($this->postData('service'))) {
            return $this->renderSuccess('添加成功', url('goods/service'));
        }
        $error = $model->getError() ? : '添加失败';
        return $this->renderError($error);
    }

    public function service_edit($service_id)
    {
        $model = GoodsServiceModel::detail($service_id);
        if (!$this->request->isAjax()) {
            return $this->fetch('service_edit', compact('model'));
        }
        // 更新记录
        if ($model->add($this->postData('service'))) {
            return $this->renderSuccess('更新成功', url('goods/service'));
        }
        $error = $model->getError() ? : '更新失败';
        return $this->renderError($error);
    }
}
