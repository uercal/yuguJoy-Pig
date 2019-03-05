<style>
    .layui-form {
        margin: 0 auto !important;

    }

    .layui-table-body {
        overflow-x: hidden !important;
    }

    .layui-table-cell {
        height: auto !important;
    }
</style>
<link rel="stylesheet" href="assets/layui/css/layui.css" media="all">

<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="sub" class="am-form tpl-form-line-form" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">订单信息</div>
                            </div>
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="order-empty">
                                            <td colspan="7"></td>
                                        </tr>
                                        <tr>
                                            <td class="am-text-middle am-text-left" colspan="7">
                                                <span class="am-margin-right-lg"> <?= $detail['create_time'] ?></span>
                                                <span class="am-margin-right-lg">订单号：<?= $detail['order_no'] ?></span>
                                            </td>
                                        </tr>
                                        <?php $i = 0;
                                        foreach ($detail['goods'] as $goods) : $i++; ?>
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
                                            <?php if ($i === 1) : $goodsCount = count($detail['goods']); ?>
                                            <td class="am-text-middle" rowspan="<?= $goodsCount ?>">
                                                <p>￥<?= $detail['pay_price'] ?></p>
                                                <p class="am-link-muted">(含运费：￥<?= $detail['express_price'] ?>)</p>
                                            </td>
                                            <td class="am-text-middle" rowspan="<?= $goodsCount ?>">
                                                <p><?= $detail['user']['nickName'] ?></p>
                                                <p class="am-link-muted">(用户id：<?= $detail['user']['user_id'] ?>)</p>
                                                <a href="<?= $detail['user']['avatarUrl'] ?>" title="点击查看大图" target="_blank">
                                                    <img src="<?= $detail['user']['avatarUrl'] ?>" width="72" height="72" alt="">
                                                </a>
                                            </td>
                                            <td class="am-text-middle" rowspan="<?= $goodsCount ?>">
                                                <p>付款状态：
                                                    <span class="am-badge
                                                <?= $detail['pay_status']['value'] === 20 ? 'am-badge-success' : '' ?>">
                                                        <?= $detail['pay_status']['text'] ?></span>
                                                </p>
                                                <p>发货状态：
                                                    <span class="am-badge
                                                <?= $detail['delivery_status']['value'] === 10 ? '' : 'am-badge-success' ?>">
                                                        <?= $detail['delivery_status']['text'] ?></span>
                                                </p>
                                                <p>收货状态：
                                                    <span class="am-badge
                                                <?= $detail['receipt_status']['value'] === 20 ? 'am-badge-success' : '' ?>">
                                                        <?= $detail['receipt_status']['text'] ?></span>
                                                </p>
                                            </td>
                                            <?php endif; ?>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </fieldset>
                    </div>

                    <div class="widget-body  am-margin-bottom-lg">
                        <div class="widget-head am-cf">
                            <div class="widget-title am-fl">用户选择</div>
                        </div>
                        <div class="am-form-group">
                            <table class="layui-table" lay-data="{width: 800, height:'full', url:'<?= url('order/getUserAjax') ?>', page:true, id:'idTest'}" lay-filter="demo">
                                <thead>
                                    <tr>
                                        <th lay-data="{type:'radio'}"></th>
                                        <th lay-data="{field:'user_id', width:80, sort: true}">用户ID</th>
                                        <th lay-data="{field:'nickName', width:160}">微信昵称</th>
                                        <th lay-data="{field:'avatarUrl', width:100,templet:'#avatar'}">微信头像</th>
                                        <th lay-data="{field:'user_name', width:80}">姓名</th>
                                        <th lay-data="{field:'phone', sort: true}">手机号</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    <input type="hidden" name="user_id" value="" id="user_id">

                    <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">
                            <button type="submit" class="j-submit am-btn am-btn-secondary">提交
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script type="text/html" id="avatar">
    <img src="{{d.avatarUrl}}" style="width:100%;height:100%;">
</script>


<script src="assets/layui/layui.all.js" charset="utf-8"></script>
<script>
    layui.use('table', function() {
        var table = layui.table;
        table.on('radio(demo)', function(obj) {
            var checkStatus = table.checkStatus('idTest');
            var user_id = obj.data.user_id;
            $('#user_id').attr('value', '');
            $('#user_id').attr('value', user_id);
            console.log($('#user_id').val());
        });
    });
</script>
<script>
    $(function() {

        /**
         * 表单验证提交
         * @type {*}
         */
        $('#sub').superForm({
            // form data
            buildData: function() {

            },
            // 自定义验证
            validation: function() {
                return true;
            }
        });

    });


    // $('#_sub').on('click', function() {
    //     $('#sub').click();
    // })
</script> 