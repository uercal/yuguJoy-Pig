<?php

namespace app\store\controller;

use app\store\model\Equip as EquipModel;
use app\store\model\EquipCheckLog;
use app\store\model\EquipUsingLog;


/**
 * 统计管理
 * Class Statics
 * @package app\store\controller
 */
class Statics extends Controller
{
    public function index()
    {
        $equip = new EquipModel;
        $list = $equip->getAll();
        $total_count = count($list);
        $status = [10 => '在库', 20 => '运送中', 30 => '使用中', 40 => '维修中', 50 => '停用'];

        $cate = [];
        foreach ($list as $key => $value) {
            $category = $value['goods']['category'];
            $cate[$category['category_id']]['name'] = $category['name'];
            $cate[$category['category_id']]['data'][] = $value;
        }

        foreach ($cate as $key => $value) {
            $data = [];            
            $cate[$key]['count'] = count($value['data']);
            foreach ($value['data'] as $k => $v) {
                $data[$v['status']]['name'] = $status[$v['status']];
                $data[$v['status']]['data'][] = $v;
            }
            foreach ($data as $k => $v) {
                $data[$k]['count'] = count($v['data']);
            }
            $cate[$key]['data'] = $data;
        }
                    
        // 总数统计数据
        $pies = [];
        $pies['text'] = '设备总数';
        $pies['total_count'] = $total_count;
        foreach ($cate as $key => $value) {
            $pies['legend_data'][] = $value['name'];
            $pies['data'][] = [
                'value' => $value['count'],
                'name' => $value['name']
            ];
        }

        // 各个类型设备数据
        $equip = [];
        foreach ($cate as $key => $value) {
            $equip[$key]['text'] = $value['name'];
            $equip[$key]['total_count'] = $value['count'];
            foreach ($value['data'] as $k => $v) {
                $equip[$key]['legend_data'][] = $v['name'];
                $equip[$key]['data'][] = [
                    'value' => $v['count'],
                    'name' => $v['name']
                ];
            }
        }


        return $this->fetch('index', compact('cate', 'pies', 'equip'));
    }





}
