<?php

namespace app\api\controller\member;

use app\api\controller\Controller;
use app\api\model\Order as OrderModel;
use app\api\model\Equip as EquipModel;

/**
 * 
 * Class Index
 * @package app\api\controller\user
 */
class Equip extends Controller
{

    /**
     * 解析设备二维码
     */
    public function decrypt($code)
    {
        $equip_id = EquipModel::decryptStrin($code);
        $detail = EquipModel::detail($equip_id);
        return $this->renderSuccess(compact('detail'));
    }


    /**
     * 获取列表
     */
    public function list($filter, $page)
    {
        $filter = json_decode(htmlspecialchars_decode($filter), true);
        foreach ($filter as $key => $value) {
            if (!$value) {
                unset($filter[$key]);
            }
        }
        $filter = empty($filter) ? [] : $filter;
        $model = new EquipModel;
        $list = $model->getFilterList($filter, $page);
        return $this->renderSuccess(compact('list'));
    }


    /**
     * 获取筛选选项
     */
    public function option()
    {
        $model = new EquipModel;
        $cate_option = $model->getCateArr();
        return $this->renderSuccess(compact('cate_option'));
    }


    /**
     * 搜索设备
     */
    public function serach($equip_id)
    {
        $obj = EquipModel::get($equip_id);
        $obj = $obj != null;
        return $this->renderSuccess($obj);
    }


    /**
     * xiangqing
     */
    public function detail($equip_id)
    {
        $model = new EquipModel;
        $detail = $model->detail($equip_id);
        return $this->renderSuccess(compact('detail'));
    }



    /**
     * 维修权限
     */
    public function checkPermission()
    {
        $memberInfo = $this->getMember();        
        // 权限接口json
        $role = $memberInfo['role'];
        $menu = [];
        foreach ($role['api_menu'] as $key => $value) {
            $role = json_decode($value);
            $header = explode('/', $role[0])[0];
            $menu[$header] = $role;
        }

        foreach ($menu as $key => $value) {
            if ($key == 'equip') {
                if (in_array('equip/checkLog', $value)) {
                    return $this->renderSuccess(['check' => 1]);
                }
            }
        }
        return $this->renderSuccess(['check' => 0]);
    }


    /**
     * 设备更换状态   停用/在库
     */
    public function changeStatus($equip_id)
    {
        $model = EquipModel::get($equip_id);
        $memberInfo = $this->getMember();
        $member_id = $memberInfo['id'];
        if ($model->changeStatus($member_id)) {
            return $this->renderSuccess('success');
        } else {
            return $this->renderError('error');
        }
    }


    /**
     * isChecking  设备是否处于维修中（维修记录ing）
     */
    public function isChecking($equip_id)
    {
        $model = new EquipModel;
        $isCheck = $model->isChecking($equip_id);
        return $this->renderSuccess($isCheck);
    }



    /**
     * 开始维修
     */
    public function startCheck($equip_id)
    {
        $model = EquipModel::get($equip_id);
        $memberInfo = $this->getMember();
        $member_id = $memberInfo['id'];
        if ($model->startCheck($member_id)) {
            return $this->renderSuccess('success');
        } else {
            return $this->renderError('error');
        }
    }


    /**
     * 当前维修状态
     */
    public function checkDetail($equip_id)
    {
        $model = new EquipModel;
        $checkDetail = $model->getCheckDetail($equip_id);
        return $this->renderSuccess(compact('checkDetail'));
    }


    /**
     * 完结维修
     */
    public function checkingRes($equip_id, $content, $result)
    {
        $model = EquipModel::get($equip_id);
        $memberInfo = $this->getMember();
        $member_id = $memberInfo['id'];
        if ($model->checkingRes($member_id, $content, $result)) {
            return $this->renderSuccess('success');
        } else {
            return $this->renderError('error');
        }
    }

}
