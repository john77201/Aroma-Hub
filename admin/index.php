<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Aroma Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#f97316',
                        secondary: '#dc2626',
                    }
                }
            }
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #f97316 0%, #dc2626 50%, #b91c1c 100%);
        }
        .admin-card {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }
        .admin-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .stats-card {
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.1) 0%, rgba(220, 38, 38, 0.1) 100%);
            border: 1px solid rgba(249, 115, 22, 0.2);
        }
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transform: translateX(100%);
            transition: transform 0.3s ease;
            z-index: 1000;
        }
        .toast.show {
            transform: translateX(0);
        }
        .toast.info {
            background: #3b82f6;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Toast Container -->
    <div id="toast-container"></div>

    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 hero-gradient rounded-xl flex items-center justify-center">
                            <i class="fas fa-seedling text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">Aroma Hub</h1>
                            <p class="text-xs text-gray-500">Admin Dashboard</p>
                        </div>
                    </div>
                </div>

                <!-- Admin Navigation -->
                <div class="hidden md:flex space-x-8">
                    <a href="admin-dashboard.html" class="text-primary font-medium">Dashboard</a>
                    <a href="#products" class="text-gray-600 hover:text-primary transition-colors">Products</a>
                    <a href="order-management.html" class="text-gray-600 hover:text-primary transition-colors">Orders</a>
                    <a href="#customers" class="text-gray-600 hover:text-primary transition-colors">Customers</a>
                    <a href="#analytics" class="text-gray-600 hover:text-primary transition-colors">Analytics</a>
                </div>

                <!-- Admin Actions -->
                <div class="flex items-center space-x-4">
                    <button class="relative text-gray-600 hover:text-primary transition-colors">
                        <i class="fas fa-bell text-lg"></i>
                        <span class="absolute -top-2 -right-2 bg-secondary text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">3</span>
                    </button>
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 hero-gradient rounded-full flex items-center justify-center">
                            <i class="fas fa-user-shield text-white text-sm"></i>
                        </div>
                        <div class="hidden sm:block">
                            <p class="font-medium text-gray-900">Admin User</p>
                            <p class="text-xs text-gray-500">Administrator</p>
                        </div>
                    </div>
                    <button class="text-gray-600 hover:text-primary transition-colors">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Welcome Section -->
    <section class="hero-gradient text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold mb-2">Welcome Back, Admin!</h1>
                    <p class="text-white/90 mb-4 md:mb-0">Manage your spice business with ease</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Admin Functions -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        <div class="mb-8 mt-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Admin Functions</h2>
            <p class="text-gray-600">Manage your store inventory, content, and operations</p>
        </div>

        <!-- Product Management -->
        <div class="mb-12">
            <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-box text-primary mr-3"></i>
                Product Management
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Add Product -->
                <a href="add_product.php">
                    <div class="admin-card bg-white rounded-2xl p-6 cursor-pointer hover:shadow-lg transition-all">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-gradient-to-r from-primary to-secondary rounded-2xl flex items-center justify-center mb-4">
                                <i class="fas fa-plus text-white text-xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Add Product</h4>
                            <p class="text-sm text-gray-600">Add new spices and seasonings to your inventory</p>
                        </div>
                    </div>
                </a>

                <!-- View Products -->
                <a href="view_products.php">
                    <div class="admin-card bg-white rounded-2xl p-6 cursor-pointer">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-4">
                                <i class="fas fa-list text-white text-xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">View Products</h4>
                            <p class="text-sm text-gray-600">Manage and edit your existing product catalog</p>
                        </div>
                    </div>
                </a>

                <!-- Add Bulk Products -->
                <a href="add_bulk.php">
                    <div class="admin-card bg-white rounded-2xl p-6 cursor-pointer">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-4">
                                <i class="fas fa-upload text-white text-xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Add Bulk Products</h4>
                            <p class="text-sm text-gray-600">Import multiple products via CSV or Excel</p>
                        </div>
                    </div>
                </a>

                <!-- View Bulk Products -->
                <a href="view_bulk.php">
                    <div class="admin-card bg-white rounded-2xl p-6 cursor-pointer">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-4">
                                <i class="fas fa-database text-white text-xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">View Bulk Products</h4>
                            <p class="text-sm text-gray-600">Review and manage bulk imported products</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Gift Box Management -->
        <div class="mb-12">
            <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-gift text-primary mr-3"></i>
                Gift Box Management
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Add Gift Box -->
                <a href="add_giftbox.php">
                    <div class="admin-card bg-white rounded-2xl p-6 cursor-pointer">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-gradient-to-r from-pink-500 to-rose-500 rounded-2xl flex items-center justify-center">
                                <i class="fas fa-plus text-white text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 mb-2">Add Gift Box</h4>
                                <p class="text-sm text-gray-600">Create curated spice gift boxes for special occasions</p>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- View Gift Boxes -->
                <a href="view_giftbox.php">
                    <div class="admin-card bg-white rounded-2xl p-6 cursor-pointer">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-2xl flex items-center justify-center">
                                <i class="fas fa-gifts text-white text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 mb-2">View Gift Boxes</h4>
                                <p class="text-sm text-gray-600">Manage existing gift box collections and bundles</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Premium Spices for Custom Gift Boxes -->
        <div class="mb-12">
            <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-pepper-hot text-primary mr-3"></i>
                Premium Spices (Custom Gift Boxes)
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Add Premium Spice -->
                <a href="add-premium-spice.html">
                    <div class="admin-card bg-white rounded-2xl p-6 cursor-pointer">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-gradient-to-r from-orange-500 to-red-500 rounded-2xl flex items-center justify-center">
                                <i class="fas fa-plus text-white text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 mb-2">Add Premium Spice</h4>
                                <p class="text-sm text-gray-600">Add spices for customizable gift boxes (price per gram)</p>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- View Premium Spices -->
                <a href="view-premium-spices.html">
                    <div class="admin-card bg-white rounded-2xl p-6 cursor-pointer">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-gradient-to-r from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center">
                                <i class="fas fa-list-ul text-white text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 mb-2">View Premium Spices</h4>
                                <p class="text-sm text-gray-600">Manage premium spices available for custom gift boxes</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Order Management -->
        <div class="mb-12">
            <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-shopping-cart text-primary mr-3"></i>
                Order Management
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- New Orders -->
                <a href="neworders.php">
                    <div class="admin-card bg-white rounded-2xl p-6 cursor-pointer hover:shadow-lg transition-all">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-4">
                                <i class="fas fa-clock text-white text-xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">New Orders</h4>
                            <p class="text-sm text-gray-600">Manage recently placed orders awaiting processing</p>
                            <div class="mt-3">
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-medium">8 New</span>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Processed Orders -->
                <a href="processedorders.php">
                    <div class="admin-card bg-white rounded-2xl p-6 cursor-pointer hover:shadow-lg transition-all">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-gradient-to-r from-amber-500 to-orange-500 rounded-2xl flex items-center justify-center mb-4">
                                <i class="fas fa-cogs text-white text-xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Processed Orders</h4>
                            <p class="text-sm text-gray-600">Orders that are being prepared for shipment</p>
                            <div class="mt-3">
                                <span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-xs font-medium">12 Processing</span>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Shipped Orders -->
                <a href="shipped.php">
                    <div class="admin-card bg-white rounded-2xl p-6 cursor-pointer hover:shadow-lg transition-all">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-4">
                                <i class="fas fa-shipping-fast text-white text-xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Shipped Orders</h4>
                            <p class="text-sm text-gray-600">Orders that are currently in transit to customers</p>
                            <div class="mt-3">
                                <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-medium">15 Shipped</span>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Completed Orders -->
                <a href="completed.php">
                    <div class="admin-card bg-white rounded-2xl p-6 cursor-pointer hover:shadow-lg transition-all">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-4">
                                <i class="fas fa-check-circle text-white text-xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Completed Orders</h4>
                            <p class="text-sm text-gray-600">Successfully delivered orders and customer feedback</p>
                            <div class="mt-3">
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium">142 Completed</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Blog Management -->
        <div class="mb-12">
            <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-blog text-primary mr-3"></i>
                Blog Management
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Add Blog -->
                <a href="add_blogs.php">
                    <div class="admin-card bg-white rounded-2xl p-6 cursor-pointer">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center">
                                <i class="fas fa-pen text-white text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 mb-2">Add Blog Post</h4>
                                <p class="text-sm text-gray-600">Create engaging content about spices, recipes, and health tips</p>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- View Blogs -->
                <a href="view_blogs.php">
                    <div class="admin-card bg-white rounded-2xl p-6 cursor-pointer">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-gradient-to-r from-teal-500 to-cyan-600 rounded-2xl flex items-center justify-center">
                                <i class="fas fa-newspaper text-white text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 mb-2">View Blog Posts</h4>
                                <p class="text-sm text-gray-600">Edit, publish, or manage your blog content library</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Quick Actions -->
       
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div class="md:col-span-2">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 hero-gradient rounded-xl flex items-center justify-center">
                            <i class="fas fa-seedling text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">Aroma Hub</h3>
                            <p class="text-sm text-gray-400">Admin Dashboard</p>
                        </div>
                    </div>
                    <p class="text-gray-400 mb-6 max-w-md">
                        Manage your premium spice business with our comprehensive admin dashboard. 
                        Monitor sales, manage inventory, and grow your customer base.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="admin-dashboard.html" class="text-gray-400 hover:text-white transition-colors">Dashboard</a></li>
                        <li><a href="view_products.php" class="text-gray-400 hover:text-white transition-colors">Products</a></li>
                        <li><a href="new-orders.html" class="text-gray-400 hover:text-white transition-colors">New Orders</a></li>
                        <li><a href="view-premium-spices.html" class="text-gray-400 hover:text-white transition-colors">Premium Spices</a></li>
                        <li><a href="#analytics" class="text-gray-400 hover:text-white transition-colors">Analytics</a></li>
                    </ul>
                </div>

                <!-- Support -->
                <div>
                    <h4 class="font-semibold mb-4">Support</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Help Center</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Documentation</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">API Reference</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Contact Support</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">System Status</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2024 Aroma Hub Admin Dashboard. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Toast notification system
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-info-circle'} mr-2"></i>
                    <span>${message}</span>
                </div>
            `;
            
            container.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.add('show');
            }, 100);
            
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    container.removeChild(toast);
                }, 300);
            }, 3000);
        }

       
    </script>
</body>
</html>