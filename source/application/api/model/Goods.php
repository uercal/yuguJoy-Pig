<?php

namespace app\api\model;

use think\Cache;
use app\common\model\Goods as GoodsModel;
use think\Request;

/**
 * 商品模型
 * Class Goods
 * @package app\api\model
 */
class Goods extends GoodsModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'sales_initial',
        'sales_actual',
        'is_delete',
        'wxapp_id',
        'create_time',
        'update_time'
    ];

    /**
     * 商品详情：HTML实体转换回普通字符
     * @param $value
     * @return string
     */
    public function getContentAttr($value)
    {
        return htmlspecialchars_decode($value);
    }

    /**
     * 根据商品id集获取商品列表 (购物车列表用)
     * @param $goodsIds
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getListByIds($goodsIds)
    {
        return $this->with(['category', 'image.file', 'spec', 'spec_rel.spec', 'delivery.rule'])
            ->where('goods_id', 'in', $goodsIds)->select();
    }

    // 根据cateid获取商品列表
    public function getListByCate($cate_id)
    {
        return $this->with(['category', 'image.file', 'spec', 'spec_rel.spec', 'delivery.rule'])
            ->where('category_id', '=', $cate_id)->select();
    }

    public function getAllGoods()
    {
        return $this->with(['category', 'image.file', 'spec', 'service.service', 'spec_rel.spec', 'delivery.rule'])
            ->select();
    }


    public function getListCollect($ids, $search = '', $sortType = 'all', $sortPrice = false)
    {
        // 筛选条件
        $filter = [];
        $filter['goods_status'] = ['=', 10];
        $filter['goods_id'] = ['in', $ids];
        !empty($search) && $filter['goods_name'] = ['like', '%' . trim($search) . '%'];

        // 排序规则
        $sort = [];
        if ($sortType === 'all') {
            $sort = ['goods_sort', 'goods_id' => 'desc'];
        } elseif ($sortType === 'sales') {
            $sort = ['goods_sales' => 'desc'];
        } elseif ($sortType === 'price') {
            $sort = $sortPrice ? ['goods_max_price' => 'desc'] : ['goods_min_price'];
        }
        // 商品表名称
        $tableName = $this->getTable();
        // 多规格商品 最高价与最低价
        $GoodsSpec = new GoodsSpec;
        $minPriceSql = $GoodsSpec->field(['MIN(goods_price)'])
            ->where('goods_id', 'EXP', "= `$tableName`.`goods_id`")->buildSql();
        $maxPriceSql = $GoodsSpec->field(['MAX(goods_price)'])
            ->where('goods_id', 'EXP', "= `$tableName`.`goods_id`")->buildSql();
        // 执行查询
        $list = $this->field([
            '*', '(sales_initial + sales_actual) as goods_sales',
            "$minPriceSql AS goods_min_price",
            "$maxPriceSql AS goods_max_price"
        ])->with(['category', 'image.file', 'spec'])
            ->where('is_delete', '=', 0)
            ->where($filter)
            ->order($sort)
            ->paginate(15, false, [
                'query' => Request::instance()->request()
            ]);
        return $list;
    }














    public function getManySpecDataApi($spec_rel, $skuData)
    {
        // spec_attr
        $specAttrData = [];
        foreach ($spec_rel->toArray() as $item) {
            if (!isset($specAttrData[$item['spec_id']])) {
                $specAttrData[$item['spec_id']] = [
                    'group_id' => $item['spec']['spec_id'],
                    'group_name' => $item['spec']['spec_name'],
                    'spec_items' => [],
                ];
            }
            // 筛选规格 金额为零 的规格，区分            
            $specAttrData[$item['spec_id']]['spec_items'][] = [
                'item_id' => $item['spec_value_id'],
                'spec_value' => $item['spec_value'],
                'spec_value_id' => $item['spec_value_id'],
            ];
        }
        $specAttrData = array_values($specAttrData);


        $spec_sku_info = [];
        foreach ($specAttrData[1]['spec_items'] as $key => $value) {
            $spec_sku_info[$value['spec_value_id']] = $value;
        }
        

        // spec_list
        $specListData = [];
        foreach ($skuData->toArray() as $item) {
            if ($item['goods_price'] == "0") continue;
            $specListData = [
                'goods_spec_id' => $item['goods_spec_id'],
                'spec_sku_id' => $item['spec_sku_id'],
                'rows' => [],
                'form' => [
                    'goods_price' => $item['goods_price'],
                    'stock_num' => $item['stock_num'],
                ],
            ];

            $parent_sku_id = explode('_', $item['spec_sku_id'])[0];
            $child_sku_id = explode('_', $item['spec_sku_id'])[1];


            foreach ($specAttrData[0]['spec_items'] as $key => $value) {
                if (!isset($specAttrData[0]['spec_items'][$key]['child'])) {
                    $specAttrData[0]['spec_items'][$key]['child_name'] = $specAttrData[1]['group_name'];
                    $specAttrData[0]['spec_items'][$key]['child'] = [];
                }
                if ($value['spec_value_id'] == $parent_sku_id) {
                    $specListData['spec_info'] = $spec_sku_info[$child_sku_id];
                    $specAttrData[0]['spec_items'][$key]['child'][] = $specListData;
                }
            }

        }
        // 


        return ['spec_attr' => $specAttrData[0]];
    }
}
