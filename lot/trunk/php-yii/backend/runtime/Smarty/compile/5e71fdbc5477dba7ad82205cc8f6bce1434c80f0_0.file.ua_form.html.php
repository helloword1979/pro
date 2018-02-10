<?php
/* Smarty version 3.1.31, created on 2018-02-10 11:00:22
  from "/Users/frank/www/newproject/trunk/php-yii/backend/views/agent/ua_form.html" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5a7ed0c69b81f2_19452878',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5e71fdbc5477dba7ad82205cc8f6bce1434c80f0' => 
    array (
      0 => '/Users/frank/www/newproject/trunk/php-yii/backend/views/agent/ua_form.html',
      1 => 1517801681,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a7ed0c69b81f2_19452878 (Smarty_Internal_Template $_smarty_tpl) {
?>
<style>
    .select{width:20%;float:left;margin-right:10px}
    #sh-select{display: none}
    #money{width:20%}
</style>

<div class="page-header">
    <h1>
         <a href="javascript:;" id="agent_index">总代列表</a>
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            <?php if ($_GET['type'] == 'create') {?>新增总代<?php }?>
            <?php if ($_GET['type'] == 'update') {?>编辑总代<?php }?>
        </small>
    </h1>
</div>
<div class="row">
    <div class="col-xs-12">
        <form class="form-horizontal" role="form"  id="agent-form">
            <input type="hidden" value="<?php if (isset($_smarty_tpl->tpl_vars['data']->value['id'])) {
echo $_smarty_tpl->tpl_vars['data']->value['id'];
}?>" id='agent_id'>
            <?php if (!empty($_smarty_tpl->tpl_vars['sh_data']->value)) {?>
            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1">上级股东</label>
                <div class="col-sm-9">
                 <select type="select" name="sh_id"  id="sh_id"  <?php if ($_GET['type'] == 'update') {?>  disabled="disabled" <?php }?> >
                    <option value="" >全部</option>
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['sh_data']->value, 'v', false, 'k');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['k']->value => $_smarty_tpl->tpl_vars['v']->value) {
?>
                    <option value="<?php echo $_smarty_tpl->tpl_vars['v']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['v']->value['login_name'];?>
</option>
                    <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

                </select>

                <select id="sh-select">

                </select>

                </div>
            </div>
            <?php } elseif ($_GET['type'] == 'create') {?>
            <?php echo '<script'; ?>
 type="text/javascript">
                layer.alert('现在还没有股东，请先添加股东！',{icon:2},function(){
                    window.location.href = '/agent/shindex';
                })
            <?php echo '</script'; ?>
>
            <?php }?>
          

        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right" for="form-field-1-1">账号</label>
            <div class="col-sm-9">
                <input type="text" id='login_user' value="<?php if (isset($_smarty_tpl->tpl_vars['data']->value['login_user'])) {
echo $_smarty_tpl->tpl_vars['data']->value['login_user'];
}?>" class="col-sm-5" <?php if ($_GET['type'] != 'create') {?>disabled<?php }?>>
            </div>
        </div>
        <div class="space-4"></div>
        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right" for="form-field-2">昵称</label>
            <div class="col-sm-9">
                <input type="text" id='login_name' value="<?php if (isset($_smarty_tpl->tpl_vars['data']->value['login_name'])) {
echo $_smarty_tpl->tpl_vars['data']->value['login_name'];
}?>" class="col-xs-10 col-sm-5">
            </div>
        </div>
        <?php if ($_GET['type'] == 'create') {?>
        <div class="space-4"></div>
        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right" for="form-field-1-1">密码</label>
            <div class="col-sm-9">
                <input type="password" id='pwd' value="" class="col-sm-5">
            </div>
        </div>
        <div class="space-4"></div>
        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right" for="form-field-2">确认密码</label>
            <div class="col-sm-9">
                <input type="password" id='confPwd' value=""  class="col-xs-10 col-sm-5">
            </div>
        </div>
        <?php }?>
        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right" for="form-field-2">状态</label>
            <div class="col-sm-9">
                <select class="form-control select" id="is_delete">
                    <option value="1" <?php if (isset($_smarty_tpl->tpl_vars['data']->value['is_delete']) && $_smarty_tpl->tpl_vars['data']->value['is_delete'] == 1) {?>selected<?php }?>>有效</option>
                    <option value="2" <?php if (isset($_smarty_tpl->tpl_vars['data']->value['is_delete']) && $_smarty_tpl->tpl_vars['data']->value['is_delete'] == 2) {?>selected<?php }?>>无效</option>
                </select>
            </div>
        </div>
        <div class="space-4"></div>
        <div class="clearfix form-actions">
            <div class="col-md-offset-3 col-md-9">
                <button class="btn btn-info" type="button" id='save'>
                    <i class="ace-icon fa fa-check bigger-110"></i>
                    提交
                </button>
                &nbsp; &nbsp; &nbsp;
                <button class="btn" type="button" id='reset' >
                    <i class="ace-icon fa fa-undo bigger-110"></i>
                    重置
                </button>
            </div>
        </div>
    </form>
    <div class="hr hr-18 dotted hr-double"></div>
</div>
</div>



<?php echo '<script'; ?>
>

    //提交
    $('#save').click(function () {
        var agent_id = $.trim($('#agent_id').val());
        var pid = $.trim($('#sh_id').val());
        var login_user = $.trim($('#login_user').val());
        var login_name = $.trim($('#login_name').val());
        var pwd = $.trim($('#pwd').val());
        var conf_pwd = $.trim($('#confPwd').val());
        var is_delete = $.trim($('#is_delete').val());
        var type = "<?php echo $_GET['type'];?>
";
        var add = "<?php echo $_smarty_tpl->tpl_vars['add']->value;?>
";
        var error = false;
        var msg = '';

        if(login_user == '') {
            msg = '账号不能为空!';
            layer.alert(msg, {icon: 2});
            return;
        }else if(login_user.length < 4 || login_user.length >20){
             layer.alert('账号长度为4-20位！', {icon: 2});
            return false;
        } else if (login_user.length < 4 || login_user.length > 20) {
            msg = '账号长度只能为4-20位之间!';
            layer.alert(msg, {icon: 2});
            return;
        } else if (!/^[A-Za-z0-9_]*$/g.test(login_user)) {
            msg = '账号只能为数字字母下划线!';
            layer.alert(msg, {icon: 2});
            return;
        } else if (login_name == '') {
            msg = '昵称不能为空!';
            layer.alert(msg, {icon: 2});
            return;
        }else {
            
        }

        //新增页面密码验证
        if (type == 'create') {
            if(pid == '' && add == false){
                msg = '请选择股东!';
                layer.alert(msg, {icon: 2});
                return;
            }else if(pwd == '') {
                error = true;
                msg = '密码不能为空!';
            } else if (pwd.length < 6 || pwd.length > 20) {
                error = true;
                msg = '密码长度只能为6-20位之间!';
            } else if (!/^[A-Za-z0-9_]*$/g.test(pwd)) {
                error = true;
                msg = '密码只能为数字字母下划线!';
            } else if (conf_pwd == '') {
                error = true;
                msg = '确认密码不能为空!';
            } else if (conf_pwd.length < 6 || conf_pwd.length > 20) {
                error = true;
                msg = '确认密码长度只能为6-20位之间!';
            } else if (!/^[A-Za-z0-9_]*$/g.test(conf_pwd)) {
                error = true;
                msg = '确认密码只能为数字字母下划线!';
            } else if (conf_pwd != pwd) {
                error = true;
                msg = '密码和确认密码不一致!';
            }else {
                error = false;
                msg = '';
            }
        }

        if (error) {
            layer.alert(msg, {icon: 2});
            return false;
        }

        var data = {
            agent_id:agent_id,
            pid: pid,
            login_user: login_user,
            login_name: login_name,
            pwd: pwd,
            conf_pwd: conf_pwd,
            is_delete: is_delete,
        };

        $.ajax({
            type: "post",
            url: '/agent/ua_save',
            data: data,
            dataType: 'json',
            error: function () {
                layer.alert('网络异常,请稍后再试!', {icon: 2});
            },
            success: function (res) {
                if (res.code == 400) {
                    layer.alert(res.msg, {icon: 2});
                } else if(res.ErrorCode == 2){
                    layer.close(load);
                    layer.alert(res.ErrorMsg, {icon: 2});
                } else if (res.code == 200) {
                    layer.alert(res.msg, {icon: 1},function(){
                        window.location.href = '/agent/uaindex';
                    });
                }
            }
        });

    });

    //重置
    $('#reset').click(function () {
         $("#sh_id").find("option[value = '']").prop("selected", 'selected');
        $('#agent-form').find('input').val("");
    })

    $('#agent_index').click(function(){
        $.pjax({
            method: 'get',
            url: '/agent/uaindex',
            container: '#container'
        });
    })

    
<?php echo '</script'; ?>
><?php }
}
