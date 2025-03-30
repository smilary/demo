/**
 * 标签会话存储处理脚本
 * 用于管理标签在会话存储中的状态
 */

// 保存标签到会话存储
function saveTabToSession(title, url, tabId) {
    // 使用AJAX将标签信息保存到会话
    $.ajax({
        url: 'api/save_tab.php',
        type: 'POST',
        data: {
            tab_id: tabId,
            title: title,
            url: url,
            csrf_token: getCsrfToken()
        },
        success: function(response) {
            console.log('标签已保存到会话');
        },
        error: function(xhr, status, error) {
            console.error('保存标签失败:', error);
        }
    });
}

// 更新标签活动状态
function updateTabActiveStatus(tabId) {
    $.ajax({
        url: 'api/switch_tab.php',
        type: 'POST',
        data: {
            tab_id: tabId,
            csrf_token: getCsrfToken()
        },
        success: function(response) {
            console.log('标签状态已更新');
        },
        error: function(xhr, status, error) {
            console.error('更新标签状态失败:', error);
        }
    });
}

// 获取CSRF Token
function getCsrfToken() {
    // 尝试从meta标签获取
    const metaToken = document.querySelector('meta[name="csrf-token"]');
    if (metaToken) {
        return metaToken.getAttribute('content');
    }
    
    // 如果没有meta标签，尝试从隐藏字段获取
    const inputToken = document.querySelector('input[name="csrf_token"]');
    if (inputToken) {
        return inputToken.value;
    }
    
    return '';
}