<?php
// 个人工作台入口文件 - 根目录版本

// 设置内容视图路径
$content_view = __DIR__.'/app/views/dashboard/workspace.php';

// 设置页面标题
$page_title = '个人工作台 - 项目管理系统';

// 设置激活菜单
$active_menu = '';

// 设置额外CSS文件
$extra_css = ['styles/workspace.css'];

// 包含布局模板
require_once __DIR__.'/app/views/templates/layout.php';