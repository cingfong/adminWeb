<?php

require_once 'function.php';

//判斷用戶是否登入，一定是最優先
lop_get_current_user();



//判斷是否為需要編輯的數據
//===========================
function add_category(){
  if(empty($_POST['name'])){
    //
    $GLOBALS['message'] = '請完整填寫表單';
    return;
  }
  //接收並保存
  $name = $_POST['name'];
  $rows = lop_execute("insert into catena values('{$name}' , null)");
    $GLOBALS['success'] = !$row > 0;
    $GLOBALS['message'] = !$rows <= 0 ? '添加失敗' : '添加成功';
}

function edit_category () {
  global $current_edit_category;

  //接收並保存
  $id = $current_edit_category['product_id'];
  $name = empty($_POST['name']) ? $current_edit_category['name'] : $_POST['name'];
  //更新成最新數據
  $current_edit_category['name'] = $name;

  $rows = lop_execute("update catena set name = '{$name}' where product_id = {$id}");
    $GLOBALS['success'] = !$row > 0;
    $GLOBALS['message'] = !$rows <= 0 ? '更新失敗' : '更新成功';

}

//判斷是編輯還是添加
if (empty($_GET['id'])) {
  //添加
  if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    add_category();
  }
} else {
  //編輯
  //客戶端通過 URL 傳遞了一個 ID => 客戶端是要來拿一個修改數據表單
  // => 客戶端是要來拿一個修改數據的表單
  // => 需要拿到用戶想要修改的數據
  $current_edit_category =  lop_fetch_one('select * from catena where product_id = ' . $_GET['id']);
  if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    edit_category();
  }
}


//查詢全部的分類數據
$categories = lop_fetch_all('select * from catena');

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
    <link rel="stylesheet" type="text/css" href="static/css/class_categories.css">
    <div class="main">
        <?php include 'inc/navbar.php'; ?>
        <div class="content">
            <div class="left_form">
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
                <?php if (isset($current_edit_category)): ?>
                    <h3>系列目錄</h3>
                    <h5>編輯《 <?php echo $current_edit_category['name']; ?> 》 </h5>
                    <div class="add_form">
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $current_edit_category['product_id']?>" method="post" autocomplete="off">
                            <p>名稱</p>
                            <input type="text" class="remove" id="name" name="name" placeholder="系列名稱" value="<?php echo $current_edit_category['name']; ?>">
                            <br>
                            <!-- <p>別名</p>
                            <input type="text" name="" value="別名"> -->
                            <button type="submit" class="btn btn-secondary">保存</button>
                            <a href="/class_categories.php" class="btn btn-light re_web">取消編輯</a>
                        </form>
                    </div>
                <?php else: ?>
                    <h3>系列目錄</h3>
                    <h5>添加新系列目錄</h5>
                    <div class="add_form">
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off">
                            <p>名稱</p>
                            <input type="text" name="name" id="name" placeholder="系列名稱">
                            <br>
                            <button type="submit" class="btn btn-secondary">添加</button>
                        </form>
                    </div>
                <?php endif ?>
            </div>
            <div class="right_form">
                <table>
                    <div class="btn_action">
                        <a id="btn_delete" class="all_del btn btn-danger" style="display: none" href="/category-delete.php">批量刪除</a>
                    </div>
                    <thead>
                        <tr>
                            <th class="checkbox"><input type="checkbox" name=""></th>
                            <th>名稱</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $item): ?>
                            <tr>
                                <th><input type="checkbox" name="" data-id="<?php echo $item['product_id']; ?>"></th>
                                <th><?php echo $item['name']; ?></th>
                                <th style="width: 190px;">
                                    <a href="class_categories.php?id=<?php echo $item['product_id']; ?>"
                                        class="btn btn-primary"
                                    >編輯</a>
                                    <a href="category-delete.php?id=<?php echo $item['product_id']; ?>"
                                        class="btn btn-danger"
                                    >刪除</a>
                                </th>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php $current_page = 'class_cat'; ?>
    <script src="static/venders/jquery/jquery-1.12.4.js"></script>
    <!-- <script src="static/venders/popper/popper.min.js"></script> -->
    <script src="static/venders/bootstrap-4.2.1-dist/js/bootstrap.js"></script>
    <script src="static/js/index.js"></script>
    <?php include 'inc/slides.php'; ?>
    <script>
          // 1. 不要重複使用無意義的選擇操作，應該採用變量去本地化
          $(function ($) {
              //有任意一個 checkbox選終究顯示，反之隱藏

              var $tbodyCheckboxs = $('tbody input')
              var $btnDelete = $('#btn_delete')

              //定義一個數組，記錄被選中的
              var allCheckeds = []
              $tbodyCheckboxs.on('change', function () {

                var id = $(this).data('id')

                //根據有沒有選中當前這個 checkbox 決定是添加還是移除
                if ($(this).prop('checked')) {
                  //判斷數組內有無重複
                  allCheckeds.includes(id) || allCheckeds.push(id)
                } else {
                  allCheckeds.splice(allCheckeds.indexOf(id), 1)
                }

                //根據剩下多少選中的 checkbox 決定是否顯示刪除
                allCheckeds.length ? $btnDelete.fadeIn() : $btnDelete.fadeOut()
                $btnDelete.prop('search', '?id=' + allCheckeds)
              })

              //找一個合適的時機 做一件合適的事情
              //全選和全不選
              $('thead input').on('change', function () {
                // 1.獲取當前選中狀態
                var checked = $(this).prop('checked');
                // 2.設置給標體中的每一個
                $tbodyCheckboxs.prop('checked', checked).trigger('change')
              })
          })
    </script>
</body>
</html>