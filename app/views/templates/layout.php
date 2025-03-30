<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : '项目管理系统'; ?></title>
    <link rel="stylesheet" href="styles/element-ui/index.css">
    <link rel="stylesheet" href="styles/workspace.css">
    <?php
    if (isset($extra_css) && is_array($extra_css)) {
        foreach ($extra_css as $css_file) {
            echo "<link rel=\"stylesheet\" href=\"" . $css_file . "\">\n";
        }
    }
    ?>
</head>
<body>
    <div class="app-container">
        <?php if (!isset($is_secondary_page)): ?>
        <header class="app-header">
            <div class="logo">
                <img src="img/logo1.png" alt="Logo">
            </div>
            <nav class="main-nav">
                <ul>
                    <li class="active fixed-workspace">
                        <a href="main.php?view=workspace">个人工作台</a>
                    </li>
                    <li class="<?php echo $active_menu === '合同管理' ? 'active' : ''; ?>">
                        <a href="main.php?view=contracts">合同管理</a>
                    </li>
                </ul>
            </nav>
        </header>
        <?php endif; ?>
        
        <main class="app-main">
            <?php
            if (defined('INCLUDED_IN_LAYOUT') && isset($content_view)) {
                include $content_view;
            }
            ?>
        </main>
    </div>

    <script src="js/menu_handler.js"></script>
    <script src="js/tab_session_handler.js"></script>
</body>
</html>