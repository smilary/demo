<?php
session_start();
require_once __DIR__.'/../../../config/db_connect.php';

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// 生成CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = md5(uniqid(mt_rand(), true));
}

// 设置默认页面标题
$page_title = isset($page_title) ? $page_title : '项目管理系统';

// 设置默认激活菜单
$active_menu = isset($active_menu) ? $active_menu : '';

// 设置额外CSS文件
$extra_css = isset($extra_css) ? $extra_css : [];
// 添加标签样式
$extra_css[] = 'styles/tabs.css';

// 设置额外JS文件
$extra_js = isset($extra_js) ? $extra_js : [];

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

if (!$tab_exists) {
    $current_page['active'] = true;
    $_SESSION['tabs'][] = $current_page;
}

// 包含头部模板
include_once __DIR__.'/header.php';
?>

<!-- 标签栏 -->
<div class="tabs-container">
    <div class="tabs-wrapper">
        <div class="tabs">
            <?php foreach ($_SESSION['tabs'] as $tab): ?>
                <div class="tab <?php echo $tab['active'] ? 'active' : ''; ?>" data-tab-id="<?php echo $tab['id']; ?>" data-tab-url="<?php echo htmlspecialchars($tab['url']); ?>">
                    <span class="tab-title"><?php echo htmlspecialchars($tab['title']); ?></span>
                    <span class="tab-close" onclick="closeTab('<?php echo $tab['id']; ?>')">×</span>
                </div>
            <?php endforeach; ?>
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

<?php 
// 包含底部模板
include_once __DIR__.'/footer.php';
?>