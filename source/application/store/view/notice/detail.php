<style>

.layui-form{
    margin:0 auto !important;
         
}

.layui-table-body{
    overflow-x:hidden !important;
}

.am-form textarea[disabled]{
    background-color:#fff;
}

</style>
<link rel="stylesheet" href="assets/layui/css/layui.css"  media="all">
                
<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">基本信息</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label">标题 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="notice[title]"
                                           value="<?= $notice['title'] ?>" disabled>
                                </div>
                            </div>

                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">内容 </label>
                                <div class="am-u-sm-9 am-u-end">                                    
                                    <textarea name="notice[content]" id="" cols="30" rows="10" disabled><?= $notice['content'] ?></textarea>
                                </div>
                            </div>                            
                                                        
                        </fieldset>
                    </div>
                </form>
                <div class="widget-body  am-margin-bottom-lg">
                    <div class="widget-head am-cf">
                        <div class="widget-title am-fl">通知人员</div>
                    </div>                                                                                                                
                    <div class="am-scrollable-horizontal">
                        <table class="regional-table am-table am-table-bordered am-table-centered
                            am-text-nowrap am-margin-bottom-xs">
                            <tbody>
                            <tr>
                                <th>ID</th>
                                <th>姓名</th>
                                <th>电话</th>
                                <th>角色</th>
                            </tr>
                            <?php foreach ($notice['member'] as $member) : ?>
                            <tr>
                                <td><?= $member['id'] ?></td>
                                <td><?= $member['name'] ?></td>
                                <td><?= $member['phone'] ?></td>
                                <td><?= $member['role']['role_name'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
               
            </div>            
        </div>
    </div>
</div>
