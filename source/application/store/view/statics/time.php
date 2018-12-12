<style>


</style>
<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf" style="display:flex;position:relative;">
                    <div class="widget-title am-cf">列表</div>     
                    <!-- 搜索栏 -->
                    <form method="GET" action="" id="form">
                        <div class="am-form-group" style="position:absolute;right:20px;">
                            <div class="am-btn-toolbar">                                
                                <div class="am-btn-group am-btn-group-xs" style="display:flex;">                                    
                                    <a type="button" class="am-btn am-btn-default am-btn-primary am-margin-right" id="my-start">开始日期</a>
                                    <input type="text" class="am-form-field" style="padding: 3px 5px;" name="startDate" id="my-startDate" value="<?= isset($map['startDate']) ? $map['startDate'] : "" ?>">
                                    <a type="button" class="am-btn am-btn-default am-btn-primary am-margin-right" id="my-end">结束日期</a>
                                    <input type="text" class="am-form-field" style="padding: 3px 5px;" name="endDate" id="my-endDate" value="<?= isset($map['endDate']) ? $map['endDate'] : "" ?>">
                                </div>
                                <div class="am-btn-group am-btn-group-xs">
                                    <a class="am-btn am-btn-default am-btn-success am-radius" id="search"
                                    href="javascript:;">
                                        <span class="am-icon-search"></span> 确定
                                    </a>
                                </div>
                            </div>                            
                        </div>
                    </form>
                    
                </div>                                                             

                <div class="am-panel-group" id="accordion">
                    <?php foreach ($state as $key => $status) : ?>
                    <div class="am-panel am-panel-default">
                        <div class="am-panel-hd">
                        <h4 class="am-panel-title" data-am-collapse="{parent: '#accordion', target: '#do-not-say-<?= $key ?>'}">
                            <?= $status['name'] ?>
                            <span style="font-size:12px;color:#777777;">
                                共计 <a href="JavaScript:;"><?= $status['count'] ?></a> 次
                            </span>
                        </h4>
                        </div>

                        <div id="do-not-say-<?= $key ?>" class="am-panel-collapse am-collapse am-in">
                            <div class="am-panel-bd">                                                                    
                                <table width="100%" class="am-table am-table-compact am-table-striped
                                tpl-table-black am-text-nowrap">
                                    <thead>
                                    <tr>
                                        <th>设备ID</th>
                                        <th>产品名称</th>
                                        <th>型号</th>
                                        <th>记录时间</th>                                    
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($status['data'] as $data) : ?>
                                        <tr>
                                            <td><?= $data['equip']['equip_id'] ?></td>
                                            <td><?= $data['equip']['goods_name'] ?></td>
                                            <td><?= $data['equip']['spec_value']['spec_value'] ?></td>
                                            <td><?= $data['create_time'] ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>                                
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>     
                    
                </div>
            </div>
        </div>
    </div>
    
</div>
<script>
    $(function () {
        var $modal = $('#your-modal');                
        $('#search').on('click', function(e) {
            var url = "<?php echo url('statics/time') ?>";
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

