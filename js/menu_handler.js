/**
 * 菜单处理和页面加载脚本
 * 用于处理菜单点击事件和加载对应的功能页面
 */

document.addEventListener('DOMContentLoaded', function() {
    // 为所有二级菜单项添加点击事件
    const menuItems = document.querySelectorAll('.submenu-container .el-menu-item');
    
    menuItems.forEach(item => {
        item.addEventListener('click', function(e) {
            // 阻止默认的链接跳转行为
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
            
            // 获取链接地址
            const link = this.parentElement.getAttribute('href');
            
            // 如果链接以#/开头，表示是内部功能页面
            if (link && link.startsWith('#/')) {
                const pageName = link.substring(2); // 去掉#/前缀
                loadFunctionPage(pageName);
                
                // 更新页面标题，用于标签显示
                const menuTitle = this.querySelector('span').textContent;
                document.title = menuTitle + ' - 项目管理系统';
                
                // 触发标签更新事件
                const tabUpdateEvent = new CustomEvent('tabUpdate', {
                    detail: {
                        title: menuTitle,
                        url: window.location.href
                    }
                });
                document.dispatchEvent(tabUpdateEvent);
                
                // 确保标签显示
                setTimeout(function() {
                    // 检查标签是否已创建
                    const tabsContainer = document.querySelector('.tabs-container');
                    if (tabsContainer) {
                        // 如果标签容器存在但没有标签，手动创建一个
                        const tabs = document.querySelector('.tabs');
                        if (tabs && tabs.children.length === 0) {
                            const tabId = 'tab_' + new Date().getTime();
                            const newTab = document.createElement('div');
                            newTab.className = 'tab active';
                            newTab.setAttribute('data-tab-id', tabId);
                            newTab.setAttribute('data-tab-url', window.location.href);
                            
                            const tabTitle = document.createElement('span');
                            tabTitle.className = 'tab-title';
                            tabTitle.textContent = menuTitle;
                            
                            const tabClose = document.createElement('span');
                            tabClose.className = 'tab-close';
                            tabClose.textContent = '×';
                            tabClose.onclick = function() { closeTab(tabId); };
                            
                            newTab.appendChild(tabTitle);
                            newTab.appendChild(tabClose);
                            tabs.appendChild(newTab);
                        }
                    }
                }, 500);
            }
        });
    });
    
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
                case 'contractServiceConfirmation':
                    pagePath = 'app/views/contracts/service_confirmation.php';
                    break;
                case 'tyTsMilestoneConfirmation':
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
    
    // 添加CSS样式
    const style = document.createElement('style');
    style.textContent = `
        #mainContent {
            min-height: 500px;
            background-color: #fff;
            border-radius: 4px;
            box-shadow: 0 2px 12px 0 rgba(0,0,0,.1);
            padding: 20px;
        }
        
        .loading-indicator {
            text-align: center;
            padding: 40px;
            font-size: 16px;
            color: #909399;
        }
        
        .error-message {
            text-align: center;
            padding: 40px;
            font-size: 16px;
            color: #F56C6C;
        }
    `;
    document.head.appendChild(style);
});