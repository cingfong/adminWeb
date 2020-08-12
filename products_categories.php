<?php

require_once 'function.php';

//判斷用戶是否登入，一定是最優先
lop_get_current_user();

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
    <link rel="stylesheet" type="text/css" href="static/css/products_categories.css">
    <div class="main">
        <?php include 'inc/navbar.php'; ?>
        <div class="content">
            <div class="list-group title" id="myList" role="tablist">
                <a class="title_li active" data-toggle="list" href="#home" role="tab">包包類別</a>
                <a class="title_li" data-toggle="list" href="#profile" role="tab">配飾類別</a>
            </div>
            <div class="table">
                <div class="tab-content">
                    <div class="tab-pane active" id="home" role="tabpanel">
                        <div class="left_form">
                            <h3>包包目錄</h3>
                            <h5>添加新包包目錄</h5>
                            <div class="add_form">
                                <form>
                                    <p>名稱</p>
                                    <input type="text" name="" value="分類名稱">
                                    <br>
                                    <button type="button" class="btn btn-secondary">添加</button>
                                </form>
                            </div>
                        </div>
                        <div class="right_form">
                            <table>
                                <div class="btn_action">
                                    <button type="button" class="all_del btn btn-danger">批量刪除</button>
                                </div>
                                <thead>
                                    <tr>
                                        <th class="checkbox"><input type="checkbox" name=""></th>
                                        <th>名稱</th>
                                        <th class="th_btn">操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th><input type="checkbox" name=""></th>
                                        <th>Frame Bag</th>
                                        <th><button type="button" class="btn btn-primary">編輯</button><button type="button" class="btn btn-danger">刪除</button></th>
                                    </tr>
                                    <tr>
                                        <th><input type="checkbox" name=""></th>
                                        <th>Saddle Bag</th>
                                        <th><button type="button" class="btn btn-primary">編輯</button><button type="button" class="btn btn-danger">刪除</button></th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="profile" role="tabpanel">
                        <div class="left_form">
                            <h3>配件目錄</h3>
                            <h5>添加新配件目錄</h5>
                            <div class="add_form">
                                <form>
                                    <p>名稱</p>
                                    <input type="text" name="" value="分類名稱">
                                    <br>
                                    <button type="button" class="btn btn-secondary">添加</button>
                                </form>
                            </div>
                        </div>
                        <div class="right_form">
                            <table>
                                <div class="btn_action">
                                    <button type="button" class="all_del btn btn-danger">批量刪除</button>
                                </div>
                                <thead>
                                    <tr>
                                        <th class="checkbox"><input type="checkbox" name=""></th>
                                        <th>名稱</th>
                                        <th class="th_btn">操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th><input type="checkbox" name=""></th>
                                        <th>HandleBat Tape</th>
                                        <th><button type="button" class="btn btn-primary">編輯</button><button type="button" class="btn btn-danger">刪除</button></th>
                                    </tr>
                                    <tr>
                                        <th><input type="checkbox" name=""></th>
                                        <th>Bike Grips</th>
                                        <th><button type="button" class="btn btn-primary">編輯</button><button type="button" class="btn btn-danger">刪除</button></th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $current_page = 'products_cat'; ?>
    <script src="static/venders/jquery/jquery-1.12.4.js"></script>
    <!-- <script src="static/venders/popper/popper.min.js"></script> -->
    <script src="static/venders/bootstrap-4.2.1-dist/js/bootstrap.js"></script>
    <script src="static/js/index.js"></script>
    <?php include 'inc/slides.php'; ?>
</body>
</html>