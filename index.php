<?php

require_once 'function.php';

//判斷用戶是否登入，一定是最優先
lop_get_current_user();



//獲取介面所需要的數據
//重複的操作需封裝起來

$catena_count = lop_fetch_one('select count(1) as num from catena;')['num'];
$kind_count = lop_fetch_one('select count(1) as num from kind;')['num'];
$products_count = lop_fetch_one('select count(1) as num from products;')['num'];
$slides_count = lop_fetch_one('select count(1) as num from slides;')['num'];
$news_count = lop_fetch_one('select count(1) as num from news;')['num'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>admin</title>
    <meta name="viewport" content="width=device-width,  initial-scale=1, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>
    <script src="static/venders/fontsicon/fontAwesome.js"></script>
    <link rel="stylesheet" type="text/css" href="static/venders/bootstrap-4.2.1-dist/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="static/css/index.css">
    <div class="main">
        <?php include 'inc/navbar.php'; ?>
        <div class="content">
            <div class="jumbotron">
              <p>LOPHURA 管理系統</p>
              <a class="btn btn-primary btn-lg" href="add_products.php" role="button">新增產品</a>
            </div>
            <ul class="list-group">
              <li class="list-group-item group_title">網站內容統計：</li>
              <li class="list-group-item">總共<?php echo $products_count; ?>個產品</li>
              <li class="list-group-item"><?php echo $kind_count; ?>個產品分類</li>
              <li class="list-group-item"><?php echo $catena_count; ?>個系列分類</li>
              <li class="list-group-item"><?php echo $slides_count; ?>張輪播圖</li>
              <li class="list-group-item"><?php echo $news_count; ?>個活動</li>
            </ul>
        </div>
    </div>
    <script src="static/venders/jquery/jquery-1.12.4.js"></script>
    <script src="static/venders/popper/popper.min.js"></script>
    <script src="static/venders/bootstrap-4.2.1-dist/js/bootstrap.js"></script>
    <?php $current_page = 'index' ?>
    <?php include 'inc/slides.php'; ?>
</body>
</html>