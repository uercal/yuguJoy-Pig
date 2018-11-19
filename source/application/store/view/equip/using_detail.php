<link rel="stylesheet" href="assets/layui/css/layui.css">
<style>
.equip-detail{
    font-size:12px;color:#777;
}
</style>
<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf" style="display:flex;position:relative;">
                    <div class="widget-title am-cf">设备使用详情</div>     
                     <!-- 搜索栏 -->
                     <form method="GET" action="" id="form">
                        <div class="am-form-group" style="position:absolute;right:20px;">
                            <div class="am-btn-toolbar">                                                              
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
                       
                        <ul class="layui-timeline">
                            <?php foreach ($data as $item) : ?>
                            <li class="layui-timeline-item">
                                <i class="layui-icon layui-timeline-axis">&#xe63f;</i>
                                <div class="layui-timeline-content layui-text">
                                <h3 class="layui-timeline-title"><?= $item['equip_status_text'] ?>
                                <span style="font-size:13px;color:#777;"><?= date('Y年m月d日 h:i:s', strtotime($item['create_time'])) ?></span>
                                </h3>                                
                                <p class="equip-detail">
                                    设备ID：<a href="JavaScript:;"><?= $item['equip_id'] ?></a>
                                </p>
                                <p class="equip-detail">
                                    设备所属产品：<a href="JavaScript:;"><?= $item['equip']['goods']['goods_name'] ?> <?= $item['equip']['spec_value']['spec_value'] ?></a>
                                </p>
                                <?php if ($item['order']) : ?>
                                <p class="equip-detail">
                                    订单号：<a href="JavaScript:;"><?= $item['order']['order_no'] ?></a>
                                </p>                                                                  
                                <?php endif; ?>
                                <p class="equip-detail">
                                    操作人：<a href="JavaScript:;"><?= $item['member'] ? $item['member']['name'] : '管理员' ?></a>
                                </p>
                                <p class="equip-detail">
                                    变更状态：
                                    <?php switch ($item['equip_status']):
                                        case 10:
                                            echo ("<a href='JavaScript:; style='color:#0c79b1;'>");
                                            break;                                    
                                        case 20:
                                            echo ("<a href='JavaScript:;' style='color:#E0690C;'>");
                                            break;
                                        case 30:
                                            echo ("<a href='JavaScript:;' style='color:#B848FF;'>");
                                            break;
                                        case 40:
                                            echo ("<a href='JavaScript:;' style='color:#4AAA4A;'>");
                                            break;
                                        case 50:
                                            echo ("<a href='JavaScript:;' style='color:red;'>");
                                            break;                                                                           
                                    endswitch;
                                    ?>               
                                    <?= $item['equip_status_text'] ?></a>
                                </p>
                                </div>
                            </li>                          
                            <?php endforeach; ?>  
                        </ul>


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

