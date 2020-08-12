<?php

require_once 'function.php';

//判斷用戶是否登入，一定是最優先
lop_get_current_user();

//判斷是否為需要編輯的數據
//===========================
function add_slides(){
    //取出編號查詢有無重複
    $have_data =  lop_fetch_one('select * from slides where order_num = ' . $_POST['order_num']);
    // 當前時間戳
    $name_data = time();
    if(empty($_FILES['img']) || empty($_POST['order_num'])){
        $GLOBALS['message'] = '請完整填寫表單';
        return;
        }elseif ($_POST['order_num'] > 10 || $_POST['order_num'] < 1 ){
            $GLOBALS['message'] = '請輸入正確編號';
            return;
        }elseif ($have_data > 0){
            $GLOBALS['message'] = '順序編號已重複';
        }else if(empty($_FILES['img']['error'])){
            $temp_file = $_FILES['img']['tmp_name'];
            $target_file = './static/img/slides/' . $name_data . $_FILES['img']['name'];

            if (move_uploaded_file($temp_file, $target_file)) {
                $image_file = './static/img/slides/' . $name_data . $_FILES['img']['name'];
            }
            // 接收数据
            $img = isset($target_file) ? $target_file : '';
            // 接收並保存
            $order_num = $_POST['order_num'];
            $rows = lop_execute("insert into slides values(null, '{$img}', '{$order_num}')");
            $GLOBALS['success'] = !$row > 0;
            $GLOBALS['message'] = !$rows <= 0 ? '添加失敗' : '添加成功';
        }
    }

function edit_slides () {
    global $current_edit_slides;
    //取出編號查詢有無重複
    $have_data =  lop_fetch_one('select * from slides where order_num = ' . $_POST['order_num']);
    //當前時間戳
    $name_data = time();
    //接收並保存
    $id = $current_edit_slides['id'];

    //檢查順序編號
    if($_POST['order_num'] > 10 || $_POST['order_num'] < 1 ){
        $GLOBALS['message'] = '請輸入正確編號';
        return;
    }
    $order_num = empty($_POST['order_num']) ? $current_edit_category['order_num'] : $_POST['order_num'];
    $order_num = empty($_POST['order_num']) ? $current_edit_category['order_num'] : $_POST['order_num'];
    //照片
    if (empty($_FILES['img']['error'])) {
        $temp_file = $_FILES['img']['tmp_name'];
        $target_file = './static/img/slides/' . $name_data . $_FILES['img']['name'];
        //承上
        if (move_uploaded_file($temp_file, $target_file)) {
            $image_file = './static/img/slides/' . $name_data . $_FILES['img']['name'];
        }
        // 接收数据
        $img = isset($target_file) ? $target_file : '';
    }else{
        //無夾帶檔案
        if ($have_data > 0) {
            $GLOBALS['message'] = '順序編號已重複';
            return;
        }
        $img = $current_edit_slides['slide_img'];
    }


        //更新成最新數據
        $current_edit_slides['slide_img'] = $img;
        $current_edit_slides['order_num'] = $order_num;

        $rows = lop_execute("update slides set slide_img = '{$img}', order_num = '{$order_num}' where id = $id");
        $GLOBALS['success'] = !$row > 0;
        $GLOBALS['message'] = !$rows <= 0 ? '更新失敗' : '更新成功';
}

//判斷是編輯還是添加
if (empty($_GET['id'])) {
  //添加
  if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    add_slides();
  }
} else {
  //編輯
  //客戶端通過 URL 傳遞了一個 ID => 客戶端是要來拿一個修改數據表單
  // => 客戶端是要來拿一個修改數據的表單
  // => 需要拿到用戶想要修改的數據
  $current_edit_slides =  lop_fetch_one('select * from slides where id = ' . $_GET['id']);
  if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    edit_slides();
  }
}

//查詢全部的輪播數據
$slides = lop_fetch_all('select * from slides');
foreach ($slides as $k => $v ){
    $data[] = $v['order_num'];
}

