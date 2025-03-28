<?php
// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// 生成CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = md5(uniqid(mt_rand(), true));
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo isset($page_title) ? $page_title : '项目管理系统'; ?></title>
    <!-- 引入Element UI样式 -->
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
    <link rel="stylesheet" href="styles/dashboard.css">
    <?php if (isset($extra_css) && is_array($extra_css)): ?>
        <?php foreach ($extra_css as $css_file): ?>
            <link rel="stylesheet" href="<?php echo $css_file; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
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
                            <span class="top-menus-item<?php echo isset($active_menu) && $active_menu === '合同管理' ? ' active' : ''; ?>"><span class="top-menu-title">合同管理</span></span>
                            <span class="top-menus-item<?php echo isset($active_menu) && $active_menu === '项目管理' ? ' active' : ''; ?>"><span class="top-menu-title">项目管理</span></span>
                            <span class="top-menus-item<?php echo isset($active_menu) && $active_menu === '生产指令' ? ' active' : ''; ?>"><span class="top-menu-title">生产指令</span></span>
                            <span class="top-menus-item<?php echo isset($active_menu) && $active_menu === '采购管理' ? ' active' : ''; ?>"><span class="top-menu-title">采购管理</span></span>
                            <span class="top-menus-item<?php echo isset($active_menu) && $active_menu === '物流发货' ? ' active' : ''; ?>"><span class="top-menu-title">物流发货</span></span>
                            <span class="top-menus-item<?php echo isset($active_menu) && $active_menu === '财务管理' ? ' active' : ''; ?>"><span class="top-menu-title">财务管理</span></span>
                            <span class="top-menus-item<?php echo isset($active_menu) && $active_menu === '系统管理' ? ' active' : ''; ?>"><span class="top-menu-title">系统管理</span></span>
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
                <a href="/demo/workspace_new.php" aria-current="page" class="router-link-exact-active router-link-active" style="text-decoration: none;">
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