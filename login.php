<?php

// 載入配置
require_once 'outer/config.php';

// 給用戶找訊息（如果有就給舊的，沒有則給新的）
session_start();


function login() {
  if(empty($_POST['email'])) {
    $GLOBALS['message'] = '請填寫郵箱';
    return;
  }
  if(empty($_POST['password'])) {
    $GLOBALS['message'] = '請填寫密碼';
    return;
  }
  $email = $_POST['email'];
  $password = $_POST['password'];

  // 客戶端提交完整表單則進行數據校驗
  $conn = mysqli_connect(LOP_DB_HOST, LOP_DB_USER, LOP_DB_PASS, LOP_DB_NAME);

  if (!$conn) {
    exit('<h1>連結數據庫失敗');
  }

  $query = mysqli_query($conn, "select * from users where email = '{$email}' limit 1;");

  if (!$query) {
    $GLOBALS['message'] = '登入失敗，請重試！';
    return;
  }

  //獲取登入用戶
  $user = mysqli_fetch_assoc($query);

  if (!$user) {
    //用戶名不存在
    $GLOBALS['message'] = '郵箱與密碼不匹配';
    return;
  }
  //  密碼應需加密儲存
  if ($user['password'] !== $password) {
    //密碼不正確
    $GLOBALS['message'] = '郵箱與密碼不匹配';
    return;
  }
  //存登入標示
  // $_SEEION['is_logged_in'] = true;
  // 為了後續可以直接獲取當前登入用戶信息，這裡直接將用戶信息放入 session 中
  $_SESSION['current_login_user'] = $user;
  // 每次加載重新抓取
  // $_SESSION['current_login_id'] = $user['id'];

  // 準備跳轉
  header('Location: /index.php');
}

if ($_SERVER['REQUEST_METHOD'] ==='POST'){
  login();
}

//退出功能
if ($_SERVER['REQUEST_METHOD'] ==='GET' && isset($_GET['action']) && $_GET['action'] === 'logout'){
  unset($_SESSION['current_login_user']);
}
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>admin</title>
    <meta name="viewport" content="width=device-width,  initial-scale=1, user-scalable=no" >
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>
    <script src="static/venders/fontsicon/fontAwesome.js"></script>
    <link rel="stylesheet" type="text/css" href="static/venders/bootstrap-4.2.1-dist/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="static/assets/animate/animate.css">
    <link rel="stylesheet" type="text/css" href="static/css/login.css">
    <div class="login">
      <form class="login-wrap<?php echo isset($message) ? ' shake animated' : '' ?>" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" novalidate autocomplete="off">
        <img class="avatar" src="/static/assets/img/default.png">
        <!-- 有错误信息时展示 -->
        <?php if (isset($message)): ?>
        <div class="alert alert-danger">
          <strong>错误！</strong> <?php echo $message; ?>
        </div>
        <?php endif ?>
        <div class="form-group">
          <label for="email" class="sr-only">邮箱</label>
          <input id="email" name="email" type="email" class="form-control" placeholder="邮箱" autofocus value="<?php echo empty($_POST['email']) ? '' : $_POST['email'] ?>">
        </div>
        <div class="form-group">
          <label for="password" class="sr-only">密码</label>
          <input id="password" name="password" type="password" class="form-control" placeholder="密码">
        </div>
        <button class="btn btn-dark">登 录</button>
      </form>
    </div>
    <script src="static/venders/jquery/jquery-1.12.4.js"></script>
    <script src="static/venders/popper/popper.min.js"></script>
    <script src="static/venders/bootstrap-4.2.1-dist/js/bootstrap.js"></script>
    <script>
  $(function ($){
      var emailFormat = /^[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+$/
    $("#email").on("blur", function () {
      var value = $(this).val()
      if (!value || !emailFormat.test(value)) return
        $.get('/api/avatar.php', { email: value } , function (res){
          if(!res) return
          $('.avatar').fadeOut(function () {
            $(this).on('load',function () {
              $(this).fadeIn()
            }).attr('src',res).fadeIn()
          })
        })
    });
  })
</script>
</body>
</html>