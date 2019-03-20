<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf">
                    <div class="widget-title am-cf">审核列表</div>
                </div>
                <div class="widget-body am-fr" style="display: flex;justify-content: flex-end;flex-direction: row;flex-wrap: wrap;">
                    <!-- 搜索栏 -->
                    <form method="GET" action="" id="form">
                        <div class="am-form-group">
                            <div class="am-btn-toolbar">
                                <div class="am-btn-group am-btn-group-xs" style="display:flex;">
                                    <a class="am-btn am-btn-default am-radius " href="javascript:;">
                                        <span class="am-icon-pencil"></span> 审核类型
                                    </a>
                                    <select name="type" class="am-field-valid" id="type" style="font-size:12px;" data-am-selected="{btnSize: 'sm',  placeholder:'请选择审核类型'}">
                                        <option value="10" <?php if (isset($map['type']) && $map['type'] == 10) : ?> selected <?php endif; ?>>用户认证</option>
                                        <option value="20" <?php if (isset($map['type']) && $map['type'] == 20) : ?> selected <?php endif; ?>>员工派送</option>
                                        <option value="30" <?php if (isset($map['type']) && $map['type'] == 30) : ?> selected <?php endif; ?>>线下提现</option>
                                    </select>
                                </div>
                                <div class="am-btn-group am-btn-group-xs" style="display:flex;">
                                    <a class="am-btn am-btn-default am-radius " href="javascript:;">
                                        <span class="am-icon-pencil"></span> 状态
                                    </a>
                                    <select name="status" id="status" style="font-size:12px;" class="am-field-valid" data-am-selected="{btnSize: 'sm', placeholder:'请选择状态'}">
                                        <!-- <option value=""></option>                                                               -->
                                        <option value="0" <?php if (!isset($map['status'])) : ?> selected <?php endif; ?>>所有</option>
                                        <option value="10" <?php if (isset($map['status']) && $map['status'] == 10) : ?> selected <?php endif; ?>>审核中</option>
                                        <option value="20" <?php if (isset($map['status']) && $map['status'] == 20) : ?> selected <?php endif; ?>>已通过</option>
                                        <option value="30" <?php if (isset($map['status']) && $map['status'] == 30) : ?> selected <?php endif; ?>>已驳回</option>
                                    </select>
                                </div>
                                <div class="am-btn-group am-btn-group-xs" style="display:flex;">
                                    <a class="am-btn am-btn-default am-radius" href="javascript:;">
                                        <span class="am-icon-home"></span> 用户id
                                    </a>
                                    <input type="text" class="am-form-field" name="user_id" style="padding: 3px 5px;" placeholder="用户id" value="<?= isset($map['user_id']) ? $map['user_id'] : "" ?>">
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
                <div class="widget-body am-fr">
                    <div class="am-scrollable-horizontal am-u-sm-12">
                        <!--  -->
                        <table width="100%" class="am-table am-table-compact am-table-striped
                         tpl-table-black am-text-nowrap">
                            <thead>
                                <tr>
                                    <th><?= $map['type'] == 20 ? '员工ID' : '用户ID' ?></th>
                                    <th><?= $map['type'] == 20 ? '员工姓名' : '微信头像' ?></th>
                                    <th><?= $map['type'] == 20 ? '订单号' : '微信昵称' ?></th>
                                    <th>手机号码</th>
                                    <th>审核类型</th>
                                    <th>提交文件</th>
                                    <th>状态</th>
                                    <th>更新时间</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!$list->isEmpty()) : foreach ($list as $item) : ?>
                                <tr>
                                    <td class="am-text-middle"><?= $map['type'] == 20 ? $item['member']['id'] : $item['user']['user_id'] ?></td>
                                    <td class="am-text-middle">
                                        <?= $item['member']['name'] ?>
                                        <a href="<?= $item['user']['avatarUrl'] ?>" title="点击查看大图" target="_blank">
                                            <img src="<?= $item['user']['avatarUrl'] ?>" width="40" height="40" alt="">
                                        </a>
                                    </td>
                                    <td class="am-text-middle"><?= $map['type'] == 20 ? $item['order']['order_no'] : $item['user']['nickName'] ?></td>
                                    <td class="am-text-middle"><?= $map['type'] == 20 ? $item['member']['phone'] : $item['user']['phone'] ?: '--' ?></td>
                                    <td class="am-text-middle"><?= $item['type_text'] ?: '--' ?></td>
                                    <td class="am-text-middle">
                                        <button class="am-btn am-btn-sm am-btn-secondary" onclick="detail(<?= $item['id'] ?>)">查看并审核</button>
                                    </td>
                                    <td class="am-text-middle
                                    <?php if ($item['status'] == 10) : ?>am-warning 
                                    <?php elseif ($item['status'] == 20) : ?>am-success 
                                    <?php else : ?>am-danger 
                                    <?php endif; ?> 
                                    "><?= $item['status_text'] ?></td>
                                    <td class="am-text-middle"><?= $item['update_time'] ?></td>
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
    $(function() {
        var $modal = $('#your-modal');
        $('#status').on('change', function() {
            var url = "<?php echo url('exam/index') ?>";
            var param = $('#form').serialize();
            var html = url + '&' + param;
            window.location.href = html;
        });
        $('#type').on('change', function() {
            var url = "<?php echo url('exam/index') ?>";
            var param = $('#form').serialize();
            var html = url + '&' + param;
            window.location.href = html;
        });
    });
</script>
<script>
    // $(function(){
    //     var b = "<?= isset($map['startDate']) ? $map['startDate'] : "" ?>";
    //     var e = "<?= isset($map['endDate']) ? $map['endDate'] : "" ?>";
    //     var startDate = new Date(b);
    //     var endDate = new Date(e);                             
    //     // 
    //     $('#my-start').datepicker().on('changeDate.datepicker.amui', function(event) {
    //         if (event.date.valueOf() > endDate.valueOf()) {
    //             alert('开始日期应小于结束日期！');
    //         } else {            
    //             startDate = new Date(event.date);                
    //             $('#my-startDate').val($('#my-start').data('date'));
    //         }
    //         $(this).datepicker('close');
    //     });

    //     $('#my-end').datepicker().
    //     on('changeDate.datepicker.amui', function(event) {
    //         if (event.date.valueOf() < startDate.valueOf()) {
    //             alert('结束日期应大于开始日期！');
    //         } else {            
    //             endDate = new Date(event.date);
    //             $('#my-endDate').val($('#my-end').data('date'));
    //         }
    //         $(this).datepicker('close');
    //     });    

    // })

    function detail(id) {
        window.location.href = "<?= url('exam/detail') ?>&id=" + id;
    }
</script> 