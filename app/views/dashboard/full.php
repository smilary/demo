<?php
require_once __DIR__.'/../../lib/dashboard_manager.php';

// 获取完整看板数据
$stats = get_project_stats();
$orders = get_urgent_orders();
$approvals = get_recent_approvals();
?>
<!DOCTYPE html>
<html>
<head>
    <title>项目看板 - 详细视图</title>
    <style>
        .dashboard {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            padding: 20px;
        }
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .card-header {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .stat-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .progress-bar {
            height: 10px;
            background: #f0f0f0;
            border-radius: 5px;
            margin-top: 5px;
        }
        .progress {
            height: 100%;
            border-radius: 5px;
            background: #4CAF50;
        }
    </style>
</head>
<body>
    <header style="display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; background: #f5f5f5;">
        <div style="display: flex; gap: 20px;">
            <h1>项目看板</h1>
            <a href="/" style="align-self: center; padding: 5px 10px; background: #2196F3; color: white; text-decoration: none; border-radius: 4px;">返回首页</a>
        </div>
        <div style="display: flex; align-items: center; gap: 15px;">
            <span>欢迎您，管理员</span>
            <button style="padding: 5px 10px; background: #f44336; color: white; border: none; border-radius: 4px;">退出登录</button>
        </div>
    </header>

    <div class="dashboard">
        <!-- 项目统计卡片 -->
        <div class="card">
            <div class="card-header">项目概览</div>
            <div class="stat-item">
                <span>总项目数:</span>
                <span><?= $stats['total'] ?></span>
            </div>
            <div class="stat-item">
                <span>进行中:</span>
                <span><?= $stats['active'] ?></span>
            </div>
            <div class="stat-item">
                <span>已完成:</span>
                <span><?= $stats['completed'] ?></span>
            </div>
            <div class="stat-item">
                <span>完成率:</span>
                <span><?= round($stats['completion_rate']*100) ?>%</span>
            </div>
            <div class="progress-bar">
                <div class="progress" style="width: <?= $stats['completion_rate']*100 ?>%"></div>
            </div>
        </div>

        <!-- 紧急任务卡片 -->
        <div class="card">
            <div class="card-header">紧急生产指令</div>
            <?php foreach ($orders as $order): ?>
            <div class="stat-item">
                <span><?= $order['product_name'] ?></span>
                <span><?= $order['quantity'] ?>件</span>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- 最近审批卡片 -->
        <div class="card">
            <div class="card-header">最近审批</div>
            <?php foreach ($approvals as $approval): ?>
            <div class="stat-item">
                <span><?= $approval['record_type'] ?>#<?= $approval['record_id'] ?></span>
                <span style="color: <?= $approval['approval_result'] ? 'green' : 'red' ?>">
                    <?= $approval['approval_result'] ? '通过' : '拒绝' ?>
                </span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
