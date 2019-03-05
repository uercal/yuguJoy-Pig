<?php 
$a = array_column(array_column($menus['order'], null, 'index')[0], null, 'index');
if (isset($a['order/order_migrate'])) :
    print_r($a);
    ?>

<style>
    .tpl-table-black-operation a.tpl-table-black-operation-war {
        border: 1px solid orange;
        color: orange;
    }

    .tpl-table-black-operation a.tpl-table-black-operation-war:hover {
        background: orange;
        color: #fff;
    }

    .tpl-table-black-operation a.tpl-table-black-operation-dis {
        border: 1px solid #e9e9e9;
        color: #e9e9e9;
    }

    .tpl-table-black-operation a.tpl-table-black-operation-dis:hover {
        background: unset;
        color: #e9e9e9;
    }
</style>
<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf">
                    <div class="widget-title am-cf" style="display:flex;">
                        <p>
                            迁移订单
                        </p>
                        <form method="GET" action="" id="form">
                            <div class="am-form-group" style="position:absolute;right:20px;">
                                <div class="am-btn-toolbar">
                                    <div class="am-btn-group am-btn-group-xs" style="display:flex;">
                                        <a class="am-btn am-btn-default am-btn-primary am-radius" href="javascript:;">
                                            <span class="am-icon-home"></span> 订单号
                                        </a>
                                        <input type="text" class="am-form-field" name="order_no" style="padding: 3px 5px;" placeholder="订单号" value="<?= isset($map['order_no']) ? $map['order_no'] : "" ?>">
                                    </div>

                                    <div class="am-btn-group am-btn-group-xs">
                                        <a class="am-btn am-btn-default am-btn-success am-radius" id="search" href="javascript:;">
                                            <span class="am-icon-search"></span> 搜索
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="widget-body am-fr">
                    <!--  -->
                    <!--  -->
                    <div class="order-list am-scrollable-horizontal am-u-sm-12 am-margin-top-xs">
                        <table width="100%" class="am-table am-table-centered
                        am-text-nowrap am-margin-bottom-xs">
                            <thead>
                                <tr>
                                    <th width="30%" class="goods-detail">商品信息</th>
                                    <th width="10%">单价/数量</th>
                                    <th width="15%">实付款</th>
                                    <th>买家</th>
                                    <th>交易状态</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($list)) : foreach ($list as $order) : ?>
                                <tr class="order-empty">
                                    <td colspan="7"></td>
                                </tr>
                                <tr>
                                    <td class="am-text-middle am-text-left" colspan="7">
                                        <span class="am-margin-right-lg"> <?= $order['create_time'] ?></span>
                                        <span class="am-margin-right-lg">订单号：<?= $order['order_no'] ?></span>
                                    </td>
                                </tr>
                                <?php $i = 0;
                                foreach ($order['goods'] as $goods) : $i++; ?>
                                <tr>
                                    <td class="goods-detail am-text-middle">
                                        <div class="goods-image">
                                            <img src="<?= $goods['image']['file_path'] ?>" alt="">
                                        </div>
                                        <div class="goods-info">
                                            <p class="goods-title"><?= $goods['goods_name'] ?></p>
                                            <p class="goods-spec am-link-muted">
                                                <?= $goods['goods_attr'] ?>
                                            </p>
                                        </div>
                                    </td>
                                    <td class="am-text-middle">
                                        <p>￥<?= $goods['goods_price'] ?></p>
                                        <p>×<?= $goods['total_num'] ?></p>
                                    </td>
                                    <?php if ($i === 1) : $goodsCount = count($order['goods']); ?>
                                    <td class="am-text-middle" rowspan="<?= $goodsCount ?>">
                                        <p>￥<?= $order['pay_price'] ?></p>
                                        <p class="am-link-muted">(含运费：￥<?= $order['express_price'] ?>)</p>
                                    </td>
                                    <td class="am-text-middle" rowspan="<?= $goodsCount ?>">
                                        <p><?= $order['user']['nickName'] ?></p>
                                        <p class="am-link-muted">(用户id：<?= $order['user']['user_id'] ?>)</p>
                                        <a href="<?= $order['user']['avatarUrl'] ?>" title="点击查看大图" target="_blank">
                                            <img src="<?= $order['user']['avatarUrl'] ?>" width="72" height="72" alt="">
                                        </a>
                                    </td>
                                    <td class="am-text-middle" rowspan="<?= $goodsCount ?>">
                                        <p>付款状态：
                                            <span class="am-badge
                                                <?= $order['pay_status']['value'] === 20 ? 'am-badge-success' : '' ?>">
                                                <?= $order['pay_status']['text'] ?></span>
                                        </p>
                                        <p>发货状态：
                                            <span class="am-badge
                                                <?= $order['delivery_status']['value'] === 10 ? '' : 'am-badge-success' ?>">
                                                <?= $order['delivery_status']['text'] ?></span>
                                        </p>
                                        <p>收货状态：
                                            <span class="am-badge
                                                <?= $order['receipt_status']['value'] === 20 ? 'am-badge-success' : '' ?>">
                                                <?= $order['receipt_status']['text'] ?></span>
                                        </p>
                                    </td>
                                    <td class="am-text-middle" rowspan="<?= $goodsCount ?>">
                                        <div class="tpl-table-black-operation">
                                            <a class="tpl-table-black-operation-war" data-id="<?= $order['order_id'] ?>" onclick="migrate(<?= $order['order_id'] ?>)">
                                                迁移</a>
                                        </div>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach; ?>
                                <?php endforeach;
                        else : ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="am-modal am-modal-confirm" tabindex="-1" id="my-confirm">
    <div class="am-modal-dialog">
        <div class="am-modal-hd">确认</div>
        <div class="am-modal-bd">
            确定要删除这条订单吗？
        </div>
        <div class="am-modal-footer">
            <span class="am-modal-btn" data-am-modal-cancel>取消</span>
            <span class="am-modal-btn" data-am-modal-confirm>确定</span>
        </div>
    </div>
</div>
<script>
    $(function() {
        var $modal = $('#your-modal');
        $('#search').on('click', function(e) {
            var url = "<?php echo url() ?>";
            var param = $('#form').serialize();
            var html = url + '&' + param;
            window.location.href = html;
        });
    });
</script>
<script>
    $(function() {
        
    })

    function migrate(order_id) {
        var order_id = order_id;
        var url = '<?= url('migrate') ?>&order_id='+order_id;
        window.location.href = url;
    }


</script>


















<?php endif; ?> 