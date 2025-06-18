<?php
session_start();

// Nếu đã có session hoặc cookie "remember", chuyển hướng sang home
if (isset($_SESSION['username']) || isset($_COOKIE['remember'])) {
    header("Location: home.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đăng nhập</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 40px;
      background-color: #f0f0f0;
    }
    .login-box {
      max-width: 400px;
      margin: auto;
      padding: 24px;
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    input[type="text"], input[type="password"] {
      width: 100%;
      padding: 10px;
      margin-top: 8px;
      margin-bottom: 16px;
      border: 2px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
    }
    input.error {
      border-color: red;
    }
    #message {
      display: none;
      color: red;
      margin-bottom: 10px;
    }
    button {
      width: 100%;
      padding: 10px;
      background-color: #4CAF50;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    button:hover {
      background-color: #45a049;
    }
    .links {
      text-align: center;
      margin-top: 16px;
    }
    .links a {
      margin: 0 10px;
      color: blue;
      text-decoration: none;
    }
    .remember {
      margin-bottom: 16px;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>Đăng nhập</h2>
    <div id="message">Thông báo</div>
    <form id="loginForm" method="POST" action="login.php">
      <label for="username">Tên đăng nhập:</label>
      <input type="text" id="username" name="username">

      <label for="password">Mật khẩu:</label>
      <input type="password" id="password" name="password">

      <div class="remember">
        <input type="checkbox" id="remember" name="remember">
        <label for="remember">Nhớ tài khoản</label>
      </div>

      <button type="submit">Đăng nhập</button>
    </form>

    <div class="links">
      <a href="/auth/register.html">Đăng ký</a> |
      <a href="/auth/forgotpassword.html">Quên mật khẩu</a>
    </div>
  </div>

  <script>
    const form = document.getElementById("loginForm");
    const message = document.getElementById("message");
    const username = document.getElementById("username");
    const password = document.getElementById("password");

    function containsUnicode(str) {
      return /[^\u0000-\u007f]/.test(str);
    }

    form.addEventListener("submit", function (e) {
      let error = "";
      username.classList.remove("error");
      password.classList.remove("error");

      if (username.value.trim() === "" || password.value.trim() === "") {
        error = "Điền đầy đủ username và password";
        username.classList.add("error");
        password.classList.add("error");
      } else if (containsUnicode(username.value)) {
        error = "Username không được dùng kí tự unicode";
        username.classList.add("error");
      } else if (containsUnicode(password.value)) {
        error = "Password không được dùng kí tự unicode";
        password.classList.add("error");
      }

      if (error !== "") {
        e.preventDefault();
        message.textContent = error;
        message.style.display = "block";
      }
    });
  </script>
</body>
</html>
