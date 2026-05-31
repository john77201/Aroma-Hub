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
                    <a href="#" class="text-primary font-medium">Dashboard</a>
                    <a href="#" class="text-gray-600 hover:text-primary transition-colors">Products</a>
                    <a href="#" class="text-gray-600 hover:text-primary transition-colors">Orders</a>
                    <a href="#" class="text-gray-600 hover:text-primary transition-colors">Customers</a>
                    <a href="#" class="text-gray-600 hover:text-primary transition-colors">Analytics</a>
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
   

    <!-- Welcome Section -->
   
 </header>