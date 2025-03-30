<?php
session_start();
require_once 'config/db_connect.php';
global $db;

$error = '';
$remember = false;

// 处理退出请求
if (isset($_GET['logout'])) {
    // 清除session
    session_unset();
    session_destroy();
    
    // 清除记住我cookie
    if (isset($_COOKIE['remember_me'])) {
        setcookie('remember_me', '', time() - 3600, '/');
    }
    
    // 重定向到登录页
    header('Location: index.php');
    exit;
}

// 检查是否已登录
if (isset($_SESSION['user_id'])) {
    header('Location: main.php');
    exit;
}

// 处理登录请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $remember = isset($_POST['remember']);

    if (empty($username) || empty($password)) {
        $error = '用户名和密码不能为空';
    } else {
        // 查询用户
        $stmt = $db->prepare("SELECT id, username, password, real_name FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user) {
            
            // 验证密码 (使用MD5加密)
            if (md5($password) === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['real_name'] = $user['real_name'];
                
                // 记住我功能
                if ($remember) {
                    $cookie_value = $user['id'] . ':' . md5($user['username'] . $user['password']);
                    setcookie('remember_me', $cookie_value, time() + 86400 * 30, '/');
                }
                
                header('Location: main.php');
                exit;
            } else {
                $error = '用户名或密码错误';
            }
        } else {
            $error = '用户名或密码错误';
        }
    }
}

// 检查记住我cookie
if (empty($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
    list($user_id, $token) = explode(':', $_COOKIE['remember_me']);
    
    $stmt = $db->prepare("SELECT id, username, password, real_name FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if ($user) {
        $expected_token = md5($user['username'] . $user['password']);
        
        if ($token === $expected_token) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['real_name'] = $user['real_name'];
            header('Location: main.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>项目管理系统 - 登录</title>
    <!-- 引入Element UI样式，与dashboard.php保持一致 -->
    <link rel="stylesheet" href="styles/element-ui/index.css">
    <link href="styles/dashboard.css" rel="stylesheet">
    <link href="styles/login.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="logo">
                <img src="img/logo.png" alt="logo">
                <h3 class="system-title">项目管理系统</h3>
            </div>
            
            <?php if ($error): ?>
            <div class="el-alert el-alert--error" style="margin-bottom: 20px;">
                <div class="el-alert__content">
                    <p class="el-alert__description"><?php echo htmlspecialchars($error); ?></p>
                </div>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="" class="login-form">
                <div class="form-item">
                    <label for="username" class="form-label">用户名</label>
<input type="text" class="form-input" id="username" name="username" placeholder="请输入用户名" required autocomplete="username">
                </div>
                <div class="form-item">
                    <label for="password" class="form-label">密码</label>
<input type="password" class="form-input" id="password" name="password" placeholder="请输入密码" required autocomplete="current-password">
                </div>
                <div class="form-actions">
                    <div class="form-checkbox">
                        <input type="checkbox" id="remember" name="remember" <?php echo $remember ? 'checked' : ''; ?>>
                        <label for="remember">记住我</label>
                    </div>
                    <button type="submit" class="login-button">登 录</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
