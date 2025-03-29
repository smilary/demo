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
                                <div class="tab <?php echo $tab['active'] ? 'active' : ''; ?>" data-tab-id="<?php echo $tab['id']; ?>" data-tab-url="<?php echo htmlspecialchars($tab['url']); ?>" onclick="switchTab('<?php echo $tab['id']; ?>')">
                                    <span class="tab-title"><?php echo htmlspecialchars($tab['title']); ?></span>
                                    <span class="tab-close" onclick="closeTab('<?php echo $tab['id']; ?>')">×</span>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 添加CSRF Token到AJAX请求头
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '<?= $_SESSION["csrf_token"] ?>'
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
                
                // 遍历所有标签，找到目标标签的URL
                tabs.forEach(tab => {
                    if (tab.getAttribute('data-tab-id') === tabId) {
                        targetUrl = tab.getAttribute('data-tab-url');
                    }
                });
                
                // 如果找到了目标URL，则跳转
                if (targetUrl) {
                    window.location.href = targetUrl;
                }
            };
            
            // 关闭标签函数
            window.closeTab = function(tabId) {
                // 阻止事件冒泡
                event.stopPropagation();
                
                // 获取所有标签
                const tabs = document.querySelectorAll('.tab');
                let tabIndex = -1;
                let isActiveTab = false;
                let tabsArray = [];
                
                // 构建标签数组并找到要关闭的标签索引
                tabs.forEach((tab, index) => {
                    const id = tab.getAttribute('data-tab-id');
                    const url = tab.getAttribute('data-tab-url');
                    const title = tab.querySelector('.tab-title').textContent;
                    const active = tab.classList.contains('active');
                    
                    tabsArray.push({ id, url, title, active });
                    
                    if (id === tabId) {
                        tabIndex = index;
                        isActiveTab = active;
                    }
                });
                
                if (tabIndex !== -1) {
                    // 发送AJAX请求关闭标签
                    $.ajax({
                        url: 'api/close_tab.php',
                        type: 'POST',
                        data: { tab_id: tabId },
                        success: function(response) {
                            // 如果是当前激活的标签，则需要激活其他标签
                            if (isActiveTab && tabsArray.length > 1) {
                                // 优先激活左侧标签，如果没有则激活右侧标签
                                const newActiveIndex = tabIndex > 0 ? tabIndex - 1 : tabIndex + 1;
                                window.location.href = tabsArray[newActiveIndex].url;
                            } else if (!isActiveTab) {
                                // 如果关闭的不是当前标签，只需刷新页面更新标签栏
                                location.reload();
                            } else {
                                // 如果关闭的是最后一个标签，返回工作台
                                window.location.href = 'main.php';
                            }
                        },
                        error: function() {
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