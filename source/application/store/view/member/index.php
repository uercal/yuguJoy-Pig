<style>
.tpl-table-black-operation a.tpl-table-black-operation-war {
  border: 1px solid orange;
  color: orange;
}

.tpl-table-black-operation a.tpl-table-black-operation-war:hover {
  background: orange;
  color: #fff;
}
</style>

<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf">
                    <div class="widget-title am-cf">员工列表</div>
                </div>
                <div class="widget-body am-fr">                    
                    <div class="am-scrollable-horizontal am-u-sm-12">
                        <div class="am-u-sm-12 am-u-md-6 am-u-lg-6">
                            <div class="am-form-group">
                                <div class="am-btn-toolbar">
                                    <div class="am-btn-group am-btn-group-xs">
                                        <a class="am-btn am-btn-default am-btn-success am-radius"
                                        href="<?= url('member/add') ?>">
                                            <span class="am-icon-plus"></span> 新增
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table width="100%" class="am-table am-table-compact am-table-striped
                         tpl-table-black am-text-nowrap">
                            <thead>
                            <tr>
                                <th>员工ID</th>
                                <th>员工姓名</th>
                                <th>角色</th>
                                <th>手机号码</th>                                
                                <th>职称</th>                                
                                <th>状态<th>                                
                                <th>注册时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!$list->isEmpty()) : foreach ($list as $item) : ?>
                                <tr>
                                    <td class="am-text-middle"><?= $item['id'] ?></td>
                                    <td class="am-text-middle"><?= $item['name'] ?></td>
                                    <td class="am-text-middle"><?= $item['role']['role_name'] ?></td>
                                    <td class="am-text-middle"><?= $item['phone'] ?></td>                                    
                                    <td class="am-text-middle"><?= $item['function'] ?></td>                                    
                                    <td class="am-text-middle"><?= $item['status'] == 40 ? '休息' : getMemeberStatus($item['order_log'])['msg'] ?></td>                                    
                                    <td class="am-text-middle"><?= $item['create_time'] ?></td>
                                    <td class="am-text-middle">
                                        <div class="tpl-table-black-operation">
                                            <a href="<?= url(
                                                        'member/edit',
                                                        ['id' => $item['id']]
                                                    ) ?>">
                                                <i class="am-icon-pencil"></i> 编辑
                                            </a>
                                            <a  onclick="reset(<?= $item['id'] ?>)" class="tpl-table-black-operation-green">
                                                <i class="am-icon-book"></i> 修改密码
                                            </a>
                                            <?php if (getMemeberStatus($item['order_log'])['code'] == 0) : ?>
                                            <a href="javascript:;"
                                               data-id="<?= $item['id'] ?>" onclick="exchange(<?= $item['id'] ?>)">
                                                <i class="am-icon-edit"></i> 状态切换
                                            </a>
                                            <?php endif; ?>
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

    });
    function reset(id){        
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

    function exchange(id){
        layer.confirm('是否状态切换（空闲/休息）？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            $.post('<?= url("member/exchangeStatus") ?>',{
                id:id                
            },function(res){
                layer.msg(res.msg);
                setTimeout(() => {
                    window.location.reload();
                }, 1100);
            });
        }, function(){
            
        });
    }
</script>

