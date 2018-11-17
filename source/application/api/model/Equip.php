<?php

namespace app\api\model;

use app\common\model\Equip as EquipModel;
use think\Cache;


class Equip extends EquipModel
{
    public function specType($goods_id)
    {
        $data = $this->field(['spec_value_id', 'type'])->where('goods_id', $goods_id)->group('spec_value_id,type')->select()->toArray();
        return $this->getTypeText($data);
    }


    public function getTypeText($data)
    {
        $type = [10 => '笔记本', 20 => '打印机'];
        foreach ($data as $key => $value) {
            $data[$key]['type_text'] = $type[$value['type']];
        }
        $_data = [];
        foreach ($data as $key => $value) {
            if (!isset($_data[$value['spec_value_id']])) {
                $_data[$value['spec_value_id']] = [];
            }
            $_data[$value['spec_value_id']][] = $value;

        }
        return $_data;
    }
}
