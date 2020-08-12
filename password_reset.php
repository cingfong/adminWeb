<?php

require_once 'function.php';

//判斷用戶是否登入，一定是最優先
lop_get_current_user();



//判斷是否為需要編輯的數據
//===========================
function re_pasd(){
  if(empty($_POST['old_pasd'] && $_POST['new_pasd'] && $_POST['check_pasd'])){
    $GLOBALS['message'] = '請完整填寫表單';
    return;
  }else{
    $old_pasd = $_POST['old_pasd'];
    $new_pasd = $_POST['new_pasd'];
    $check_pasd = $_POST['check_pasd'];
    if($new_pasd !== $check_pasd){
        $GLOBALS['message'] = '新密碼請一致';
    }else{
    //
        $new_pasd_leng = strlen($new_pasd);
        $check_pasd_leng = strlen($check_pasd);
        if($new_pasd_leng < 8 && $check_pasd_leng < 8){
            $GLOBALS['message'] = '新密碼請大於八個字';
        }else{
            $sql_data = lop_fetch_one('select * from users where 1');
            $sql_pasd = $sql_data['password'];
                $GLOBALS['message'] = $sql_pasd;
            if($old_pasd !== $sql_pasd){
                $GLOBALS['message'] = '舊密碼有誤';
            }else{
              $rows = lop_execute("update users set password = '{$new_pasd}' where 1");
                $GLOBALS['success'] = !$row > 0;
                $GLOBALS['message'] = !$rows <= 0 ? '更新失敗' : '更新成功';
            }
        }
    //
    }
  }
}

//判斷是編輯還是添加
if (empty($_GET['old_pasd'])) {
    //get動作
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    re_pasd();
    }
} else {
  if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $GLOBALS['message'] = '更新錯誤';
  }
}


//查詢全部的分類數據
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
    <link rel="stylesheet" type="text/css" href="static/css/password-reset.css">
    <div class="main">
        <?php include 'inc/navbar.php'; ?>
        <div class="content">
      <!-- 有错误信息时展示 -->
                <?php if(isset($message)): ?>
                  <?php if($success): ?>
                    <div class="alert alert-success">
                      <strong>成功！</strong><?php echo $message ?>
                    </div>
                  <?php else: ?>
                    <div class="alert alert-danger">
                      <strong>错误！</strong><?php echo $message ?>
                    </div>
                  <?php endif ?>
                <?php endif; ?>
            <h2>修改密碼</h2>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off">
                <div class="updata">
                    <p>舊密碼</p>
                    <input class="old_pasd" type="password" name="old_pasd" placeholder="舊密碼">
                </div>
                <div class="updata">
                    <p>新密碼</p>
                    <input class="new_pasd" type="password" name="new_pasd" placeholder="新密碼">
                </div>
                <div class="updata">
                    <p>確認新密碼</p>
                    <input class="check_pasd" type="password" name="check_pasd" placeholder="確認密碼">
                </div>
                <button type="submit" class="btn btn-secondary">修改密碼</button>
            </form>
        </div>
    </div>
    <?php $current_page = 'password_reset'; ?>
    <script src="static/venders/jquery/jquery-1.12.4.js"></script>
    <script src="static/venders/popper/popper.min.js"></script>
    <script src="static/venders/bootstrap-4.2.1-dist/js/bootstrap.js"></script>
    <script src="static/js/index.js"></script>
    <?php include 'inc/slides.php'; ?>
    <script>
    </script>
</body>
</html>