<?php
/* Smarty version 3.1.31, created on 2018-02-10 10:59:50
  from "/Users/frank/www/newproject/trunk/php-yii/backend/views/layouts/login.html" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5a7ed0a6851955_82463943',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e079bd1b9381bd2db7b83c241f537461e3abc034' => 
    array (
      0 => '/Users/frank/www/newproject/trunk/php-yii/backend/views/layouts/login.html',
      1 => 1517801681,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a7ed0a6851955_82463943 (Smarty_Internal_Template $_smarty_tpl) {
?>
<!DOCTYPE html>
<html lang="zh-CN"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="description" content="overview &amp; stats">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <title>Admin 后台登录页面</title>
        <link rel="stylesheet" href="<?php echo Yii::getAlias('@css');?>
/pack/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo Yii::getAlias('@css');?>
/pack/font-awesome.min.css">
        <link rel="stylesheet" href="<?php echo Yii::getAlias('@css');?>
/pack/ace-fonts.css">
        <link rel="stylesheet" href="<?php echo Yii::getAlias('@css');?>
/pack/ace.min.css" id="main-ace-style">

        <?php echo '<script'; ?>
 src="<?php echo Yii::getAlias('@js');?>
/pack/ace-extra.min.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="<?php echo Yii::getAlias('@js');?>
/pack/jquery-2.1.4.min.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="<?php echo Yii::getAlias('@js');?>
/pack/bootstrap.min.js"><?php echo '</script'; ?>
>
    </head>
    <style>
        .light-login {
            background: #dfe0e2 url(/public/images/pattern.jpg);
        }
        .blur-login {
            background: #394557 url(/public/images/meteorshower2.jpg);
        }
        .light-login .widget-box{
            padding: 0px;
        }
        .align-right a{cursor: pointer}
    </style>
    <body class="login-layout blur-login">
        <div class="main-container">
            <div class="main-content">
                <div class="row">
                    <div class="col-sm-10 col-sm-offset-1">
                        <div class="login-container">
                            <div class="center">
                                <h1>
                                    <span class="red"> Admin </span>
                                </h1>
                                <h4 class="blue" id="id-company-text"></h4>
                            </div>
                            <div class="space-6"></div>
                            <?php echo $_smarty_tpl->tpl_vars['content']->value;?>

                            <div class="navbar-fixed-top align-right">
                                <br>
                                &nbsp;
                                <a id="btn-login-dark" >暗黑</a>
                                &nbsp;
                                <span class="blue">/</span>
                                &nbsp;
                                <a id="btn-login-blur" >蓝色</a>
                                &nbsp;
                                <span class="blue">/</span>
                                &nbsp;
                                <a id="btn-login-light" >明亮</a>
                                &nbsp; &nbsp; &nbsp;
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php echo '<script'; ?>
>
            //暗黑
            $('#btn-login-dark').click(function(){
            $('body.login-layout').removeClass('light-login');
            $('body.login-layout').removeClass('blur-login');
            });
            //蓝色
            $('#btn-login-blur').click(function(){
            $('body.login-layout').removeClass('light-login');
            $('body.login-layout').addClass('blur-login');
            });
            //明亮
            $('#btn-login-light').click(function(){
            $('body.login-layout').removeClass('blur-login');
            $('body.login-layout').addClass('light-login');
            })
        <?php echo '</script'; ?>
>

    </body>
</html><?php }
}
