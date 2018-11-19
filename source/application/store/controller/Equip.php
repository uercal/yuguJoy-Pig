<?php

namespace app\store\controller;

use app\store\model\Goods as GoodsModel;
use app\store\model\Equip as EquipModel;
use app\api\model\Goods as GoodsApiModel;
use app\store\model\EquipCheckLog;
use app\store\model\EquipUsingLog;
// 
use Endroid\QrCode\QrCode;
use app\store\model\Member as MemberModel;

/**
 * 设备管理控制器
 * Class Goods
 * @package app\store\controller
 */
class Equip extends Controller
{
    /**
     * 设备列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $model = new EquipModel();
        $goods = GoodsModel::getChooseList();
        $res = $model->getList();
        $list = $res['data'];
        $map = $res['map'];
        return $this->fetch('index', compact('list', 'map', 'goods'));
    }

    /**
     * 添加设备
     * @return array|mixed
     */
    public function add()
    {
        if (!$this->request->isAjax()) {
            // 设备分类
            $goods = GoodsModel::getChooseList();
            return $this->fetch('add', compact('goods'));
        }
        $model = new EquipModel;
        if ($model->add($this->postData('equip'))) {
            return $this->renderSuccess('添加成功', url('equip/index'));
        }
        $error = $model->getError() ? : '添加失败';
        return $this->renderError($error);
    }

    /**
     * 删除设备
     * @param $equip_id
     * @return array
     * @throws \think\exception\DbException
     */
    public function delete($equip_id)
    {
        $model = GoodsModel::get($equip_id);
        if (!$model->remove()) {
            return $this->renderError('删除失败');
        }
        return $this->renderSuccess('删除成功');
    }

    /**
     * 设备编辑
     * @param $equip_id
     * @return array|mixed
     * @throws \think\exception\DbException
     */
    public function edit($equip_id)
    {
        // 设备详情
        $model = EquipModel::detail($equip_id);
        if (!$this->request->isAjax()) {
            // 产品
            $spec_list = $this->getSku($model['goods_id']);
            $goods = GoodsModel::getChooseList();
            return $this->fetch('edit', compact('model', 'goods', 'spec_list'));
        }
        // 更新记录
        if ($model->edit($this->postData('equip'))) {
            return $this->renderSuccess('更新成功', url('equip/index'));
        }
        $error = $model->getError() ? : '更新失败';
        return $this->renderError($error);
    }



    public function getSku($goods_id)
    {
        $detail = GoodsModel::detail($goods_id);
        $model = new GoodsApiModel();
        $specData = $model->getManySpecData($detail['spec_rel'], $detail['spec']);
        $data = $specData['spec_attr'];

        return $data;
    }


    public function getOne($equip_id)
    {
        $equip = new EquipModel;
        $detail = $equip->getOne($equip_id);

        if ($detail['status'] != 10) {
            $data = [];
            $msg = '该设备非在库状态！';
            $code = 0;
        } else {
            $code = 1;
            $data = $detail;
            $msg = '';
        }

        $res = ['code' => $code, 'msg' => $msg, 'data' => $data];
        return $res;
    }


    // QR
    public function madeQrCode($equip_id)
    {
        $string = $this->ecryptdString($equip_id);
        $qrCode = new qrCode($string);
        // 
        $qrCode->setSize(300);
        header('Content-Type: ' . $qrCode->getContentType());
        echo $qrCode->writeString();
        exit;
    }




    function ecryptdString($str)
    {
        $keys = 'uercal,';
        $iv = ',xiaocaizhu';
        $encrypted_string = base64_encode($keys . $str . $iv);
        $encrypted_string = substr($encrypted_string, 0, -2);
        // halt([$encrypted_string,$this->decryptStrin($encrypted_string)]);
        return $encrypted_string;
    }

    function decryptStrin($str)
    {
        $str .= "==";
        $decrypted_string = base64_decode($str);
        $data = explode(',', $decrypted_string);
        return $data[1];
    }



    /**
     * 维修记录
     */
    public function checkLog()
    {
        $model = new EquipCheckLog();
        $res = $model->getList();
        $map = $res['map'];
        $list = $res['data'];
        return $this->fetch('check_index', compact('list', 'map'));
    }

    public function checkAdd()
    {
        if (!$this->request->isAjax()) {
            return $this->fetch('check_add');
        }
        $model = new EquipCheckLog;
        if ($model->add($this->postData('post'))) {
            return $this->renderSuccess('添加成功');
        }
        return $this->renderError('添加失败');
    }


    // 维修记录接口
    public function checkAjax()
    {
        $input = input();
        $type = $input['type'];
        if ($type == 'equip') {
            $model = new EquipModel;
            $data = $model->getOne($input['equip_id'], ['<>', 0]);
            return $data;
        }
        if ($type == 'member') {
            $model = new MemberModel;
            $data = $model->getOne(['phone' => $input['member_phone']]);
            return $data;
        }
    }



    // 使用记录列表
    public function usingLog()
    {
        $log = new EquipUsingLog;
        $res = $log->getList();
        $map = $res['map'];
        $list = $res['data'];
        return $this->fetch('using_index', compact('map', 'list'));

    }


    // 使用记录详情
    public function usingDetail()
    {
        $log = new EquipUsingLog;
        $res = $log->EquipDetail();
        $map = $res['map'];
        $data = $res['data'];
        // halt($data->toArray());
        // halt($data->toArray()[0]['equip']);
        return $this->fetch('using_detail', compact('map', 'data'));
    }

}
