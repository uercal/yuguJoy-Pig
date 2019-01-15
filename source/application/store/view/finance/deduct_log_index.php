<style>


</style>
<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf" style="display:flex;position:relative;">
                    <div class="widget-title am-cf">扣款记录</div>     
                    <!-- 搜索栏 -->
                    <form method="GET" action="" id="form">
                        <div class="am-form-group" style="position:absolute;right:20px;">
                            <div class="am-btn-toolbar">                                                                
                                <div class="am-btn-group am-btn-group-xs" style="display:flex;">
                                    <a class="am-btn am-btn-default am-radius"
                                    href="javascript:;">
                                        <span class="am-icon-home"></span> 用户id                                        
                                    </a>
                                    <input type="text" class="am-form-field" name="user_id" style="padding: 3px 5px;" placeholder="用户id" value="<?= isset($map['user_id']) ? $map['user_id'] : "" ?>">
                                </div>
                                <div class="am-btn-group am-btn-group-xs" style="display:flex;">
                                    <a class="am-btn am-btn-default am-radius"
                                    href="javascript:;">
                                        <span class="am-icon-home"></span> 订单id                                        
                                    </a>
                                    <input type="text" class="am-form-field" name="order_id" style="padding: 3px 5px;" placeholder="订单id" value="<?= isset($map['order_id']) ? $map['order_id'] : "" ?>">
                                </div>
                                
                                <div class="am-btn-group am-btn-group-xs">
                                    <a class="am-btn am-btn-default am-btn-success am-radius" id="search"
                                    href="javascript:;">
                                        <span class="am-icon-search"></span> 搜索
                                    </a>
                                </div>
                            </div>                            
                        </div>
                    </form>
                    
                </div>        
                                     
                <div class="widget-body am-fr">                    
                    <div class="am-scrollable-horizontal am-u-sm-12">                        
                        <!--  -->
                        <table width="100%" class="am-table am-table-compact am-table-striped
                         tpl-table-black am-text-nowrap">
                            <thead>
                            <tr>
                                <th>订单ID</th>
                                <th>用户ID</th>
                                <th>用户头像</th>
                                <th style="width:10%;">订单商品</th>
                                <th>对应时间段</th> 
                                <th>租赁模式</th>
                                <th>扣款单价（月&日）</th>                                                                                                                    
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!$list->isEmpty()) : foreach ($list as $item) : ?>                                
                                <tr>
                                    <td class="am-text-middle"><?= $item['deduct']['order_id'] ?></td>
                                    <td class="am-text-middle"><?= $item['deduct']['user']['user_id'] ?></td>
                                    <td class="am-text-middle">
                                        <a href="<?= $item['deduct']['user']['avatarUrl'] ?>" title="点击查看大图" target="_blank">
                                            <img src="<?= $item['deduct']['user']['avatarUrl'] ?>" width="40" height="40" alt="">
                                        </a>                                        
                                    </td>
                                    <td class="am-text-middle"><?= $item['deduct']['order_goods']['goods_name'] ? : '--' ?></td>
                                    <td class="am-text-middle"><?= date('Y-m-d', $item['start_time']) . ' 至 ' . date('Y-m-d', $item['end_time']) ?></td>
                                    <td class="am-text-middle"> <?= $item['deduct']['rent_mode']['name'] ?> </td>                                    
                                    <td class="am-text-middle"> <?= $item['deduct']['deduct_price'] ?> </td>                                                                        
                                </tr>                                
                            <?php endforeach;
                            else : ?>
                                <tr>
                                    <td colspan="8" class="am-text-center">暂无记录</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="am-u-lg-12 am-cf">
                        <div class="am-fr"><?= $list->render() ?> </div>
                        <div class="am-fr pagination-total am-margin-right">
                            <div class="am-vertical-align-middle">总记录：<?= $list->total() ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>
<script>
    $(function () {
        var $modal = $('#your-modal');                
        $('#search').on('click', function(e) {
            var url = "<?php echo url('finance/deduct') ?>";
            var param = $('#form').serialize();
            var html = url + '&' + param;
            window.location.href = html;            
        });    
    });
</script>
<script>
    $(function(){
        var b = "<?= isset($map['startDate']) ? $map['startDate'] : "" ?>";
        var e = "<?= isset($map['endDate']) ? $map['endDate'] : "" ?>";
        var startDate = new Date(b);
        var endDate = new Date(e);                             
        // 
        $('#my-start').datepicker().on('changeDate.datepicker.amui', function(event) {
            if (event.date.valueOf() > endDate.valueOf()) {
                alert('开始日期应小于结束日期！');
            } else {            
                startDate = new Date(event.date);                
                $('#my-startDate').val($('#my-start').data('date'));
            }
            $(this).datepicker('close');
        });

        $('#my-end').datepicker().
        on('changeDate.datepicker.amui', function(event) {
            if (event.date.valueOf() < startDate.valueOf()) {
                alert('结束日期应大于开始日期！');
            } else {            
                endDate = new Date(event.date);
                $('#my-endDate').val($('#my-end').data('date'));
            }
            $(this).datepicker('close');
        });    
        
    })

    function detail(id){
        window.location.href = "<?= url('exam/detail') ?>&id="+id;
    }




</script>

