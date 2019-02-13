<style>


</style>
<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf" style="display:flex;position:relative;">
                    <div class="widget-title am-cf">用户列表</div>     
                    <!-- 搜索栏 -->
                    <form method="GET" action="" id="form">
                        <div class="am-form-group" style="position:absolute;right:20px;">
                            <div class="am-btn-toolbar">
                                <div class="am-btn-group am-btn-group-xs" style="display:flex;">
                                    <a class="am-btn am-btn-default am-radius"
                                    href="javascript:;">
                                        <span class="am-icon-home"></span> 公司名称                                        
                                    </a>
                                    <input type="text" class="am-form-field" name="company" style="padding: 3px 5px;" placeholder="公司名称" value="<?= isset($map['company'])?$map['company']:"" ?>">
                                </div>
                                <div class="am-btn-group am-btn-group-xs" style="display:flex;">
                                    <a class="am-btn am-btn-default am-radius"
                                    href="javascript:;">
                                        <span class="am-icon-phone"></span> 手机号码                                        
                                    </a>
                                    <input type="text" class="am-form-field" name="phone" style="padding: 3px 5px;" placeholder="手机号码" value="<?= isset($map['phone'])?$map['phone']:"" ?>">
                                </div>

                                <div class="am-btn-group am-btn-group-xs" style="display:flex;">                                    
                                    <a type="button" class="am-btn am-btn-default am-btn-primary am-margin-right" id="my-start">开始日期</a>
                                    <input type="text" class="am-form-field" style="padding: 3px 5px;" name="startDate" id="my-startDate" value="<?= isset($map['startDate'])?$map['startDate']:"" ?>">
                                    <a type="button" class="am-btn am-btn-default am-btn-primary am-margin-right" id="my-end">结束日期</a>
                                    <input type="text" class="am-form-field" style="padding: 3px 5px;" name="endDate" id="my-endDate" value="<?= isset($map['endDate'])?$map['endDate']:"" ?>">
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
                                <th>用户ID</th>
                                <th>微信头像</th>
                                <th>微信昵称</th>
                                <th>真实姓名</th>
                                <th>性别</th>
                                <th>公司名称</th>
                                <th>手机号码</th>
                                <th>城市</th>
                                <th>注册时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!$list->isEmpty()) : foreach ($list as $item) : ?>
                                <tr>
                                    <td class="am-text-middle"><?= $item['user_id'] ?></td>
                                    <td class="am-text-middle">
                                        <a href="<?= $item['avatarUrl'] ?>" title="点击查看大图" target="_blank">
                                            <img src="<?= $item['avatarUrl'] ?>" width="72" height="72" alt="">
                                        </a>
                                    </td>
                                    <td class="am-text-middle"><?= $item['nickName'] ?></td>
                                    <td class="am-text-middle"><?= $item['user_name'] ?></td>
                                    <td class="am-text-middle"><?= $item['gender'] ?></td>
                                    <td class="am-text-middle"><?= $item['company'] ? : '--' ?></td>
                                    <td class="am-text-middle"><?= $item['phone'] ? : '--' ?></td>
                                    <td class="am-text-middle"><?= $item['city'] ? : '--' ?></td>
                                    <td class="am-text-middle"><?= $item['create_time'] ?></td>
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
            var url = "<?php echo url('user/index') ?>";
            var param = $('#form').serialize();
            var html = url + '&' + param;
            window.location.href = html;            
        });    
    });
</script>
<script>
    $(function(){
        var b = "<?= isset($map['startDate'])?$map['startDate']:"" ?>";
        var e = "<?= isset($map['endDate'])?$map['endDate']:"" ?>";
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

