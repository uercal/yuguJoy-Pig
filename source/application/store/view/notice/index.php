<style>
.tpl-table-black-operation a.tpl-table-black-operation-war {
  border: 1px solid orange;
  color: orange;
}

.tpl-table-black-operation a.tpl-table-black-operation-war:hover {
  background: orange;
  color: #fff;
}

td{
    white-space:nowrap;overflow:hidden;text-overflow: ellipsis;
}

</style>

<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf">
                    <div class="widget-title am-cf">通知列表</div>
                </div>
                <div class="widget-body am-fr">                    
                    <div class="am-scrollable-horizontal am-u-sm-12">
                        <div class="am-u-sm-12 am-u-md-6 am-u-lg-6">
                            <div class="am-form-group">
                                <div class="am-btn-toolbar">
                                    <div class="am-btn-group am-btn-group-xs">
                                        <a class="am-btn am-btn-default am-btn-success am-radius"
                                        href="<?= url('notice/add') ?>">
                                            <span class="am-icon-plus"></span> 新增
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table width="100%" class="am-table am-table-compact am-table-striped
                         tpl-table-black am-text-nowrap" style="table-layout: fixed;">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>标题</th>
                                <th>内容</th>
                                <th>发布时间</th>                                                                
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!$list->isEmpty()) : foreach ($list as $item) : ?>
                                <tr>
                                    <td class="am-text-middle"><?= $item['id'] ?></td>
                                    <td class="am-text-middle"><?= $item['title'] ?></td>
                                    <td class="am-text-middle"><?= $item['content'] ?></td>
                                    <td class="am-text-middle"><?= $item['create_time'] ?></td>                                                                        
                                    <td class="am-text-middle">
                                        <div class="tpl-table-black-operation">                                            
                                            <a  href="<?= url('notice/detail', ['id' => $item['id']]) ?>" class="tpl-table-black-operation-green">
                                                <i class="am-icon-book"></i> 查看
                                            </a>                                            
                                            <a href="javascript:;" class="item-delete tpl-table-black-operation-del"
                                               data-id="<?= $item['id'] ?>">
                                                <i class="am-icon-trash"></i> 删除
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
<script>
    $(function () {
        // 删除元素
        var url = "<?= url('notice/delete') ?>";
        $('.item-delete').delete('id', url);
    });
    function detail(id){        
        layer.prompt({title: '输入新密码', formType: 1}, function(text){      
            layer.closeAll();
            $.post('<?= url("member/resetPass") ?>',{
                id:id,
                password:text
            },function(res){
                layer.msg(res.msg);
                setTimeout(() => {
                    window.location.reload();
                }, 1100);
            });
        });
    }

    
    
</script>