$slides == null ? '' : array_multisort($data, SORT_ASC, $slides);
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
    <link rel="stylesheet" type="text/css" href="static/css/slides.css">
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
                <?php if (isset($current_edit_slides)): ?>
                    <h3>首頁幻燈片</h3>
                    <h5>編輯幻燈片第《 <?php echo $current_edit_slides['order_num']; ?> 》張 </h5>
                    <div class="add_form">
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $current_edit_slides['id']?>" method="post" autocomplete="off" enctype="multipart/form-data">
                            <p>圖片</p>
                            <div class="form-group">
                            <!-- <label for="exampleFormControlFile1">請上傳照片</label> -->
                                <img class="help-block thumbnail look_img" width="400px" src="<?php echo $current_edit_slides['slide_img']; ?>">
                                <input class="up_img img" type="file" class="form-control-file" name="img" id="img" accept="image/*">
                            </div>
                            <p>順序</p>
                            <input class="number" type="number"  id="order_num" name="order_num" placeholder="順序為1-10" value="<?php echo $current_edit_slides['order_num']; ?>">
                            <br>
                            <button type="submit" class="btn btn-secondary">保存</button>
                            <a href="/slides.php" class="btn btn-light re_web">取消編輯</a>
                        </form>
                    </div>
                <?php else: ?>
                    <h3>首頁幻燈片</h3>
                    <h5>添加新幻燈片內容</h5>
                    <div class="add_form">
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off" enctype="multipart/form-data">
                            <p>圖片</p>
                            <div class="form-group">
                                <!-- <label for="exampleFormControlFile1">請上傳照片</label> -->
                                <img class="help-block thumbnail look_img" width="400px" style="display: none">
                                <input class="up_img img" type="file" class="form-control-file" name="img" id="img" accept="image/*">
                            </div>
                            <p>照片順序</p>
                            <input class="number" type="number" name="order_num" id="order_num" placeholder="順序為1-10">
                            <br>
                            <button type="submit" class="btn btn-secondary">添加</button>
                        </form>
                    </div>
                <?php endif ?>
            </div>
            <div class="right_form">
                <table>
                    <div class="btn_action">
                        <a id="btn_delete" class="all_del btn btn-danger" style="display: none" href="/slides-delete.php">批量刪除</a>
                    </div>
                    <thead>
                        <tr>
                            <th class="checkbox"><input type="checkbox" name=""></th>
                            <th>圖片</th>
                            <th>順序</th>
                            <th class="th_btn">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($slides as $item ): ?>
                            <tr>
                                <th><input type="checkbox" name="" data-id="<?php echo $item['id']; ?>"></th>
                                <th class="see_img"><img src="<?php echo $item['slide_img']; ?>"></th>
                                <th class="num"><?php echo $item['order_num']; ?></th>
                                <th class="th_btn">
                                    <a href="slides.php?id=<?php echo $item['id']; ?>"
                                        class="btn btn-primary"
                                    >編輯</a>
                                    <a href="slides-delete.php?id=<?php echo $item['id']; ?>"
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
    <?php $current_page = 'slides'; ?>
    <script src="static/venders/jquery/jquery-1.12.4.js"></script>
    <!-- <script src="static/venders/popper/popper.min.js"></script> -->
    <script src="static/venders/bootstrap-4.2.1-dist/js/bootstrap.js"></script>
    <script src="static/js/index.js"></script>
    <?php include 'inc/slides.php'; ?>
    <script>
          $(function ($) {
              var $tbodyCheckboxs = $('tbody input')
              var $btnDelete = $('#btn_delete')
              var allCheckeds = []

              $tbodyCheckboxs.on('change', function () {

                var id = $(this).data('id')

                if ($(this).prop('checked')) {

                  allCheckeds.includes(id) || allCheckeds.push(id)

                } else {

                  allCheckeds.splice(allCheckeds.indexOf(id), 1)

                }

                allCheckeds.length ? $btnDelete.fadeIn() : $btnDelete.fadeOut()

                $btnDelete.prop('search', '?id=' + allCheckeds)

              })

              $('thead input').on('change', function () {

                var checked = $(this).prop('checked');

                $tbodyCheckboxs.prop('checked', checked).trigger('change')
              })
          })
              //照片預覽
      $('#img').on('change', function () {
        var file = $(this).prop('files')[0]
        var url = URL.createObjectURL(file)
        $(this).siblings('.thumbnail').attr('src', url).fadeIn()
      })
    </script>
</body>
</html>