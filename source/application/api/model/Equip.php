<?php

namespace app\api\model;

use app\common\model\Equip as EquipModel;
use think\Cache;
use think\Db;


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


    public function getFilterList($filter, $page)
    {
        if (empty($filter)) {
            return $this->with(['goodsGetName', 'specValue'])->order('create_time', 'desc')->paginate(12, false, ['page' => $page, 'list_rows' => 12]);
        } else {

            $map = [];
            isset($filter['status']) ? $map['status'] = ['=', $filter['status']] : '';
            isset($filter['start_time']) ? $map['create_time'][] = ['>=', strtotime($filter['start_time'])] : '';
            isset($filter['end_time']) ? $map['create_time'][] = ['<=', strtotime($filter['end_time'])] : '';
            // halt($map);
            if (isset($filter['cate_id'])) {
                return $this->with(['goodsGetName', 'specValue'])->where('goods_id', 'IN', function ($query) use ($filter) {
                    $query->name('goods')->where('category_id', $filter['cate_id'])->field('goods_id');
                })->where($map)->order('create_time', 'desc')->paginate(12, false, ['page' => $page, 'list_rows' => 12]);
            } else {
                return $this->with(['goodsGetName', 'specValue'])->where($map)->order('create_time', 'desc')->paginate(12, false, ['page' => $page, 'list_rows' => 12]);
            }

        }
    }


    public function getCateArr()
    {
        return Db::name('category')->select()->toArray();
    }
}
