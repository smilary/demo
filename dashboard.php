<?php
session_start();
require_once __DIR__.'/config/db_connect.php';
require_once __DIR__.'/lib/dashboard_manager.php';

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// 生成CSRF Token
if (empty($_SESSION['csrf_token'])) {
$_SESSION['csrf_token'] = md5(uniqid(mt_rand(), true));
}

// 获取项目统计数据(带缓存)
$cache_key = 'dashboard_stats_' . $_SESSION['user_id'];
if (!isset($_SESSION[$cache_key]) || (time() - $_SESSION[$cache_key]['timestamp'] > 300)) {
    $_SESSION[$cache_key] = [
        'data' => get_project_stats(),
        'timestamp' => time()
    ];
}
$stats = $_SESSION[$cache_key]['data'];

// 获取紧急订单(带缓存)
$cache_key = 'urgent_orders_' . $_SESSION['user_id'];
if (!isset($_SESSION[$cache_key]) || (time() - $_SESSION[$cache_key]['timestamp'] > 180)) {
    $_SESSION[$cache_key] = [
        'data' => get_urgent_orders(),
        'timestamp' => time()
    ];
}
$orders = $_SESSION[$cache_key]['data'];

// 获取最近审批(带缓存)
$cache_key = 'recent_approvals_' . $_SESSION['user_id'];
if (!isset($_SESSION[$cache_key]) || (time() - $_SESSION[$cache_key]['timestamp'] > 180)) {
    $_SESSION[$cache_key] = [
        'data' => get_recent_approvals(),
        'timestamp' => time()
    ];
}
$approvals = $_SESSION[$cache_key]['data'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>项目看板 - 登录后首页</title>
    <!-- 引入Element UI样式 -->
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
    <link rel="stylesheet" href="styles/dashboard.css">
    <!-- 引入jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="navbar">
        <div class="logo">
            <img src="img/logo.png" alt="logo">
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
                            <span class="top-menus-item"><span class="top-menu-title">财务管理</span></span>
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
                        <span>欢迎您，<?php echo isset($_SESSION['real_name']) ? htmlspecialchars($_SESSION['real_name']) : '管理员'; ?></span>
                    </div>
                    <div class="navbar-operation-item hover-effect">
                        <a href="index.php?logout=1" style="color: inherit; text-decoration: none;">
                            <em class="el-icon-switch-button"></em> 退出
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 左侧菜单栏 -->
    <div style="display: flex;">
        <ul data-v-4d4bf192="" data-v-422060c6="" role="menubar" class="el-menu" style="background-color: rgb(245, 247, 249); width: 220px; min-height: calc(100vh - 60px);" id="leftSidebar">
            <!-- 个人工作台 -->
            <div data-v-4d4bf192="" class="menu-wrapper">
                <a href="workspace.php" aria-current="page" class="router-link-exact-active router-link-active" style="text-decoration: none;">
                    <li role="menuitem" tabindex="-1" class="el-menu-item is-active submenu-title-noDropdown" style="padding-left: 20px; color: rgb(0, 58, 112); background-color: rgb(216 224 233); border-left: 3px solid #1890ff; transition: all 0.3s ease; font-size: 16px; font-family: 'Microsoft YaHei', Arial, sans-serif;">
                        <i class="el-icon-s-home"></i>
                        <span>个人工作台</span>
                    </li>
                </a>
            </div>
            
            <!-- 合同管理二级菜单 -->
            <div data-v-4d4bf192="" class="menu-wrapper submenu-container" data-menu-name="合同管理" style="display: none;">
                <ul role="menu" class="el-menu el-menu--inline contract-menu" style="background-color: rgb(245, 247, 249); box-shadow: inset 0 2px 5px rgba(0,0,0,0.05); border-radius: 4px; margin: 5px 0;">
                        <a href="#/contractServiceConfirmation" style="text-decoration: none;">
                            <li role="menuitem" tabindex="-1" class="el-menu-item" style="padding-left: 40px; color: rgb(86, 94, 109); background-color: rgb(245, 247, 249); font-size: 15px; font-family: 'Microsoft YaHei', Arial, sans-serif; margin: 2px 0; border-radius: 4px;">
                                <span>服务确认</span>
                            </li>
                        </a>
                        <a href="#/tyTsMilestoneConfirmation" style="text-decoration: none;">
                            <li role="menuitem" tabindex="-1" class="el-menu-item" style="padding-left: 40px; color: rgb(86, 94, 109); background-color: rgb(245, 247, 249); font-size: 15px; font-family: 'Microsoft YaHei', Arial, sans-serif; margin: 2px 0; border-radius: 4px;">
                                <span>里程碑确认</span>
                            </li>
                        </a>
                        <a href="#/contractConfirmationAcceptance" style="text-decoration: none;">
                            <li role="menuitem" tabindex="-1" class="el-menu-item" style="padding-left: 40px; color: rgb(86, 94, 109); background-color: rgb(245, 247, 249); font-size: 15px; font-family: 'Microsoft YaHei', Arial, sans-serif; margin: 2px 0; border-radius: 4px;">
                                <span>合同验收</span>
                            </li>
                        </a>
                        <a href="#/initiateContractFines" style="text-decoration: none;">
                            <li role="menuitem" tabindex="-1" class="el-menu-item" style="padding-left: 40px; color: rgb(86, 94, 109); background-color: rgb(245, 247, 249); font-size: 15px; font-family: 'Microsoft YaHei', Arial, sans-serif; margin: 2px 0; border-radius: 4px;">
                                <span>合同罚款</span>
                            </li>
                        </a>
                    </ul>
                </li>
            </div>
            
            <!-- 项目管理二级菜单 -->
            <div data-v-4d4bf192="" class="menu-wrapper submenu-container" data-menu-name="项目管理" style="display: none;">
                <ul role="menu" class="el-menu el-menu--inline project-menu" style="background-color: rgb(245, 247, 249); box-shadow: inset 0 2px 5px rgba(0,0,0,0.05); border-radius: 4px; margin: 5px 0;">
                        <a href="#/projectList" style="text-decoration: none;">
                            <li role="menuitem" tabindex="-1" class="el-menu-item" style="padding-left: 40px; color: rgb(86, 94, 109); background-color: rgb(245, 247, 249); font-size: 15px; font-family: 'Microsoft YaHei', Arial, sans-serif; margin: 2px 0; border-radius: 4px;">
                                <span>项目列表</span>
                            </li>
                        </a>
                        <a href="#/projectSchedule" style="text-decoration: none;">
                            <li role="menuitem" tabindex="-1" class="el-menu-item" style="padding-left: 40px; color: rgb(86, 94, 109); background-color: rgb(245, 247, 249); font-size: 15px; font-family: 'Microsoft YaHei', Arial, sans-serif; margin: 2px 0; border-radius: 4px;">
                                <span>项目进度</span>
                            </li>
                        </a>
                        <a href="#/projectResource" style="text-decoration: none;">
                            <li role="menuitem" tabindex="-1" class="el-menu-item" style="padding-left: 40px; color: rgb(86, 94, 109); background-color: rgb(245, 247, 249); font-size: 15px; font-family: 'Microsoft YaHei', Arial, sans-serif; margin: 2px 0; border-radius: 4px;">
                                <span>资源分配</span>
                            </li>
                        </a>
                    </ul>
                </li>
            </div>
            
            <!-- 生产指令二级菜单 -->
            <div data-v-4d4bf192="" class="menu-wrapper submenu-container" data-menu-name="生产指令" style="display: none;">
                <ul role="menu" class="el-menu el-menu--inline production-menu" style="background-color: rgb(245, 247, 249); box-shadow: inset 0 2px 5px rgba(0,0,0,0.05); border-radius: 4px; margin: 5px 0;">
                        <a href="#/productionOrders" style="text-decoration: none;">
                            <li role="menuitem" tabindex="-1" class="el-menu-item" style="padding-left: 40px; color: rgb(86, 94, 109); background-color: rgb(245, 247, 249); font-size: 15px; font-family: 'Microsoft YaHei', Arial, sans-serif; margin: 2px 0; border-radius: 4px;">
                                <span>生产订单</span>
                            </li>
                        </a>
                        <a href="#/productionSchedule" style="text-decoration: none;">
                            <li role="menuitem" tabindex="-1" class="el-menu-item" style="padding-left: 40px; color: rgb(86, 94, 109); background-color: rgb(245, 247, 249); font-size: 15px; font-family: 'Microsoft YaHei', Arial, sans-serif; margin: 2px 0; border-radius: 4px;">
                                <span>生产排期</span>
                            </li>
                        </a>
                    </ul>
                </li>
            </div>

            <!-- 采购管理二级菜单 -->
            <div data-v-4d4bf192="" class="menu-wrapper submenu-container" data-menu-name="采购管理" style="display: none;">
                <ul role="menu" class="el-menu el-menu--inline purchase-menu" style="background-color: rgb(245, 247, 249); box-shadow: inset 0 2px 5px rgba(0,0,0,0.05); border-radius: 4px; margin: 5px 0;">
                        <a href="#/purchaseOrderConfirmation" style="text-decoration: none;">
                            <li role="menuitem" tabindex="-1" class="el-menu-item" style="padding-left: 40px; color: rgb(86, 94, 109); background-color: rgb(245, 247, 249); font-size: 15px; font-family: 'Microsoft YaHei', Arial, sans-serif; margin: 2px 0; border-radius: 4px;">
                                <span>采购订单</span>
                            </li>
                        </a>
                        <a href="#/materialInventoryManagement" style="text-decoration: none;">
                            <li role="menuitem" tabindex="-1" class="el-menu-item" style="padding-left: 40px; color: rgb(86, 94, 109); background-color: rgb(245, 247, 249); font-size: 15px; font-family: 'Microsoft YaHei', Arial, sans-serif; margin: 2px 0; border-radius: 4px;">
                                <span>库存管理</span>
                            </li>
                        </a>
                        <a href="#/materialWarehouseApproval" style="text-decoration: none;">
                            <li role="menuitem" tabindex="-1" class="el-menu-item" style="padding-left: 40px; color: rgb(86, 94, 109); background-color: rgb(245, 247, 249); font-size: 15px; font-family: 'Microsoft YaHei', Arial, sans-serif; margin: 2px 0; border-radius: 4px;">
                                <span>入库审批</span>
                            </li>
                        </a>
                    </ul>
                </li>
            </div>

            <!-- 物流发货二级菜单 -->
            <div data-v-4d4bf192="" class="menu-wrapper submenu-container" data-menu-name="物流发货" style="display: none;">
                <ul role="menu" class="el-menu el-menu--inline logistics-menu" style="background-color: rgb(245, 247, 249); box-shadow: inset 0 2px 5px rgba(0,0,0,0.05); border-radius: 4px; margin: 5px 0;">
                        <a href="#/noGoldwindDelivery" style="text-decoration: none;">
                            <li role="menuitem" tabindex="-1" class="el-menu-item" style="padding-left: 40px; color: rgb(86, 94, 109); background-color: rgb(245, 247, 249); font-size: 15px; font-family: 'Microsoft YaHei', Arial, sans-serif; margin: 2px 0; border-radius: 4px;">
                                <span>订单装箱</span>
                            </li>
                        </a>
                        <a href="#/packingListQuery" style="text-decoration: none;">
                            <li role="menuitem" tabindex="-1" class="el-menu-item" style="padding-left: 40px; color: rgb(86, 94, 109); background-color: rgb(245, 247, 249); font-size: 15px; font-family: 'Microsoft YaHei', Arial, sans-serif; margin: 2px 0; border-radius: 4px;">
                                <span>装箱单查询</span>
                            </li>
                        </a>
                        <a href="#/deliveryManage/supplierDeliveryMsg" style="text-decoration: none;">
                            <li role="menuitem" tabindex="-1" class="el-menu-item" style="padding-left: 40px; color: rgb(86, 94, 109); background-color: rgb(245, 247, 249); font-size: 15px; font-family: 'Microsoft YaHei', Arial, sans-serif; margin: 2px 0; border-radius: 4px;">
                                <span>供应商发货</span>
                            </li>
                        </a>
                    </ul>
                </li>
            </div>
            
            <!-- 财务管理二级菜单 -->
            <div data-v-4d4bf192="" class="menu-wrapper submenu-container" data-menu-name="财务管理" style="display: none;">
                <ul role="menu" class="el-menu el-menu--inline finance-menu" style="background-color: rgb(245, 247, 249); box-shadow: inset 0 2px 5px rgba(0,0,0,0.05); border-radius: 4px; margin: 5px 0;">
                        <a href="#/receiptManagement" style="text-decoration: none;">
                            <li role="menuitem" tabindex="-1" class="el-menu-item" style="padding-left: 40px; color: rgb(86, 94, 109); background-color: rgb(245, 247, 249); font-size: 15px; font-family: 'Microsoft YaHei', Arial, sans-serif; margin: 2px 0; border-radius: 4px;">
                                <span>收款管理</span>
                            </li>
                        </a>
                        <a href="#/paymentManagement" style="text-decoration: none;">
                            <li role="menuitem" tabindex="-1" class="el-menu-item" style="padding-left: 40px; color: rgb(86, 94, 109); background-color: rgb(245, 247, 249); font-size: 15px; font-family: 'Microsoft YaHei', Arial, sans-serif; margin: 2px 0; border-radius: 4px;">
                                <span>付款管理</span>
                            </li>
                        </a>
                        <a href="#/invoiceManagement" style="text-decoration: none;">
                            <li role="menuitem" tabindex="-1" class="el-menu-item" style="padding-left: 40px; color: rgb(86, 94, 109); background-color: rgb(245, 247, 249); font-size: 15px; font-family: 'Microsoft YaHei', Arial, sans-serif; margin: 2px 0; border-radius: 4px;">
                                <span>发票管理</span>
                            </li>
                        </a>
                        <a href="#/contractPaymentPlans" style="text-decoration: none;">
                            <li role="menuitem" tabindex="-1" class="el-menu-item" style="padding-left: 40px; color: rgb(86, 94, 109); background-color: rgb(245, 247, 249); font-size: 15px; font-family: 'Microsoft YaHei', Arial, sans-serif; margin: 2px 0; border-radius: 4px;">
                                <span>合同收款计划</span>
                            </li>
                        </a>
                    </ul>
                </li>
            </div>
            
            <!-- 系统管理二级菜单 -->
            <div data-v-4d4bf192="" class="menu-wrapper submenu-container" data-menu-name="系统管理" style="display: none;">
                <ul role="menu" class="el-menu el-menu--inline system-menu" style="background-color: rgb(245, 247, 249); box-shadow: inset 0 2px 5px rgba(0,0,0,0.05); border-radius: 4px; margin: 5px 0;">
                        <a href="#/userManagement" style="text-decoration: none;">
                            <li role="menuitem" tabindex="-1" class="el-menu-item" style="padding-left: 40px; color: rgb(86, 94, 109); background-color: rgb(245, 247, 249); font-size: 15px; font-family: 'Microsoft YaHei', Arial, sans-serif; margin: 2px 0; border-radius: 4px;">
                                <span>用户管理</span>
                            </li>
                        </a>
                        <a href="#/permissionConfiguration" style="text-decoration: none;">
                            <li role="menuitem" tabindex="-1" class="el-menu-item" style="padding-left: 40px; color: rgb(86, 94, 109); background-color: rgb(245, 247, 249); font-size: 15px; font-family: 'Microsoft YaHei', Arial, sans-serif; margin: 2px 0; border-radius: 4px;">
                                <span>权限配置</span>
                            </li>
                        </a>
                        <a href="#/systemSettings" style="text-decoration: none;">
                            <li role="menuitem" tabindex="-1" class="el-menu-item" style="padding-left: 40px; color: rgb(86, 94, 109); background-color: rgb(245, 247, 249); font-size: 15px; font-family: 'Microsoft YaHei', Arial, sans-serif; margin: 2px 0; border-radius: 4px;">
                                <span>系统设置</span>
                            </li>
                        </a>
                    </ul>
                </li>
            </div>
        </ul>

        <!-- 主内容区 -->
        <div class="dashboard" style="flex: 1; padding: 20px;">
            <!-- 项目跟踪看板 -->
            <div class="card" style="grid-column: span 3;">
                <div class="card-header">项目跟踪看板</div>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px;">
                    <!-- 项目状态列 -->
                    <div class="status-column">
                        <h3>未开始</h3>
                        <div class="task-card">
                            <div class="task-title">项目A需求分析</div>
                            <div class="task-progress">
                                <div class="progress" style="width: 0%"></div>
                            </div>
                            <div class="task-due">截止: 2023-12-15</div>
                        </div>
                    </div>
                    
                    <!-- 进行中列 -->
                    <div class="status-column">
                        <h3>进行中</h3>
                        <div class="task-card">
                            <div class="task-title">项目B开发</div>
                            <div class="task-progress">
                                <div class="progress" style="width: 65%"></div>
                            </div>
                            <div class="task-due">截止: 2023-12-20</div>
                        </div>
                        <div class="task-card">
                            <div class="task-title">项目C测试</div>
                            <div class="task-progress">
                                <div class="progress" style="width: 30%"></div>
                            </div>
                            <div class="task-due">截止: 2023-12-25</div>
                        </div>
                    </div>
                    
                    <!-- 待验收列 -->
                    <div class="status-column">
                        <h3>待验收</h3>
                        <div class="task-card">
                            <div class="task-title">项目D交付</div>
                            <div class="task-progress">
                                <div class="progress" style="width: 90%"></div>
                            </div>
                            <div class="task-due">截止: 2023-12-10</div>
                        </div>
                    </div>
                    
                    <!-- 已完成列 -->
                    <div class="status-column">
                        <h3>已完成</h3>
                        <div class="task-card">
                            <div class="task-title">项目E上线</div>
                            <div class="task-progress">
                                <div class="progress" style="width: 100%"></div>
                            </div>
                            <div class="task-due">2023-12-05完成</div>
                        </div>
                    </div>
                </div>
            </div>

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
                <div class="urgent-order">
                    <div class="order-title"><?= htmlspecialchars($order['title']) ?></div>
                    <div class="order-details">
                        <span>订单号: <?= htmlspecialchars($order['order_id']) ?></span>
                        <span>截止日期: <?= htmlspecialchars($order['due_date']) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- 主内容区域 -->
    <div id="mainContent" style="flex: 1; padding: 20px; overflow: auto;">
        <!-- 页面内容将通过AJAX加载到这里 -->
    </div>

    <!-- JavaScript代码 -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 为顶部菜单添加点击事件
            const topMenuItems = document.querySelectorAll('.top-menus-item');
            
            // 激活默认菜单（合同管理）
            const defaultActiveMenu = document.querySelector('.top-menus-item.active');
            if (defaultActiveMenu) {
                const menuName = defaultActiveMenu.querySelector('.top-menu-title').textContent;
                showSubmenu(menuName);
            }
            
            topMenuItems.forEach(item => {
                item.addEventListener('click', function() {
                    // 移除所有顶部菜单的激活状态
                    topMenuItems.forEach(i => i.classList.remove('active'));
                    
                    // 激活当前点击的顶部菜单
                    this.classList.add('active');
                    
                    // 获取当前点击的菜单名称
                    const menuName = this.querySelector('.top-menu-title').textContent;
                    
                    // 显示对应的二级菜单
                    showSubmenu(menuName);
                });
            });
            
            // 显示指定名称的二级菜单
            function showSubmenu(menuName) {
                // 隐藏所有二级菜单容器
                document.querySelectorAll('.submenu-container').forEach(container => {
                    container.style.display = 'none';
                });
                
                // 显示对应的二级菜单容器
                const targetSubmenu = document.querySelector(`.submenu-container[data-menu-name="${menuName}"]`);
                if (targetSubmenu) {
                    targetSubmenu.style.display = 'block';
                }
            }
            
            // 为二级菜单项添加点击事件
            const menuItems = document.querySelectorAll('.el-menu-item');
            menuItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    // 阻止默认行为，防止立即跳转（可选，如果需要在跳转前执行其他操作）
                    // e.preventDefault();
                    
                    // 移除所有菜单项的激活状态
                    menuItems.forEach(i => {
                        i.classList.remove('is-active');
                        i.style.color = 'rgb(86, 94, 109)';
                        i.style.backgroundColor = 'rgb(245, 247, 249)';
                    });
                    
                    // 激活当前点击的菜单项
                    this.classList.add('is-active');
                    this.style.color = 'rgb(0, 58, 112)';
                    this.style.backgroundColor = 'rgb(235, 240, 245)';
                    
                    // 如果需要在跳转前执行其他操作，可以在这里添加
                    // 然后手动跳转
                    // window.location.href = this.parentElement.getAttribute('href');
                });
            });
            
            // 添加CSS样式
            const style = document.createElement('style');
            style.textContent = `
                .el-menu-item:hover {
                    background-color: rgb(235, 240, 245) !important;
                }
                .top-menus-item {
                    cursor: pointer;
                    padding: 0 15px;
                }
                .top-menus-item.active .top-menu-title {
                    color: #1890ff;
                    border-bottom: 2px solid #1890ff;
                    padding-bottom: 5px;
                }
                .submenu-title {
                    margin-top: 10px;
                }
            `;
            document.head.appendChild(style);
        });
    </script>
