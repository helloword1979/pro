<?php
/* Smarty version 3.1.31, created on 2018-02-10 11:00:45
  from "/Users/frank/www/newproject/trunk/php-yii/backend/views/agent/index.html" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5a7ed0ddde4923_42830010',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1290975c1ddd0398839ad610b31e2297d82eaf78' => 
    array (
      0 => '/Users/frank/www/newproject/trunk/php-yii/backend/views/agent/index.html',
      1 => 1517801681,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a7ed0ddde4923_42830010 (Smarty_Internal_Template $_smarty_tpl) {
?>
<style>
    #agent-form label{margin-right:10px}
    .handle a{cursor: pointer}
    .combo-select{border: none;width: 200px;top:7px}
    .combo-dropdown{z-index:100}
    .width100{width:100px}
</style>

<div class="page-header col-xs-12">
    <div id="show-table_filter" class="dataTables_length">
        <form id="agent-form" method='get' action='/agent/index'>

            <label for="login_user">账号:
                <input class='width100' type="text" name="login_user" id='login_user' value="<?php if (isset($_GET['login_user'])) {
echo $_GET['login_user'];
}?>">
            </label>
            <label for="login_name">昵称:
                <input class='width100' type="text" name='login_name'  id="login_name" value="<?php if (isset($_GET['login_name'])) {
echo $_GET['login_name'];
}?>">
            </label>
            <label for="menuLevel"> 状态:
                <select type="select" name="status"  id="status">
                    <option value="" >全部</option>
                    <option value="1" <?php if (isset($_GET['status']) && $_GET['status'] == 1) {?>selected<?php }?>>有效</option>
                    <option value="2" <?php if (isset($_GET['status']) && $_GET['status'] == 2) {?>selected<?php }?>>无效</option>
                </select>
            </label>

            <label > 每页显示条数:
                <select type="select" name="pageNum"  id="pageNum" >
                    <option value="100"  <?php if (isset($_GET['pageNum']) && $_GET['pageNum'] == 100) {?>selected<?php }?>>100</option>
                    <option value="500" <?php if (isset($_GET['pageNum']) && $_GET['pageNum'] == 500) {?>selected<?php }?>>500</option>
                    <option value="1000" <?php if (isset($_GET['pageNum']) && $_GET['pageNum'] == 1000) {?>selected<?php }?>>1000</option>
                </select>
            </label>
            <label > 页码:
                <select type="select" name="page"  id="page">
                    <?php
$__section_loop_0_saved = isset($_smarty_tpl->tpl_vars['__smarty_section_loop']) ? $_smarty_tpl->tpl_vars['__smarty_section_loop'] : false;
$__section_loop_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['pagecount']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_loop_0_total = $__section_loop_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_loop'] = new Smarty_Variable(array());
if ($__section_loop_0_total != 0) {
for ($__section_loop_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_loop']->value['index'] = 0; $__section_loop_0_iteration <= $__section_loop_0_total; $__section_loop_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_loop']->value['index']++){
?>
                    <option value="<?php echo (isset($_smarty_tpl->tpl_vars['__smarty_section_loop']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_loop']->value['index'] : null)+1;?>
"
                            <?php if (isset($_GET['page']) && $_GET['page'] == (isset($_smarty_tpl->tpl_vars['__smarty_section_loop']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_loop']->value['index'] : null)+1) {?>selected<?php }?>>
                            <?php echo (isset($_smarty_tpl->tpl_vars['__smarty_section_loop']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_loop']->value['index'] : null)+1;?>

                </option>
                <?php
}
}
if ($__section_loop_0_saved) {
$_smarty_tpl->tpl_vars['__smarty_section_loop'] = $__section_loop_0_saved;
}
?>
            </select>
        </label>
        <label>
            <input type="button" id='search' class="btn btn-sm btn-success" value="搜索"/>
        </label>
        <?php if (isset($_smarty_tpl->tpl_vars['user_type']->value) && in_array($_smarty_tpl->tpl_vars['user_type']->value,array(1,6,7))) {?>
        <label><span class="btn btn-sm btn-success" id="create">添加</span></label>
        <?php }?>
    </form>
</div>
</div>
<div class="row" >
    <div class="col-xs-12">
        <div class="table-responsive">
            <table id="sample-table-1" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="center">ID</th>
                        <th class="center">线路</th>
                        <th class="center">账号</th>
                        <th class="center">昵称</th>
                        <th class="center">额度</th>
                        <th class="center">状态</th>
                        <th class="center">添加时间</th>
        <?php if (isset($_smarty_tpl->tpl_vars['user_type']->value) && in_array($_smarty_tpl->tpl_vars['user_type']->value,array(1,6,7))) {?>
                        <th class="center">操作</th>
            <?php }?>
                    </tr>
                </thead>
                <tbody>
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value, 'v', false, 'k');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['k']->value => $_smarty_tpl->tpl_vars['v']->value) {
?>
                    <tr>
                        <td class="center">
                            <a ><?php if (isset($_smarty_tpl->tpl_vars['v']->value['id'])) {
echo $_smarty_tpl->tpl_vars['v']->value['id'];
}?></a>
                        </td>
                        <td class="center">
                            <a ><?php if (isset($_smarty_tpl->tpl_vars['v']->value['line_id'])) {
echo $_smarty_tpl->tpl_vars['v']->value['line_id'];
}?></a>
                        </td>
                        <td class="center">
                            <a><?php if (isset($_smarty_tpl->tpl_vars['v']->value['login_user'])) {
echo $_smarty_tpl->tpl_vars['v']->value['login_user'];
}?></a>
                        </td>
                        <td class="center"><?php if (isset($_smarty_tpl->tpl_vars['v']->value['login_name'])) {
echo $_smarty_tpl->tpl_vars['v']->value['login_name'];
}?></td>
                        <td class="center green"><?php if (isset($_smarty_tpl->tpl_vars['v']->value['money'])) {
echo $_smarty_tpl->tpl_vars['v']->value['money'];
}?></td>
                        <td class="center"><?php if (isset($_smarty_tpl->tpl_vars['v']->value['deleteTxt'])) {
echo $_smarty_tpl->tpl_vars['v']->value['deleteTxt'];
}?></td>
                        <td class="center"><?php if (isset($_smarty_tpl->tpl_vars['v']->value['addDate'])) {
echo $_smarty_tpl->tpl_vars['v']->value['addDate'];
}?></td>

        <?php if (isset($_smarty_tpl->tpl_vars['user_type']->value) && in_array($_smarty_tpl->tpl_vars['user_type']->value,array(1,6,7))) {?>
                        <td class="center handle">
                            <span class="btn btn-xs btn-success detail" data="<?php echo $_smarty_tpl->tpl_vars['v']->value['id'];?>
" type="user_agent">
                                详情
                            </span>
                            <span class="btn btn-xs btn-success update" data="<?php echo $_smarty_tpl->tpl_vars['v']->value['id'];?>
">
                                修改
                            </span>
                            <!-- <span class="btn btn-xs btn-success role" data="<?php echo $_smarty_tpl->tpl_vars['v']->value['id'];?>
" type="user_agent">
                                分配角色
                            </span> -->
                            <span class="btn btn-xs btn-success update_pwd" data="<?php echo $_smarty_tpl->tpl_vars['v']->value['id'];?>
"  type="user_agent">
                                密码修改
                            </span>
                           
                             <span class="btn btn-xs btn-success set_money" data="<?php echo $_smarty_tpl->tpl_vars['v']->value['id'];?>
" >
                                额度分配
                            </span>
                        
                        </td>
            <?php }?>
                    </tr>
                    <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="hr hr-18 dotted hr-double"></div>
<?php echo '<script'; ?>
>
    //pjax局部加载列表
    function renderTableByPjax() {
        var login_user = $.trim($('#login_user').val());
        var login_name = $.trim($('#login_name').val());
        var status = $.trim($('#status').val());
        var pageNum = $.trim($('#pageNum').val());
        var page = $.trim($('#page').val());
        var params = {
            status: status,
            login_user: login_user,
            login_name: login_name,
            pageNum: pageNum,
            page: page
        };
        $.pjax({
            data: params,
            method: 'get',
            url: '/agent/index',
            container: '#container'
        });
    }

    //pjax局部加载表单
    function renderFormByPjax(url) {
        $.pjax({
            method: 'get',
            url: url,
            container: '#container'
        });
    }

    //搜索
    $('#search').click(function () {
        renderTableByPjax();
    })

    //每页显示条数切换
    $('#pageNum').change(function () {
        renderTableByPjax();
    })

    //状态
     $('#status').change(function () {
        renderTableByPjax();
     })

    //新增
    $('#create').click(function () {
        var url = '/agent/edit?type=create';
        renderFormByPjax(url);
    })

    //详情
    $('.detail').click(function () {
        var id = $(this).attr('data');
        var type = $(this).attr('type');
        var url = '/agent/detail?id=' + id + '&type=' + type;
        renderFormByPjax(url);
    })

    //修改
    $('.update').click(function () {
        var id = $(this).attr('data');
        var url = '/agent/edit?id=' + id + '&type=update';
        renderFormByPjax(url);
    })

    //角色
    $('.role').click(function () {
        var id = $(this).attr('data');
        $.ajax({
            type: "post",
            url: '/agent/role',
            data: {id: id},
            dataType: 'json',
            success: function (res) {
                if (res.code == 400) {
                    layer.alert(res.msg, {icon: 2});
                } else if(res.ErrorCode == 2){
                    layer.close(load);
                    layer.alert(res.ErrorMsg, {icon: 2});
                } else if (res.code == 200) {
                    layer.alert(res.msg, {icon: 1});
                }
            }
        });
    })

    //密码修改
    $('.update_pwd').click(function () {
        var id = $(this).attr('data');
        var type = $(this).attr('type');
        var url = '/agent/password?id=' + id + '&type=' + type;
        renderFormByPjax(url);
    })

    //额度分配
    $('.set_money').click(function () {
        var id = $(this).attr('data');
        var url = '/agent/money?id=' + id + '&type=user_agent';
        renderFormByPjax(url);
    })


<?php echo '</script'; ?>
>
<?php }
}
