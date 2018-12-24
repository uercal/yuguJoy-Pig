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

}
