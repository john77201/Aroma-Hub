<?php
// =================================================================
// 🔥 FIX 1: Start the session at the very top of the script
session_start(); 
// =================================================================
//include "header.php";
include "../connection.php";

if (isset($_POST['login'])) {
    // FIX 2: PHP now reads 'adminusername' and 'password' from the form's name attributes
    $username = $_POST['adminusername']; 
    $password = $_POST['password'];

    // ⚠️ SECURITY NOTE: The current method is vulnerable to SQL injection. 
    // This fix prioritizes functionality, but should be updated with prepared statements.
    $sql = "SELECT * FROM admin WHERE adminusername = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);

    if ($row > 0) 
    {
        // 🔥 FIX 3: Register the session variable before redirecting
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $username;
        
        // Use PHP header redirect for better security and flow
        header('Location: index.php');
        exit(); // Crucial to stop script execution after redirect
    } else {
        echo "<script>alert('Invalid Username or Password.');</script>"; 
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aroma Hub - Admin Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
            height: 100vh;
        }

        .container {
            display: flex;
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }

        /* Animated Background */
        .animated-bg {
            position: absolute;
            inset: 0;
            background: linear-gradient(45deg, #f97316, #ef4444, #8b5cf6);
            background-size: 400% 400%;
            animation: gradientShift 8s ease-in-out infinite;
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        /* Left Side */
        .left-side {
            display: none;
            width: 50%;
            position: relative;
            z-index: 1;
        }

        @media (min-width: 1024px) {
            .left-side {
                display: flex;
            }
        }

        .overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 10;
        }

        .spice-image {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .floating-particles {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: 11;
        }

        .particle {
            position: absolute;
            width: 8px;
            height: 8px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            animation: float 4s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) translateX(0px); opacity: 0; }
            50% { transform: translateY(-20px) translateX(10px); opacity: 1; }
        }

        .sparkle {
            position: absolute;
            color: rgba(255, 193, 7, 0.3);
            animation: sparkle 4s ease-in-out infinite;
        }

        @keyframes sparkle {
            0%, 100% { transform: rotate(0deg) scale(0.8); }
            25% { transform: rotate(5deg) scale(1); }
            75% { transform: rotate(-5deg) scale(0.8); }
        }

        .brand-content {
            position: relative;
            z-index: 20;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            color: white;
            height: 100vh;
            text-align: center;
            animation: slideInLeft 0.8s ease-out 0.2s both;
        }

        @keyframes slideInLeft {
            from { transform: translateX(-100px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .brand-header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            animation: fadeInUp 0.8s ease-out 0.5s both;
        }

        .chef-hat {
            width: 48px;
            height: 48px;
            margin-right: 12px;
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .brand-title {
            font-size: 2.5rem;
            font-weight: bold;
            background: linear-gradient(to right, #ffffff, #fed7aa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-subtitle {
            font-size: 1.25rem;
            margin-bottom: 1rem;
            animation: fadeInUp 0.8s ease-out 0.9s both;
        }

        .brand-description {
            font-size: 1.125rem;
            opacity: 0.9;
            max-width: 400px;
            animation: fadeInUp 0.8s ease-out 1.1s both;
        }

        @keyframes fadeInUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Right Side */
        .right-side {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            z-index: 1;
            animation: slideInRight 0.8s ease-out 0.4s both;
        }

        @media (min-width: 1024px) {
            .right-side {
                width: 50%;
            }
        }

        @keyframes slideInRight {
            from { transform: translateX(100px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .form-bg {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 237, 213, 0.8), rgba(255, 251, 235, 0.8));
            backdrop-filter: blur(10px);
            animation: formBgShift 6s ease-in-out infinite;
        }

        @keyframes formBgShift {
            0%, 100% { background: linear-gradient(135deg, rgba(255, 237, 213, 0.8), rgba(255, 251, 235, 0.8)); }
            50% { background: linear-gradient(135deg, rgba(254, 243, 199, 0.8), rgba(255, 237, 213, 0.8)); }
        }

        .form-container {
            width: 100%;
            max-width: 400px;
            position: relative;
            z-index: 10;
        }

        .mobile-brand {
            text-align: center;
            margin-bottom: 2rem;
            animation: slideInDown 0.6s ease-out 0.6s both;
        }

        @media (min-width: 1024px) {
            .mobile-brand {
                display: none;
            }
        }

        @keyframes slideInDown {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .mobile-brand-header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .mobile-chef-hat {
            width: 32px;
            height: 32px;
            margin-right: 8px;
            color: #ea580c;
            animation: rotate 15s linear infinite;
        }

        .mobile-brand-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #1f2937;
        }

        .mobile-brand-subtitle {
            color: #6b7280;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 0.75rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            position: relative;
            animation: cardEntry 0.6s ease-out 0.8s both;
            transition: transform 0.3s ease;
        }

        .login-card:hover {
            transform: scale(1.02);
        }

        @keyframes cardEntry {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .card-glow {
            position: absolute;
            inset: 0;
            background: linear-gradient(to right, rgba(249, 115, 22, 0.2), rgba(239, 68, 68, 0.2), rgba(245, 158, 11, 0.2));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .login-card:hover .card-glow {
            opacity: 1;
        }

        .card-header {
            padding: 1.5rem 1.5rem 1rem;
            text-align: center;
            position: relative;
            z-index: 10;
        }

        .card-title {
            font-size: 1.5rem;
            color: #1f2937;
            margin-bottom: 0.5rem;
            animation: fadeIn 1s ease-out 1s both;
        }

        .card-subtitle {
            color: #6b7280;
            animation: fadeIn 1s ease-out 1.2s both;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .card-content {
            padding: 0 1.5rem 1.5rem;
            position: relative;
            z-index: 10;
        }

        .form-group {
            margin-bottom: 1rem;
            animation: slideInUp 0.6s ease-out both;
        }

        .form-group:nth-child(1) { animation-delay: 1.4s; }
        .form-group:nth-child(2) { animation-delay: 1.6s; }

        @keyframes slideInUp {
            from { transform: translateX(-20px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: #374151;
            font-weight: 500;
        }

        .input-container {
            position: relative;
            transition: transform 0.2s ease;
        }

        .input-container:focus-within {
            transform: scale(1.02);
        }

        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            z-index: 10;
            width: 16px;
            height: 16px;
        }

        .form-input {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .form-input:focus {
            outline: none;
            border-color: #fb923c;
            box-shadow: 0 0 0 3px rgba(251, 146, 60, 0.1), 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .form-input:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            transition: all 0.2s ease;
            z-index: 10;
            width: 16px;
            height: 16px;
        }

        .password-toggle:hover {
            color: #6b7280;
            transform: translateY(-50%) scale(1.1);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            animation: fadeIn 1s ease-out 1.8s both;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            transition: transform 0.2s ease;
        }

        .checkbox-container:hover {
            transform: scale(1.05);
        }

        .checkbox {
            margin-right: 0.5rem;
        }

        .checkbox-label {
            font-size: 0.875rem;
            color: #6b7280;
        }

        .forgot-password {
            font-size: 0.875rem;
            color: #ea580c;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .forgot-password:hover {
            color: #c2410c;
            text-decoration: underline;
            transform: scale(1.05);
        }

        .login-button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(to right, #f97316, #ef4444);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            animation: slideInUp 0.6s ease-out 2s both;
        }

        .login-button:hover {
            background: linear-gradient(to right, #ea580c, #dc2626);
            transform: scale(1.05);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.25);
        }

        .login-button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .loading-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to right, #fb923c, #f87171);
            transform: translateX(-100%);
            animation: loading 1s ease-in-out infinite;
        }

        @keyframes loading {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid white;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .support-section {
            text-align: center;
            padding-top: 1rem;
            border-top: 1px solid #f3f4f6;
            margin-top: 1.5rem;
            animation: fadeIn 1s ease-out 2.2s both;
        }

        .support-text {
            font-size: 0.875rem;
            color: #6b7280;
        }

        .support-link {
            color: #ea580c;
            text-decoration: none;
            transition: transform 0.2s ease;
        }

        .support-link:hover {
            text-decoration: underline;
            transform: scale(1.05);
        }

        .copyright {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.75rem;
            color: #9ca3af;
            animation: fadeIn 1s ease-out 2.4s both;
        }

        /* Icons */
        .icon {
            display: inline-block;
            width: 1em;
            height: 1em;
            stroke-width: 0;
            stroke: currentColor;
            fill: currentColor;
        }

        /* Responsive */
        @media (max-width: 1023px) {
            .container {
                background: linear-gradient(135deg, #fed7aa, #fef3c7);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="animated-bg"></div>

        <div class="left-side">
            <div class="overlay"></div>
            <img src="https://images.unsplash.com/photo-1758745464235-ccb8c1253074?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxjb2xvcmZ1bCUyMHNwaWNlcyUyMG1hcmtldCUyMGRpc3BsYXl8ZW58MXx8fHwxNzU5MTYyOTc5fDA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral" 
                alt="Colorful spices display" class="spice-image">
            
            <div class="floating-particles" id="particles"></div>
            
            <div id="sparkles"></div>

            <div class="brand-content">
                <div class="brand-header">
                    <svg class="chef-hat icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 13.87A4 4 0 0 1 7.41 6a5.11 5.11 0 0 1 1.05-1.54 5 5 0 0 1 7.08 0A5.11 5.11 0 0 1 16.59 6 4 4 0 0 1 18 13.87V21H6Z"/>
                        <line x1="6" y1="17" x2="18" y2="17"/>
                    </svg>
                    <h1 class="brand-title">Aroma Hub</h1>
                </div>
                <p class="brand-subtitle">Premium Spices & Seasonings</p>
                <p class="brand-description">
                    Welcome to your admin dashboard. Manage your spice inventory, orders, 
                    and customers with ease.
                </p>
            </div>
        </div>

        <div class="right-side">
            <div class="form-bg"></div>
            
            <div class="form-container">
                <div class="mobile-brand">
                    <div class="mobile-brand-header">
                        <svg class="mobile-chef-hat icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 13.87A4 4 0 0 1 7.41 6a5.11 5.11 0 0 1 1.05-1.54 5 5 0 0 1 7.08 0A5.11 5.11 0 0 1 16.59 6 4 4 0 0 1 18 13.87V21H6Z"/>
                            <line x1="6" y1="17" x2="18" y2="17"/>
                        </svg>
                        <h1 class="mobile-brand-title">Aroma Hub</h1>
                    </div>
                    <p class="mobile-brand-subtitle">Admin Portal</p>
                </div>

                <div class="login-card">
                    <div class="card-glow"></div>
                    
                    <div class="card-header">
                        <h2 class="card-title">Admin Login</h2>
                        <p class="card-subtitle">Enter your credentials to access the dashboard</p>
                    </div>
                    
                    <div class="card-content">
                        <form id="loginForm" method="POST">
                            <div class="form-group">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-container">
                                    <svg class="input-icon icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                        <polyline points="22,6 12,13 2,6"/>
                                    </svg>
                                    <input type="email" id="email" class="form-input" name="adminusername" placeholder="Enter your email" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-container">
                                    <svg class="input-icon icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                        <circle cx="12" cy="16" r="1"/>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                    </svg>
                                    <input type="password" id="password" class="form-input" name="password" placeholder="Enter your password" required>
                                    <button type="button" class="password-toggle" id="passwordToggle">
                                        <svg class="icon" id="eyeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        <svg class="icon" id="eyeOffIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: none;">
                                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                            <line x1="1" y1="1" x2="23" y2="23"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="form-options">
                                <div class="checkbox-container">
                                    <input type="checkbox" id="remember" class="checkbox">
                                    <label for="remember" class="checkbox-label">Remember me</label>
                                </div>
                                <a href="#" class="forgot-password">Forgot password?</a>
                            </div>

                            <button type="submit" class="login-button" name="login" id="loginBtn">
                                <div class="loading-overlay" id="loadingOverlay" style="display: none;"></div>
                                <span id="btnText">Sign In to Dashboard</span>
                                <div class="spinner" id="spinner" style="display: none;"></div>
                            </button>
                        </form>

                        <div class="support-section">
                            <p class="support-text">
                                Need help? Contact 
                                <a href="mailto:support@aromahub.com" class="support-link">support@aromahub.com</a>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="copyright">
                    © 2024 Aroma Hub. All rights reserved.
                </div>
            </div>
        </div>
    </div>

    <script>
        // Create floating particles
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            for (let i = 0; i < 20; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.top = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 2 + 's';
                particle.style.animationDuration = (Math.random() * 3 + 2) + 's';
                particlesContainer.appendChild(particle);
            }
        }

        // Create sparkles
        function createSparkles() {
            const sparklesContainer = document.getElementById('sparkles');
            for (let i = 0; i < 8; i++) {
                const sparkle = document.createElement('div');
                sparkle.className = 'sparkle';
                sparkle.innerHTML = '✨';
                sparkle.style.left = (Math.random() * 80 + 10) + '%';
                sparkle.style.top = (Math.random() * 80 + 10) + '%';
                sparkle.style.animationDelay = (i * 0.5) + 's';
                sparkle.style.position = 'absolute';
                sparklesContainer.appendChild(sparkle);
            }
        }

        // Password toggle functionality (Kept for design/utility)
        document.getElementById('passwordToggle').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeOffIcon = document.getElementById('eyeOffIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.style.display = 'none';
                eyeOffIcon.style.display = 'block';
            } else {
                passwordInput.type = 'password';
                eyeIcon.style.display = 'block';
                eyeOffIcon.style.display = 'none';
            }
        });

        // 🔥 FIX 6: Removed the JavaScript form submission handler (e.preventDefault())
        // The form now submits directly to the PHP script above.

        // Initialize animations (Kept for design)
        document.addEventListener('DOMContentLoaded', function() {
            createParticles();
            createSparkles();
        });

        // Add hover effects to form inputs (Kept for design)
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>