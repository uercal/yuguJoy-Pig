<style>

.am-form-field{
    /* width:8rem; */
}
</style>
<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf" style="display:flex;position:relative;">
                    <div class="widget-title am-cf">设备使用记录</div>     
                     <!-- 搜索栏 -->
                     <form method="GET" action="" id="form">
                        <div class="am-form-group" style="position:absolute;right:20px;">
                            <div class="am-btn-toolbar">
                                <div class="am-btn-group am-btn-group-xs" style="display:flex;">
                                    <a class="am-btn am-btn-default am-radius "
                                    href="javascript:;">
                                        <span class="am-icon-phone"></span> 设备状态                                        
                                    </a>
                                    <select name="check_status"  id="check_status" style="font-size:12px;" data-am-selected="{btnWidth: '40%', btnSize: 'xs'}"
                                            >
                                        <option value="0">不限</option>
                                        <option value="10" <?php if (isset($map['check_status']) && $map['check_status'] == 10) : ?> selected <?php endif; ?>>在库</option>
                                        <option value="20" <?php if (isset($map['check_status']) && $map['check_status'] == 20) : ?> selected <?php endif; ?>>运送中</option>
                                        <option value="30" <?php if (isset($map['check_status']) && $map['check_status'] == 30) : ?> selected <?php endif; ?>>使用中</option>
                                        <option value="40" <?php if (isset($map['check_status']) && $map['check_status'] == 40) : ?> selected <?php endif; ?>>维修中</option>
                                        <option value="50" <?php if (isset($map['check_status']) && $map['check_status'] == 30) : ?> selected <?php endif; ?>>停用</option>

                                    </select>
                                </div>                                
                                <div class="am-btn-group am-btn-group-xs" style="display:flex;">
                                    <a class="am-btn am-btn-default am-radius"
                                    href="javascript:;">
                                        <span class="am-icon-home"></span> 设备ID                                        
                                    </a>
                                    <input type="text" class="am-form-field" style="padding: 3px 5px;" name="equip_id" id="equip_id" value="<?= isset($map['equip_id']) ? $map['equip_id'] : "" ?>">
                                </div>                                                                
                                <div class="am-btn-group am-btn-group-xs" style="display:flex;">                                    
                                    <a type="button" class="am-btn am-btn-default am-btn-primary am-margin-right" id="my-start">开始日期</a>
                                    <input type="text" class="am-form-field" style="padding: 3px 5px;" name="startDate" id="my-startDate" value="<?= isset($map['startDate']) ? $map['startDate'] : "" ?>">
                                    <a type="button" class="am-btn am-btn-default am-btn-primary am-margin-right" id="my-end">结束日期</a>
                                    <input type="text" class="am-form-field" style="padding: 3px 5px;" name="endDate" id="my-endDate" value="<?= isset($map['endDate']) ? $map['endDate'] : "" ?>">
                                </div>
                                <div class="am-btn-group am-btn-group-xs">
                                    <a class="am-btn am-btn-default am-btn-success am-radius" id="search"
                                    href="javascript:;">
                                        <span class="am-icon-search"></span> 搜索
                                    </a>
                                    <a class="am-btn am-btn-default am-btn-warning am-radius" id="detail"
                                    href="javascript:;">
                                        <span class="am-icon-search"></span> 查看该设备详情
                                    </a>
                                </div>
                            </div>                            
                        </div>
                    </form>                   
                </div>
                <div class="widget-body am-fr">                                         
                    <div class="am-scrollable-horizontal am-u-sm-12">
                        <table width="100%" class="am-table am-table-compact am-table-striped
                         tpl-table-black am-text-nowrap">
                            <thead>
                            <tr>
                                <th>订单号</th>
                                <th>设备ID</th>
                                <th>操作人</th>                                
                                <th>设备状态</th>
                                <th>操作时间</th>                                
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!$list->isEmpty()) : foreach ($list as $item) : ?>
                                <tr>
                                    <td class="am-text-middle"><?= $item['order'] ? $item['order']['order_no'] : '解绑' ?></td>                                    
                                    <td class="am-text-middle">
                                        <p class="item-title"><?= $item['equip_id'] ?></p>
                                    </td>
                                    <td class="am-text-middle">
                                        <p class="item-title"><?= $item['member'] ? $item['member']['name'] : '管理员' ?></p>
                                    </td>                                    
                                    <td class="am-text-middle"><?= $item['equip_status_text'] ?></td>
                                    <td class="am-text-middle"> <?= $item['create_time'] ?></td>                                                                        
                                    <td class="am-text-middle">
                                    
                                        <div class="tpl-table-black-operation">                                                  
                                            <a class="tpl-table-black-operation" href="<?= url(
                                                                                            'order/detail',
                                                                                            ['order_id' => $item['order_id']]
                                                                                        ) ?>" class="am-btn am-btn-xs am-radius">
                                                <i class="am-icon-book"></i> 查看归属订单
                                            </a>    
                                            <a class="tpl-table-black-operation-del" href="javascript:;" onclick="">
                                                <i class="am-icon-pencil"></i> 删除
                                            </a>                                                                               
                                        </div>
                                                                                                           
                                    </td>
                                </tr>
                            <?php endforeach;
                            else : ?>
                                <tr>
                                    <td colspan="6" class="am-text-center">暂无记录</td>
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
            var url = "<?php echo url('equip/usingLog') ?>";
            var param = $('#form').serialize();
            var html = url + '&' + param;
            window.location.href = html;            
        });
        //  
        $('#detail').on('click', function(e) {         
            var url = "<?php echo url('equip/usingDetail') ?>";
            var param = $('#form').serialize();
            var equip_id = $('#equip_id').val();
            var html = url + '&' + param;
            equip_id===''?layer.msg('请填写设备ID'):window.location.href = html;            
        });
    });
</script>
<script>
    $(function () {
        
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
    });
</script>

