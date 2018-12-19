<style>

.tpl-table-black-operation a.tpl-table-black-operation-war {
  border: 1px solid orange;
  color: orange;
}

.tpl-table-black-operation a.tpl-table-black-operation-war:hover {
  background: orange;
  color: #fff;
}

.status-bg-10{
    color:#5eb95e;
    background-color:rgba(94,185,94,.115) !important;
}

.status-bg-20{
    color:#0b76ac;
    background-color:rgba(14,144,210,.115) !important;
}

.status-bg-30{
    color:#dd514c;
    background-color:rgba(221,81,76,.115) !important;
}

.status-bg-40{
    color:#F37B1D;
    background-color:rgba(243,123,29,.115) !important;
}

.status-bg-50{
    color:#FFBF98;    
    background-color:rgba(255,191,152,.115) !important;
}

</style>
<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf" style="display:flex;">
                    <div class="widget-title am-cf">售后订单</div>                                             
                </div>
                <div class="widget-body am-fr" style="display:flex;flex-direction:column;">                              
                    <!-- 搜索栏 -->
                    <form method="GET" action="" id="form">
                        <div class="am-form-group" style="display:flex;padding-left:20px;">
                            <div class="am-btn-toolbar">  
                                <div class="am-btn-group am-btn-group-xs">
                                    <a class="am-btn am-btn-default am-radius "
                                    href="javascript:;">
                                        <span class="am-icon-phone"></span> 类型                                        
                                    </a>
                                    <select name="type"  id="type" style="font-size:12px;" data-am-selected="{btnSize: 'sm',  placeholder:'请选择类型'}"
                                            >
                                        <option value="0">不限</option>                                       
                                        <option value="10" <?php if (isset($map['type']) && $map['type'] == 10) : ?> selected <?php endif; ?>>售后维修</option>
                                        <option value="20" <?php if (isset($map['type']) && $map['type'] == 20) : ?> selected <?php endif; ?>>其他</option>                                                                                                                      
                                    </select>
                                </div>     
                                <div class="am-btn-group am-btn-group-xs">
                                    <a class="am-btn am-btn-default am-radius "
                                    href="javascript:;">
                                        <span class="am-icon-phone"></span> 状态                                        
                                    </a>
                                    <select name="status"  id="status" style="font-size:12px;" data-am-selected="{btnSize: 'sm',  placeholder:'请选择状态'}"
                                            >
                                        <option value="0">不限</option>                                       
                                        <option value="10" <?php if (isset($map['status']) && $map['status'] == 10) : ?> selected <?php endif; ?>>已发起</option>                                        
                                        <option value="30" <?php if (isset($map['status']) && $map['status'] == 20) : ?> selected <?php endif; ?>>进行中</option>
                                        <option value="40" <?php if (isset($map['status']) && $map['status'] == 30) : ?> selected <?php endif; ?>>返修中</option>
                                        <option value="50" <?php if (isset($map['status']) && $map['status'] == 40) : ?> selected <?php endif; ?>>已完成</option>                                        
                                    </select>
                                </div>                                                            
                                <div class="am-btn-group am-btn-group-xs" style="display:flex;">
                                    <a class="am-btn am-btn-default am-btn-primary am-radius"
                                    href="javascript:;">
                                        <span class="am-icon-home"></span> 主订单号                                        
                                    </a>
                                    <input type="text" class="am-form-field" name="order_no" style="padding: 3px 5px;" placeholder="订单号" value="<?= isset($map['order_no']) ? $map['order_no'] : "" ?>">
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
                                </div>
                            </div>                            
                        </div>
                    </form>
                    <!--  -->
                    <div class="widget-body am-fr">                    
                        <div class="am-scrollable-horizontal am-u-sm-12">                        
                            <!--  -->
                            <table width="100%" class="am-table am-table-compact am-table-striped
                            tpl-table-black am-text-nowrap">
                                <thead>
                                <tr>
                                    <th width="20%" class="goods-detail">售后单号</th>
                                    <th width="10%">售后类型</th>
                                    <th width="15%">用户头像</th>
                                    <th width="15%">用户昵称</th>
                                    <th>主订单号</th>
                                    <th>状态</th>                                
                                    <th>发起时间</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!$list->isEmpty()) : foreach ($list as $after) : ?>
                                    <tr>
                                        <td class="am-text-middle"><?= $after['after_no'] ?></td>
                                        <td class="am-text-middle"><?= $after['type'] ? $after['type_text'] : '/' ?></td>
                                        <td class="am-text-middle">                                            
                                            <a href="<?= $after['user']['avatarUrl'] ?>" title="点击查看大图" target="_blank">
                                                <img src="<?= $after['user']['avatarUrl'] ?>" width="40" height="40" alt="">
                                            </a>                                            
                                        </td>
                                        <td class="am-text-middle"><?= $after['user']['nickName'] ?></td>
                                        <td class="am-text-middle"><?= $after['order']['order_no'] ?></td>
                                        <td class="am-text-middle status-bg-<?= $after['status'] ?>"><?= $after['status_text'] ?></td>                                    
                                        <td class="am-text-middle"><?= $after['create_time'] ?></td>
                                        <td class="am-text-middle">
                                            <div class="tpl-table-black-operation">                                            
                                                <a  href="<?= url('order/after_detail', ['id' => $after['id']]) ?>" class="tpl-table-black-operation-green">
                                                    <i class="am-icon-book"></i> 查看
                                                </a>                                                
                                            </div>
                                        </td>
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
    $(function () {                    
        $('#search').on('click', function(e) {
            var url = "<?php echo url() ?>";
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

</script>

