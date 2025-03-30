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

// 设置内容视图路径
$content_view = __DIR__.'/app/views/dashboard/workspace.php';

// 设置页面标题
$page_title = '个人工作台';

// 设置激活菜单
$active_menu = '';

// 设置额外CSS文件
$extra_css = ['styles/workspace.css'];

// 判断是否为工作台页面
$current_uri = $_SERVER['REQUEST_URI'];
$is_workspace = strpos($page_title, '个人工作台') !== false || 
               preg_match('/contractServiceConfirmation/i', $current_uri) || 
               preg_match('/tyTsMilestoneConfirmation/i', $current_uri);

// 始终添加标签样式，因为我们现在在所有页面都显示标签
$extra_css[] = 'styles/tabs.css';

// 设置额外JS文件
$extra_js = isset($extra_js) ? $extra_js : [];

// 在所有页面处理标签
// 初始化标签会话存储
if (!isset($_SESSION['tabs'])) {
    $_SESSION['tabs'] = [];
}

// 当前页面信息
$current_page = [
    'title' => $page_title,
    'url' => $_SERVER['REQUEST_URI'],
    'id' => uniqid('tab_')
];

// 添加或激活当前标签
$tab_exists = false;
foreach ($_SESSION['tabs'] as $key => $tab) {
    if ($tab['url'] == $current_page['url']) {
        $_SESSION['tabs'][$key]['active'] = true;
        $current_page['id'] = $tab['id'];
        $tab_exists = true;
    } else {
        $_SESSION['tabs'][$key]['active'] = false;
    }
}

