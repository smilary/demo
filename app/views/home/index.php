<?php
require_once __DIR__.'/../../../lib/dashboard_manager.php';
// 获取首页摘要数据
$summary = get_dashboard_summary();
?>
<!DOCTYPE html>
<html>
<head>
    <title>首页 - 项目管理系统</title>
    <style>
        .navbar {
            display: flex;
            height: 60px;
            background: #001529;
            color: white;
            padding: 0 20px;
        }
        .logo {
            width: 200px;
            display: flex;
            align-items: center;
        }
        .logo img {
            height: 32px;
        }
        .navbar-parts {
            flex: 1;
            display: flex;
            justify-content: space-between;
        }
        .navbar-left {
            display: flex;
            align-items: center;
        }
        .system-name {
            margin: 0 20px;
            font-size: 18px;
        }
        .top-menus-wrapper {
            position: relative;
        }
        .top-menus-content {
            display: flex;
        }
        .top-menus-item {
            padding: 0 15px;
            cursor: pointer;
            line-height: 60px;
        }
        .top-menus-item:hover {
            background: rgba(255,255,255,0.1);
        }
        .top-menus-item.active {
            background: #1890ff;
        }
        .top-menu-title {
            font-size: 14px;
        }
        .navbar-right {
            display: flex;
            align-items: center;
        }
        .navbar-operation {
            display: flex;
            align-items: center;
        }
        .right-menu-item {
            padding: 0 12px;
            cursor: pointer;
            font-size: 18px;
        }
        .navbar-operation-item {
            margin-left: 15px;
            font-size: 14px;
        }
        .summary-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            padding: 20px;
        }
        .summary-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            text-align: center;
        }
        .summary-value {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }
        .summary-label {
            color: #666;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">
            <img src="img/logo.svg" alt="logo">
        </div>
        <div class="navbar-parts">
            <div class="navbar-left">
                <div class="system-name">
                    <h3 class="system-name">项目管理系统</h3>
                </div>
                <div class="navbar-custom">
                    <div class="top-menus-wrapper">
                        <div class="top-menus-content">
                            <span class="top-menus-item active"><span class="top-menu-title">合同管理</span></span>
                            <span class="top-menus-item"><span class="top-menu-title">项目管理</span></span>
                            <span class="top-menus-item"><span class="top-menu-title">生产指令</span></span>
                            <span class="top-menus-item"><span class="top-menu-title">采购管理</span></span>
                            <span class="top-menus-item"><span class="top-menu-title">物流发货</span></span>
                            <span class="top-menus-item"><span class="top-menu-title">系统管理</span></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="navbar-right">
                <div class="navbar-operation">
                    <div class="right-menu-item hover-effect item">
                        <i class="el-icon-search search-icon"></i>
                    </div>
                    <div class="right-menu-item hover-effect item">
                        <i class="el-icon-bell"></i>
                    </div>
                    <div class="navbar-operation-item">
                        <span>欢迎您，管理员</span>
                    </div>
                    <div class="navbar-operation-item hover-effect">
                        <em class="el-icon-switch-button"></em> 退出
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="summary-container">
        <div class="summary-card">
            <div class="summary-value"><?= $summary['total_projects'] ?></div>
            <div class="summary-label">总项目数</div>
        </div>
        <div class="summary-card">
            <div class="summary-value"><?= $summary['active_projects'] ?></div>
            <div class="summary-label">进行中</div>
        </div>
        <div class="summary-card">
            <div class="summary-value"><?= $summary['completion_rate'] ?>%</div>
            <div class="summary-label">完成率</div>
        </div>
    </div>

    <div style="padding: 20px; display: flex; gap: 15px;">
        <a href="/dashboard/full.php" style="padding: 10px 15px; background: #2196F3; color: white; text-decoration: none; border-radius: 4px;">
            完整看板 →
        </a>
        <a href="/contracts.php" style="padding: 10px 15px; background: #4CAF50; color: white; text-decoration: none; border-radius: 4px;">
            合同管理 →
        </a>
    </div>
</body>
</html>
