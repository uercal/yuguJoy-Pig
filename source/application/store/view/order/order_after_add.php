<style>
.am-text-nowrap {
    white-space: unset;
}
.am-service{
    /* margin-bottom:unset; */
}
.order-detail-progress li{
    width:16.6666%;
}



.order-detail-progress.progress-7 li:nth-child(7) {
  color: #fff;
}

</style>

<link rel="stylesheet" href="assets/layui/css/layui.css"  media="all">


<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-body  am-margin-bottom-lg">                    
                    <div class="widget-head am-cf">
                        <div class="widget-title am-fl">基本信息</div>
                    </div>
                    <div class="am-scrollable-horizontal">
                        <table class="regional-table am-table am-table-bordered am-table-centered
                            am-text-nowrap am-margin-bottom-xs">
                            <tbody>
                            <tr>
                                <th>订单号</th>
                                <th>实付款</th>
                                <th>买家</th>
                                <th>交易状态</th>
                            </tr>
                            <tr>
                                <td><?= $detail['order_no'] ?></td>
                                <td>
                                    <p>￥<?= $detail['pay_price'] ?></p>
                                    <p class="am-link-muted">(含运费：￥<?= $detail['express_price'] ?>)</p>
                                </td>
                                <td>
                                    <p><?= $detail['user']['nickName'] ?></p>
                                    <p class="am-link-muted">(用户id：<?= $detail['user']['user_id'] ?>)</p>
                                </td>
                                <td>
                                    <p>付款状态：
                                        <span class="am-badge
                                        <?= $detail['pay_status']['value'] === 20 ? 'am-badge-success' : '' ?>">
                                                <?= $detail['pay_status']['text'] ?></span>
                                    </p>
                                    <p>发货状态：
                                        <span class="am-badge
                                        <?= $detail['delivery_status']['value'] === 20 ? 'am-badge-success' : '' ?>">
                                                <?= $detail['delivery_status']['text'] ?></span>
                                    </p>
                                    <p>收货状态：
                                        <span class="am-badge
                                        <?= $detail['receipt_status']['value'] === 20 ? 'am-badge-success' : '' ?>">
                                                <?= $detail['receipt_status']['text'] ?></span>
                                    </p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="widget-head am-cf">
                        <div class="widget-title am-fl">商品信息(大于3月先收3月租金)</div>
                    </div>
                    <div class="am-scrollable-horizontal">
                        <table class="regional-table am-table am-table-bordered am-table-centered
                            am-text-nowrap am-margin-bottom-xs">
                            <tbody>
                            <tr>
                                <th>商品名称(悬浮查看型号)</th>                                                                                                                         
                                <th>租赁模式(悬浮查看内容)</th>
                                <th>增值服务</th>
                                <th>起租时间</th>
                                <th>起租时长</th>
                                <th>租金单价</th>                                                            
                                <th>租金</th>
                                <th>押金</th>
                                <th>购买数量</th>
                                <th>总价</th>
                            </tr>
                            <?php foreach ($detail['goods'] as $goods) : ?>                            
                                <tr>
                                    <td class="goods-detail am-text-middle rent_content" data-content="型号:<?= $goods['spec_value'] ?>">
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
                                    <td data-content="<?= $goods['rent_mode']['content'] ?>" class="rent_content"><?= $goods['rent_mode']['name'] ?></td>
                                    <!-- 增值服务 -->
                                    <td style="font-size:12px;">
                                        <p><?= $goods['secure'] == 0 ? '标准保(免费)' : '意外保' ?></p>                                    
                                        <?php foreach ($goods['service_info'] as $i) : ?>
                                            <p><?= $i['service_name'] ?></p>
                                        <?php endforeach; ?>                                        
                                    </td>
                                    <td><?= date('Y-m-d', $goods['rent_date']) ?></td>
                                    <td>
                                        <?php if ($goods['rent_mode']['is_static'] == 0) : ?><?= $goods['rent_num'] ?>天
                                        <?php else : ?><?= $goods['rent_num'] ?>个月<?php if ($goods['rent_num'] > 3) : ?><p style="color:#777;font-size:12px;">(大于3个月先收3月租金)</p>
                                        <?php endif;
                                        endif; ?>
                                    </td>         
                                    <td>￥<?= $goods['rent_price'] ?></td>                           
                                    <td>￥<?= $goods['rent_total_price'] ?></td>
                                    <td>￥<?= $goods['goods_price'] ?></td>
                                    <td>×<?= $goods['total_num'] ?></td>
                                    <td>￥<?= $goods['total_price'] ?></td>
                                </tr>
                                
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="10" class="am-text-right">总计金额：￥<?= $detail['total_price'] ?></td>                                
                            </tr>                            
                            </tbody>
                        </table>                        
                                                                                                                                               


                        <div class="widget-head am-cf">
                            <div class="widget-title am-fl">操作</div>
                        </div>                     
                        
                        <form id="my-form" class="am-form tpl-form-line-form" method="post">

                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">故障说明 </label>
                                <div class="am-u-sm-9 am-u-end">                                    
                                    <textarea name="after[request_text]" id="" cols="30" rows="10" required></textarea>
                                </div>
                            </div>    
                                                      
                            <input type="hidden" name="after[user_id]" value="<?= $detail['user_id'] ?>">

                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">
                                    <button type="submit" class="j-submit am-btn am-btn-sm am-btn-secondary" id="sub">
                                        发起售后
                                    </button>
                                </div>
                            </div>
                                      
                        </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    $(function () {
       
        /**
         * 表单验证提交
         * @type {*}
         */
        
        $('#my-form').superForm({
            // 自定义验证
            validation: function () {                
                return true;
            }
        })        
                
    });

  
</script>