// 如果是工作台页面且没有标签，或者是其他页面且标签不存在，则添加新标签
if (($is_workspace && empty($_SESSION['tabs'])) || (!$is_workspace && !$tab_exists)) {
    $current_page['active'] = true;
    $_SESSION['tabs'][] = $current_page;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo isset($page_title) ? $page_title : '项目管理系统'; ?></title>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token']; ?>">
    <!-- 引入Element UI样式 -->
    <link rel="stylesheet" href="styles/element-ui/index.css">
    <link rel="stylesheet" href="styles/dashboard.css">
    <link rel="shortcut icon" href="favicon.ico">
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
            <img src="img/logo1.png" alt="logo">
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
                <a href="main.php" aria-current="page" class="router-link-exact-active router-link-active" style="text-decoration: none;">
                    <li role="menuitem" tabindex="-1" class="el-menu-item is-active submenu-title-noDropdown" style="padding-left: 0px; text-align: left; color: rgb(0, 58, 112); background-color: rgb(216 224 233); border-left: 3px solid #1890ff; transition: all 0.3s ease; font-size: 16px; font-family: 'Microsoft YaHei', Arial, sans-serif;">
                        <i class="el-icon-s-home" style="margin-left: 0px;"></i>
                        <span>个人工作台</span>
                    </li>
                </a>
            </div>
            
            <!-- 合同管理二级菜单 -->
            <div data-v-4d4bf192="" class="menu-wrapper submenu-container" data-menu-name="合同管理" style="display: none;">
                <ul role="menu" class="el-menu el-menu--inline contract-menu" style="background-color: rgb(245, 247, 249); box-shadow: inset 0 2px 5px rgba(0,0,0,0.05); border-radius: 4px; margin: 5px 0;">
                        <a href="#/contracts" style="text-decoration: none;">
                            <li role="menuitem" tabindex="-1" class="el-menu-item" style="padding-left: 40px; color: rgb(86, 94, 109); background-color: rgb(245, 247, 249); font-size: 15px; font-family: 'Microsoft YaHei', Arial, sans-serif; margin: 2px 0; border-radius: 4px;">
                                <span>合同列表</span>
                            </li>
                        </a>
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
                </ul>
            </div>
        </ul>

        <!-- 主内容区域 -->
        <div style="flex: 1;">
            <!-- 标签栏 - 在所有页面显示 -->
            <div class="tabs-container">
                <div class="tabs-wrapper">
                    <div class="tabs">
                        <?php if (isset($_SESSION['tabs']) && !empty($_SESSION['tabs'])): ?>
                            <?php foreach ($_SESSION['tabs'] as $tab): ?>
                                <?php $is_workspace_tab = strpos($tab['title'], '个人工作台') !== false; ?>
                                <div class="tab <?php echo $tab['active'] ? 'active' : ''; ?>" data-tab-id="<?php echo $tab['id']; ?>" data-tab-url="<?php echo htmlspecialchars($tab['url']); ?>" onclick="switchTab('<?php echo $tab['id']; ?>')">
                                    <span class="tab-title"><?php echo htmlspecialchars($tab['title']); ?></span>
                                    <?php if (!$is_workspace_tab): ?>
                                        <span class="tab-close" onclick="closeTab('<?php echo $tab['id']; ?>', event)">×</span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="tab-warning" id="tabWarning">
                        <i class="el-icon-warning"></i>
                        <span>您已打开过多标签，请关闭一些标签后继续。</span>
                    </div>
                </div>
            </div>

            <!-- 主内容区域 -->
            <div id="mainContent">
                <?php 
                // 这里将包含具体页面内容
                if (isset($content_view) && file_exists($content_view)) {
                    include $content_view;
                }
                ?>
            </div>
        </div>
    </div>

    <!-- JavaScript代码 -->
    <!-- 引入标签管理脚本 -->
    <script src="js/tab_session_handler.js"></script>
    <script src="js/menu_handler.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
                    // 添加CSRF Token和请求类型到AJAX请求头
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': '<?= $_SESSION["csrf_token"] ?>',
                            'X-Requested-With': 'XMLHttpRequest'  // 确保所有AJAX请求都包含这个头部
                        }
                    });
            
            // 为顶部菜单添加点击事件
            const topMenuItems = document.querySelectorAll('.top-menus-item');
            
            // 激活默认菜单
            <?php if (isset($active_menu)): ?>
            const defaultActiveMenu = document.querySelector('.top-menus-item.active');
            if (defaultActiveMenu) {
                const menuName = defaultActiveMenu.querySelector('.top-menu-title').textContent;
                showSubmenu(menuName);
            }
            <?php endif; ?>
            
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
            
            // 为二级菜单添加点击展开/收起功能
            const submenuTitles = document.querySelectorAll('.el-submenu__title');
            submenuTitles.forEach(title => {
                title.addEventListener('click', function() {
                    const submenu = this.parentElement.querySelector('.el-menu--inline');
                    const isHidden = submenu.style.display === 'none';
                    
                    // 收起所有其他二级菜单
                    document.querySelectorAll('.el-menu--inline').forEach(menu => {
                        if (menu !== submenu) {
                            menu.style.display = 'none';
                            menu.parentElement.querySelector('.el-submenu__icon-arrow').className = 'el-submenu__icon-arrow el-icon-arrow-down';
                        }
                    });
                    
                    // 切换当前二级菜单
                    submenu.style.display = isHidden ? 'block' : 'none';
                    this.querySelector('.el-submenu__icon-arrow').className = 
                        isHidden ? 'el-submenu__icon-arrow el-icon-arrow-up' : 'el-submenu__icon-arrow el-icon-arrow-down';
                });
            });

            // 为菜单项添加点击激活样式和导航功能
            const menuItems = document.querySelectorAll('.el-menu-item');
            menuItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    // 阻止默认行为，防止立即跳转
                    e.preventDefault();
                    
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
                    
                    // 获取父元素的href属性
                    const href = this.parentElement.getAttribute('href');
                    
                    // 如果是标签页面，则通过标签系统打开
                    if (href && href !== '#') {
                        // 延迟执行，确保样式已应用
                        setTimeout(() => {
                            window.location.href = href;
                        }, 100);
                    }
                });
            });

            // 切换标签函数
            window.switchTab = function(tabId) {
                // 查找要切换到的标签
                const tabs = document.querySelectorAll('.tab');
                let targetUrl = '';
                let targetTab = null;
                
                // 遍历所有标签，找到目标标签的URL和对象
                tabs.forEach(tab => {
                    if (tab.getAttribute('data-tab-id') === tabId) {
                        targetUrl = tab.getAttribute('data-tab-url');
                        targetTab = tab;
                        // 设置当前标签为活动状态
                        tab.classList.add('active');
                    } else {
                        // 移除其他标签的活动状态
                        tab.classList.remove('active');
                    }
                });
                
                // 如果找到了目标URL
                if (targetUrl && targetTab) {
                    // 发送AJAX请求，更新标签活动状态
                    $.ajax({
                        url: 'api/switch_tab.php',
                        type: 'POST',
                        data: {
                            tab_id: tabId,
                            csrf_token: getCsrfToken()
                        },
                        success: function(response) {
                            console.log('标签状态已更新');
                            
                            // 检查URL是否包含功能页面标识
                            if (targetUrl.includes('#/')) {
                                // 提取页面名称
                                const pageName = targetUrl.split('#/')[1];
                                
                                // 使用loadFunctionPage函数加载内容
                                loadFunctionPage(pageName);
                                
                                // 更新页面标题
                                const tabTitle = targetTab.querySelector('.tab-title').textContent;
                                document.title = tabTitle + ' - 项目管理系统';
                                
                                // 更新浏览器地址栏，但不刷新页面
                                history.pushState(null, tabTitle, targetUrl);
                            } else if (targetUrl === window.location.href) {
                                // 如果是当前URL，不执行任何操作
                                return;
                            } else if (targetUrl.includes('main.php')) {
                                // 对于工作台页面，使用AJAX加载内容
                                $.ajax({
                                    url: 'api/workspace_data.php',
                                    type: 'GET',
                                    success: function(data) {
                                        const mainContent = document.getElementById('mainContent');
                                        if (mainContent) {
                                            mainContent.innerHTML = data;
                                        }
                                        // 更新页面标题
                                        document.title = '个人工作台 - 项目管理系统';
                                        
                                        // 更新浏览器地址栏，但不刷新页面
                                        history.pushState(null, '个人工作台', targetUrl);
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('加载工作台内容失败:', error);
                                    }
                                });
                            } else {
                                // 对于其他页面（非#/开头的URL），使用传统跳转
                                window.location.href = targetUrl;
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('更新标签状态失败:', error);
                            // 出错时回退到传统跳转
                            window.location.href = targetUrl;
                        }
                    });
                }
            };
            
            // 加载功能页面的函数
            function loadFunctionPage(pageName) {
                // 显示加载中状态
                const mainContent = document.getElementById('mainContent');
                if (mainContent) {
                    mainContent.innerHTML = '<div class="loading-indicator"><i class="el-icon-loading"></i> 正在加载...</div>';
                    
                    // 根据页面名称确定要加载的实际页面路径
                    let pagePath = '';
                    
                    // 映射页面名称到实际文件路径
                    switch(pageName) {
                        // 合同管理模块
                        case 'contracts':
                            pagePath = 'app/views/contracts/contracts.php';
                            break;
                        case 'contract_details':
                            pagePath = 'app/views/contracts/contract_details.php';
                            break;
                        case 'contractServiceConfirmation':
                        case 'service_confirmation':
                            pagePath = 'app/views/contracts/service_confirmation.php';
                            break;
                        case 'tyTsMilestoneConfirmation':
                        case 'milestone_confirmation':
                            pagePath = 'app/views/contracts/milestone_confirmation.php';
                            break;
                        case 'contractConfirmationAcceptance':
                            pagePath = 'app/views/contracts/contract_acceptance.php';
                            break;
                        case 'initiateContractFines':
                            pagePath = 'app/views/contracts/contract_fines.php';
                            break;
                            
                        // 项目管理模块
                        case 'projectList':
                            pagePath = 'app/views/projects/project_list.php';
                            break;
                        case 'projectSchedule':
                            pagePath = 'app/views/projects/project_schedule.php';
                            break;
                        case 'projectResource':
                            pagePath = 'app/views/projects/project_resource.php';
                            break;
                            
                        // 生产指令模块
                        case 'productionOrders':
                            pagePath = 'app/views/production/production_orders.php';
                            break;
                        case 'productionSchedule':
                            pagePath = 'app/views/production/production_schedule.php';
                            break;
                            
                        // 采购管理模块
                        case 'purchaseOrderConfirmation':
                            pagePath = 'app/views/purchase/purchase_orders.php';
                            break;
                        case 'materialInventoryManagement':
                            pagePath = 'app/views/purchase/inventory_management.php';
                            break;
                        case 'materialWarehouseApproval':
                            pagePath = 'app/views/purchase/warehouse_approval.php';
                            break;
                            
                        // 物流发货模块
                        case 'noGoldwindDelivery':
                            pagePath = 'app/views/logistics/order_packing.php';
                            break;
                        case 'packingListQuery':
                            pagePath = 'app/views/logistics/packing_list_query.php';
                            break;
                        case 'deliveryManage/supplierDeliveryMsg':
                            pagePath = 'app/views/logistics/supplier_delivery.php';
                            break;
                            
                        // 财务管理模块
                        case 'receiptManagement':
                            pagePath = 'app/views/finance/receipt_management.php';
                            break;
                        case 'paymentManagement':
                            pagePath = 'app/views/finance/payment_management.php';
                            break;
                        case 'invoiceManagement':
                            pagePath = 'app/views/finance/invoice_management.php';
                            break;
                        case 'contractPaymentPlans':
                            pagePath = 'app/views/finance/contract_payment_plans.php';
                            break;
                            
                        // 系统管理模块
                        case 'userManagement':
                            pagePath = 'app/views/system/user_management.php';
                            break;
                        case 'permissionConfiguration':
                            pagePath = 'app/views/system/permission_configuration.php';
                            break;
                        case 'systemSettings':
                            pagePath = 'app/views/system/system_settings.php';
                            break;
                            
                        default:
                            // 如果没有匹配的页面，显示错误信息
                            mainContent.innerHTML = '<div class="error-message"><i class="el-icon-warning"></i> 页面不存在或正在开发中</div>';
                            return;
                    }
                    
                    // 使用AJAX加载页面内容
                    $.ajax({
                        url: pagePath,
                        method: 'GET',
                        success: function(data) {
                            mainContent.innerHTML = data;
                        },
                        error: function(xhr, status, error) {
                            console.error('页面加载失败:', error);
                            mainContent.innerHTML = `<div class="error-message"><i class="el-icon-warning"></i> 页面加载失败: ${error}</div>`;
                        }
                    });
                }
            }
            
            // 关闭标签函数
            window.closeTab = function(tabId, event) {
                // 阻止事件冒泡
                if (event) {
                    event.stopPropagation();
                }
                
                // 检查是否为个人工作台标签
                const tabs = document.querySelectorAll('.tab');
                let tabIndex = -1;
                let isActiveTab = false;
                let isWorkspaceTab = false;
                
                // 找到要关闭的标签索引和活动状态
                tabs.forEach((tab, index) => {
                    if (tab.getAttribute('data-tab-id') === tabId) {
                        tabIndex = index;
                        isActiveTab = tab.classList.contains('active');
                        const tabTitle = tab.querySelector('.tab-title').textContent;
                        isWorkspaceTab = tabTitle.includes('个人工作台');
                    }
                });
                
                // 如果是个人工作台标签，不允许关闭
                if (isWorkspaceTab) {
                    console.log('个人工作台标签不能关闭');
                    return;
                }
                
                if (tabIndex !== -1) {
                    // 发送AJAX请求关闭标签
                    $.ajax({
                        url: 'api/close_tab.php',
                        type: 'POST',
                        data: {
                            tab_id: tabId,
                            csrf_token: getCsrfToken()
                        },
                        success: function(response) {
                            if (response.error) {
                                console.error(response.error);
                                return;
                            }
                            
                            // 更新标签栏显示
                            const tabContainer = document.querySelector('.tabs');
                            if (tabContainer) {
                                const tabToRemove = document.querySelector(`.tab[data-tab-id="${tabId}"]`);
                                if (tabToRemove) {
                                    tabToRemove.remove();
                                }
                            }
                            
                            // 检查是否还有其他非工作台标签
                            const remainingTabs = document.querySelectorAll('.tab');
                            let hasNonWorkspaceTabs = false;
                            let workspaceTabId = null;
                            
                            remainingTabs.forEach(tab => {
                                const tabTitle = tab.querySelector('.tab-title').textContent;
                                if (!tabTitle.includes('个人工作台')) {
                                    hasNonWorkspaceTabs = true;
                                } else {
                                    workspaceTabId = tab.getAttribute('data-tab-id');
                                }
                            });
                            
                            if (response.redirect) {
                                // 如果有重定向URL，则跳转到该URL
                                window.location.href = response.redirect;
                            } else if (!hasNonWorkspaceTabs && workspaceTabId) {
                                // 如果没有其他非工作台标签，激活工作台标签
                                switchTab(workspaceTabId);
                            } else {
                                // 否则，使用AJAX重新加载当前页面内容
                                const currentUrl = window.location.href;
                                if (currentUrl.includes('#/')) {
                                    const pageName = currentUrl.split('#/')[1];
                                    loadFunctionPage(pageName);
                                } else if (currentUrl.includes('main.php')) {
                                    $.ajax({
                                        url: 'api/workspace_data.php',
                                        type: 'GET',
                                        success: function(data) {
                                            const mainContent = document.getElementById('mainContent');
                                            if (mainContent) {
                                                mainContent.innerHTML = data;
                                            }
                                        }
                                    });
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('关闭标签失败:', error);
                            alert('关闭标签失败，请重试');
                        }
                    });
                }
            };
            
            // 检查标签数量并显示警告
            function checkTabsLimit() {
                const tabs = document.querySelectorAll('.tab');
                const tabWarning = document.getElementById('tabWarning');
                
                if (tabs.length > 8) { // 设置最大标签数量为8
                    tabWarning.classList.add('show');
                } else {
                    tabWarning.classList.remove('show');
                }
            }
            
            // 页面加载完成后检查标签数量
            checkTabsLimit();
        });
    </script>
</body>
</html>