<?php
/**
 * 数据库连接配置
 */
$db_host = 'localhost'; // 数据库服务器
$db_name = 'erp_db'; // 数据库名
$db_user = 'root';      // 数据库用户名
$db_pass = 'hf731024';          // 数据库密码

try {
    $db = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("数据库连接失败: " . $e->getMessage());
}

/**
 * 安全过滤函数
 */
function safe_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
?>
