<?php
// 个人工作台视图
?>

<div class="workspace-container">
    <!-- 个人信息卡片 -->
    <div class="card user-profile-card">
        <div class="card-header">个人信息</div>
        <div class="user-profile">
            <div class="user-avatar">
                <i class="el-icon-user-solid"></i>
            </div>
            <div class="user-info">
                <div class="user-name"><?php echo isset($_SESSION['real_name']) ? htmlspecialchars($_SESSION['real_name']) : '用户'; ?></div>
                <div class="user-role"><?php echo isset($_SESSION['role']) ? htmlspecialchars($_SESSION['role']) : '员工'; ?></div>
            </div>
        </div>
        <div class="user-stats">
            <div class="stat-item">
                <span>待办任务</span>
                <span class="stat-value">5</span>
            </div>
            <div class="stat-item">
                <span>进行中项目</span>
                <span class="stat-value">3</span>
            </div>
            <div class="stat-item">
                <span>本月完成</span>
                <span class="stat-value">7</span>
            </div>
        </div>
    </div>

    <!-- 工作台主区域 -->
    <div class="workspace-main">
        <!-- 待办任务 -->
        <div class="card todo-card">
            <div class="card-header">
                <span>待办任务</span>
                <a href="#" class="more-link">查看全部</a>
            </div>
            <div class="todo-list">
                <div class="todo-item">
                    <div class="todo-checkbox">
                        <input type="checkbox" id="todo1">
                        <label for="todo1"></label>
                    </div>
                    <div class="todo-content">
                        <div class="todo-title">完成项目A需求分析</div>
                        <div class="todo-due">截止日期: 2023-12-15</div>
                    </div>
                    <div class="todo-priority high">高</div>
                </div>
                <div class="todo-item">
                    <div class="todo-checkbox">
                        <input type="checkbox" id="todo2">
                        <label for="todo2"></label>
                    </div>
                    <div class="todo-content">
                        <div class="todo-title">审核项目B开发文档</div>
                        <div class="todo-due">截止日期: 2023-12-18</div>
                    </div>
                    <div class="todo-priority medium">中</div>
                </div>
                <div class="todo-item">
                    <div class="todo-checkbox">
                        <input type="checkbox" id="todo3">
                        <label for="todo3"></label>
                    </div>
                    <div class="todo-content">
                        <div class="todo-title">参加项目C周会</div>
                        <div class="todo-due">截止日期: 2023-12-20</div>
                    </div>
                    <div class="todo-priority low">低</div>
                </div>
            </div>
        </div>

        <!-- 我的项目 -->
        <div class="card projects-card">
            <div class="card-header">
                <span>我的项目</span>
                <a href="#" class="more-link">查看全部</a>
            </div>
            <div class="projects-list">
                <div class="project-item">
                    <div class="project-info">
                        <div class="project-title">项目A</div>
                        <div class="project-desc">系统需求分析与设计</div>
                    </div>
                    <div class="project-progress">
                        <div class="progress-text">65%</div>
                        <div class="progress-bar">
                            <div class="progress" style="width: 65%"></div>
                        </div>
                    </div>
                </div>
                <div class="project-item">
                    <div class="project-info">
                        <div class="project-title">项目B</div>
                        <div class="project-desc">前端界面开发</div>
                    </div>
                    <div class="project-progress">
                        <div class="progress-text">30%</div>
                        <div class="progress-bar">
                            <div class="progress" style="width: 30%"></div>
                        </div>
                    </div>
                </div>
                <div class="project-item">
                    <div class="project-info">
                        <div class="project-title">项目C</div>
                        <div class="project-desc">系统测试与部署</div>
                    </div>
                    <div class="project-progress">
                        <div class="progress-text">90%</div>
                        <div class="progress-bar">
                            <div class="progress" style="width: 90%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 右侧边栏 -->
    <div class="workspace-sidebar">
        <!-- 通知中心 -->
        <div class="card notifications-card">
            <div class="card-header">通知中心</div>
            <div class="notifications-list">
                <div class="notification-item unread">
                    <div class="notification-icon system"><i class="el-icon-message"></i></div>
                    <div class="notification-content">
                        <div class="notification-title">系统通知</div>
                        <div class="notification-text">您有一个新的审批任务</div>
                        <div class="notification-time">10分钟前</div>
                    </div>
                </div>
                <div class="notification-item">
                    <div class="notification-icon task"><i class="el-icon-s-order"></i></div>
                    <div class="notification-content">
                        <div class="notification-title">任务提醒</div>
                        <div class="notification-text">项目A需求分析即将到期</div>
                        <div class="notification-time">1小时前</div>
                    </div>
                </div>
                <div class="notification-item">
                    <div class="notification-icon message"><i class="el-icon-chat-dot-round"></i></div>
                    <div class="notification-content">
                        <div class="notification-title">消息通知</div>
                        <div class="notification-text">张经理给您发送了一条消息</div>
                        <div class="notification-time">昨天</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 快捷操作 -->
        <div class="card shortcuts-card">
            <div class="card-header">快捷操作</div>
            <div class="shortcuts-list">
                <a href="#" class="shortcut-item">
                    <i class="el-icon-document-add"></i>
                    <span>新建任务</span>
                </a>
                <a href="#" class="shortcut-item">
                    <i class="el-icon-s-claim"></i>
                    <span>发起审批</span>
                </a>
                <a href="#" class="shortcut-item">
                    <i class="el-icon-s-cooperation"></i>
                    <span>会议预约</span>
                </a>
                <a href="#" class="shortcut-item">
                    <i class="el-icon-s-finance"></i>
                    <span>费用报销</span>
                </a>
            </div>
        </div>
    </div>
</div>