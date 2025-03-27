<?php
// 页脚模板文件，包含共享的JavaScript代码
?>

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
                
                // 处理链接导航
                if (href && href.startsWith('#/')) {
                    const route = href.substring(2); // 移除 '#/' 前缀
                    
                    // 根据路由加载相应的内容
                    loadPageContent(route);
                }
            });
        });
        
        // 加载页面内容的函数
        function loadPageContent(route) {
            // 默认页面路径前缀
            const basePath = 'app/views/';
            let pagePath = '';
            
            // 根据路由确定页面路径
            switch(route) {
                // 合同管理
                case 'contractServiceConfirmation':
                    pagePath = basePath + 'contracts/service_confirmation.php';
                    break;
                case 'tyTsMilestoneConfirmation':
                    pagePath = basePath + 'contracts/milestone_confirmation.php';
                    break;
                case 'contractConfirmationAcceptance':
                    pagePath = basePath + 'contracts/contract_acceptance.php';
                    break;
                case 'initiateContractFines':
                    pagePath = basePath + 'contracts/contract_fines.php';
                    break;
                    
                // 项目管理
                case 'projectList':
                    pagePath = basePath + 'projects/project_list.php';
                    break;
                case 'projectSchedule':
                    pagePath = basePath + 'projects/project_schedule.php';
                    break;
                case 'projectResource':
                    pagePath = basePath + 'projects/project_resource.php';
                    break;
                    
                // 生产指令
                case 'productionOrders':
                    pagePath = basePath + 'production/production_orders.php';
                    break;
                case 'productionSchedule':
                    pagePath = basePath + 'production/production_schedule.php';
                    break;
                    
                // 采购管理
                case 'purchaseOrderConfirmation':
                    pagePath = basePath + 'purchase/purchase_order.php';
                    break;
                case 'materialInventoryManagement':
                    pagePath = basePath + 'purchase/inventory_management.php';
                    break;
                case 'materialWarehouseApproval':
                    pagePath = basePath + 'purchase/warehouse_approval.php';
                    break;
                    
                // 物流发货
                case 'noGoldwindDelivery':
                    pagePath = basePath + 'logistics/order_packing.php';
                    break;
                case 'packingListQuery':
                    pagePath = basePath + 'logistics/packing_list_query.php';
                    break;
                case 'deliveryManage/supplierDeliveryMsg':
                    pagePath = basePath + 'logistics/supplier_delivery.php';
                    break;
                    
                // 财务管理
                case 'receiptManagement':
                    pagePath = basePath + 'finance/receipt_management.php';
                    break;
                case 'paymentManagement':
                    pagePath = basePath + 'finance/payment_management.php';
                    break;
                case 'invoiceManagement':
                    pagePath = basePath + 'finance/invoice_management.php';
                    break;
                case 'contractPaymentPlans':
                    pagePath = basePath + 'finance/contract_payment_plans.php';
                    break;
                    
                // 系统管理
                case 'userManagement':
                    pagePath = basePath + 'system/user_management.php';
                    break;
                case 'permissionConfiguration':
                    pagePath = basePath + 'system/permission_configuration.php';
                    break;
                case 'systemSettings':
                    pagePath = basePath + 'system/system_settings.php';
                    break;
                    
                default:
                    // 默认显示工作台
                    pagePath = 'workspace.php';
            }
            
            // 使用AJAX加载页面内容
            $.ajax({
                url: pagePath,
                method: 'GET',
                success: function(data) {
                    // 将加载的内容插入到主内容区域
                    $('#mainContent').html(data);
                    // 更新浏览器历史记录，便于使用浏览器的前进/后退按钮
                    history.pushState(null, null, '#/' + route);
                },
                error: function(xhr, status, error) {
                    console.error('页面加载失败:', error);
                    // 显示错误信息
                    $('#mainContent').html('<div class="error-message">页面加载失败，请稍后再试。</div>');
                }
            });
        }
        
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
                font-weight: bold;
                margin-bottom: 5px;
            }
        `;
        document.head.appendChild(style);
        
        <?php if (isset($custom_scripts)): ?>
        // 自定义页面脚本
        <?= $custom_scripts ?>
        <?php endif; ?>
    });
</script>
</body>
</html>