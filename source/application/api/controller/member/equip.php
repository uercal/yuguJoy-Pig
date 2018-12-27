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
}
