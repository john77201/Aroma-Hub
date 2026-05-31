<?php
session_start();
include 'connection.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM user WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        $_SESSION['userid'] = $row['userid'];
        $_SESSION['uname'] = $row['username'];
        echo "<script>alert('Login successful!'); window.location='index.php';</script>";
        exit();
    } else {
        echo "<script>
                document.addEventListener('DOMContentLoaded',()=>{
                    let box=document.querySelector('.form-box');
                    box.classList.add('shake');
                    setTimeout(()=>box.classList.remove('shake'),500);
                });
                alert('Invalid Email or Password.');
              </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Aroma Hub - Login</title>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
    *{margin:0;padding:0;box-sizing:border-box;}
    body{
    font-family:'Poppins',sans-serif;
    height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    background: url('images/top-view-asian-food-ingredients-mix.jpg') no-repeat center center;
    background-size: cover;
    position:relative;
}

    body::before{
        content:"";
        position:absolute;
        top:0;left:0;width:100%;height:100%;
        background:rgba(255,255,255,0.1); /* lighter overlay for clearer image */
        backdrop-filter:blur(0.5px); /* very light blur */
    }

    .form-box{
        position:relative;
        z-index:1;
        width:400px;
        background:rgba(255,255,255,0.95);
        border-radius:20px;
        padding:60px 35px 45px;
        box-shadow:0 15px 40px rgba(0,0,0,0.25);
        text-align:center;
        animation: fadeIn 1s ease;
    }
    @keyframes fadeIn {
        from { opacity:0; transform:translateY(-20px);}
        to { opacity:1; transform:translateY(0);}
    }
    @keyframes shake {
        0%,100%{transform:translateX(0);}
        20%,60%{transform:translateX(-10px);}
        40%,80%{transform:translateX(10px);}
    }
    .shake{animation:shake 0.5s;}
    .logo{
        position:absolute;
        top:-40px; left:50%;
        transform:translateX(-50%);
        background:#fff;
        border-radius:50%;
        padding:12px;
        box-shadow:0 5px 15px rgba(0,0,0,0.2);
    }
    .logo i{
        font-size:30px;
        color:#b45309;
    }
    h1{
        margin-bottom:25px;
        font-weight:700;
        font-size:2rem;
        color:#b45309;
    }
    .input-group{
        position:relative;
        margin:15px 0;
    }
    .input-group input{
        width:100%;
        padding:14px 45px 14px 18px;
        border-radius:12px;
        border:1.5px solid #d97706;
        font-size:15px;
        background:#fff;
        transition:0.3s;
    }
    .input-group input:hover{
        box-shadow:0 3px 10px rgba(180,83,9,0.1);
    }
    .input-group input:focus{
        outline:none;
        border-color:#b45309;
        box-shadow:0 0 0 3px rgba(180,83,9,0.15);
    }
    .input-group i{
        position:absolute;
        right:15px;
        top:50%;
        transform:translateY(-50%);
        font-size:18px;
        color:#b45309;
        cursor:pointer;
    }
    .input-group input:focus + i{
        color:#d97706;
        transform:translateY(-50%) scale(1.1);
    }
    button{
        border-radius:20px;
        border:none;
        background:linear-gradient(135deg,#b45309,#d97706);
        color:white;
        font-size:15px;
        font-weight:600;
        padding:14px 40px;
        text-transform:uppercase;
        margin-top:20px;
        cursor:pointer;
        transition:0.3s;
    }
    button:hover{
        transform:translateY(-2px) scale(1.05);
        box-shadow:0 10px 25px rgba(180,83,9,0.4);
    }
    .social-login{
        margin-top:25px;
    }
    .social-btn{
        display:inline-block;
        margin:8px;
        padding:12px 18px;
        border-radius:50%;
        background:#eee;
        color:#444;
        font-size:18px;
        transition:0.3s;
        box-shadow:0 4px 8px rgba(0,0,0,0.1);
    }
    .social-btn:hover{
        transform:scale(1.1);
    }
    .social-btn.google{color:#db4437;}
    .social-btn.facebook{color:#3b5998;}
    .social-btn.twitter{color:#1da1f2;}
    .switch-link{
        margin-top:18px;
        display:block;
        color:#b45309;
        text-decoration:none;
        font-weight:600;
    }
    .switch-link:hover{
        text-decoration:underline;
    }
  </style>
</head>
<body>
  <div class="form-box">
    <!-- Floating logo -->
    <div class="logo"><i class="fa-solid fa-spa"></i></div>

    <h1>Welcome Back</h1>
    <form method="POST" action="">
      <div class="input-group">
        <input type="email" name="email" placeholder="Email" required />
        <i class="fa-solid fa-envelope"></i>
      </div>
      <div class="input-group">
        <input type="password" name="password" id="password" placeholder="Password" required />
        <i class="fa-solid fa-eye" id="togglePassword"></i>
      </div>
      <button type="submit" name="login">Login</button>
    </form>

    <!-- Social Login Buttons -->
    <div class="social-login">
        <p style="margin-bottom:10px; font-weight:600;">Or sign in with</p>
        <a href="#" class="social-btn google"><i class="fab fa-google"></i></a>
        <a href="#" class="social-btn facebook"><i class="fab fa-facebook-f"></i></a>
        <a href="#" class="social-btn twitter"><i class="fab fa-twitter"></i></a>
    </div>

    <a class="switch-link" href="register.php">Don't have an account? Sign Up</a>
  </div>

  <!-- JS effects -->
  <script>
    // Show/hide password
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    togglePassword.addEventListener('click', () => {
      if (password.type === "password") {
        password.type = "text";
        togglePassword.classList.replace("fa-eye","fa-eye-slash");
      } else {
        password.type = "password";
        togglePassword.classList.replace("fa-eye-slash","fa-eye");
      }
    });
  </script>
</body>
</html>
