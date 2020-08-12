<?php

require_once 'function.php';

//判斷用戶是否登入，一定是最優先
lop_get_current_user();

//判斷是否為需要編輯的數據
//===========================
function add_news(){
    //取出編號查詢有無重複
    $have_data =  lop_fetch_one('select * from news where order_num = ' . $_POST['order_num']);
    // 當前時間戳
    $name_data = time();
    if(empty($_POST['order_num']) ||
        empty($_POST['img_alt']) ||
        empty($_FILES['logo']) ||
        empty($_FILES['banner']) ||
        empty($_FILES['img']['name'][0])
    ){
        $GLOBALS['message'] = '請完整填寫表單';
        return;
    }elseif ($_POST['order_num'] > 10 || $_POST['order_num'] < 1 ){
        $GLOBALS['message'] = '請輸入正確編號';
        return;
    }elseif ($have_data > 0){
        $GLOBALS['message'] = '順序編號已重複';
        //更動
    }else if(empty($_FILES['logo']['error']) ||
            empty($_FILES['banner']['error'])){
        $temp_file_logo = $_FILES['logo']['tmp_name'];
        $temp_file_banner = $_FILES['banner']['tmp_name'];
        $target_file_logo = './static/img/news/' . $name_data . $_FILES['logo']['name'];
        $target_file_banner = './static/img/news/' . $name_data . $_FILES['banner']['name'];

        if (move_uploaded_file($temp_file_logo, $target_file_logo)) {
            $image_file = './static/img/news/' . $name_data . $_FILES['logo']['name'];
        }
        if (move_uploaded_file($temp_file_banner, $target_file_banner)) {
            $image_file = './static/img/news/' . $name_data . $_FILES['banner']['name'];
        }
        //多個檔案上傳方式
        include_once 'api/upload.func.php';
        $files = getFiles();
        foreach ($files as $fileInfo){
            $res = uploadFile($fileInfo);
            echo $res['mes'] . '<br>';
            if (!empty($res['dest'])){
                $uploadFiles[] = $res['dest'];
            }
        }
        $img = implode(',',$uploadFiles);
        $order_num = $_POST['order_num'];
        $img_alt = $_POST['img_alt'];
        $logo = isset($target_file_logo) ? $target_file_logo : '';
        $banner = isset($target_file_banner) ? $target_file_banner : '';


        $rows = lop_execute("insert into news values(null, '{$order_num}', '{$img}', '{$img_alt}', '{$logo}', '{$banner}')");
        $GLOBALS['success'] = !$row > 0;
        $GLOBALS['message'] = !$rows <= 0 ? '更新失敗' : '更新成功';
    }
}

function edit_news () {
    global $current_edit_news;
    //取出編號查詢有無重複
    $have_data =  lop_fetch_one('select * from news where order_num = ' . $_POST['order_num']);
    //當前時間戳
    $name_data = time();
    //接收並保存
    $id = $current_edit_news['id'];
    $img_alt = empty($_POST['img_alt']) ? $current_edit_news['img_alt'] : $_POST['img_alt'];

    //檢查順序編號
    if($_POST['order_num'] > 10 || $_POST['order_num'] < 1 ){
        $GLOBALS['message'] = '請輸入正確編號';
        return;
    }
    $order_num = empty($_POST['order_num']) ? $current_edit_news['order_num'] : $_POST['order_num'];
        //判斷照片是否都為空
    if(empty($_FILES['img']['error']) &&
        empty($_FILES['logo']['error']) &&
        empty($_FILES['banner']['error'])
    ){
        //無夾帶檔案，驗證順序編號
        if ($have_data > 0) {
            $GLOBALS['message'] = '順序編號已重複';
            return;
        }
    }else{
        //logo驗證照片
        if (empty($_FILES['logo']['error'])) {
            $temp_file_logo = $_FILES['logo']['tmp_name'];
            $target_file_logo = './static/img/news/' . $name_data . $_FILES['logo']['name'];
            //承上
            move_uploaded_file($temp_file_logo, $target_file_logo);
        }
        if (empty($_FILES['banner']['error'])) {
            $temp_file_banner = $_FILES['banner']['tmp_name'];
            $target_file_banner = './static/img/news/' . $name_data . $_FILES['banner']['name'];
            move_uploaded_file($temp_file_banner, $target_file_banner);
        }
            //多個檔案上傳方式
            include_once 'api/upload.func.php';
            $files = getFiles();
            //取出 live value 值且變成陣列
    $old_news_live =  lop_fetch_one('select news_live from news where id = ' . $_GET['id'])['news_live'];
    $old_news_live_array =  explode(",",$old_news_live);
    //獲取有被刪除的值
    $del_num_val = $_POST['del_live_img'];
    $del_num_array = str_split($del_num_val);
    if(!empty($del_num_array)){
        foreach ($del_num_array as $value) {
            unset($old_news_live_array[$value]);
        }
    }
    $old_news_live_array = array_values($old_news_live_array);
    //因為與直接套用 array 編號所以數字需減 1 後面取代才不會出現錯誤
    $old_news_live_leng = count($old_news_live_array);
            //計算 for 第幾位
            $files_num = 0;
            foreach ($files as $fileInfo){
                $res = uploadFile($fileInfo);
                if (!empty($res['dest'])){
                    $uploadFiles[] = $res['dest'];
                    //若空值則以舊資料代替
                }elseif($old_news_live_leng > $files_num){
                    $uploadFiles[] = $old_news_live_array[$files_num];
                }
                $files_num = $files_num + 1;
            }
            $img = implode(',',$uploadFiles);
            $uploadFiles_length = count($uploadFiles);

            if($uploadFiles_length > 1){
                $current_edit_news['news_live'] = $img;
            }else{
                $current_edit_news['news_live'] = $img;

            }
    }
        $logo = isset($target_file_logo) ? $target_file_logo : $current_edit_news['min_icon'];
        $banner = isset($target_file_banner) ? $target_file_banner : $current_edit_news['banner'];
        //更新成最新數據
        $current_edit_news['min_icon'] = $logo;
        $current_edit_news['banner'] = $banner;
        $current_edit_news['order_num'] = $order_num;
        $current_edit_news['img_alt'] = $img_alt;

        $rows = lop_execute("update news set order_num = '{$order_num}', img_alt = '{$img_alt}', news_live = '{$img}', min_icon = '{$logo}', banner = '{$banner}' where id = $id");
        $GLOBALS['success'] = !$row > 0;
        $GLOBALS['message'] = !$rows <= 0 ? '更新失敗' : '更新成功';
}

//判斷是編輯還是添加
if (empty($_GET['id'])) {
  //添加
  if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    add_news();
  }
} else {
  //編輯
  //客戶端通過 URL 傳遞了一個 ID => 客戶端是要來拿一個修改數據表單
  // => 客戶端是要來拿一個修改數據的表單
  // => 需要拿到用戶想要修改的數據
  $current_edit_news =  lop_fetch_one('select * from news where id = ' . $_GET['id']);
  if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    edit_news();
  }else{
    $current_edit_news_live =  lop_fetch_one('select news_live from news where id = ' . $_GET['id'])['news_live'];
  }
}

//查詢全部的輪播數據
$news = lop_fetch_all('select * from news');
foreach ($news as $k => $v ){
    $data[] = $v['order_num'];
}

$news == null ? '' : array_multisort($data, SORT_ASC, $news);
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
    <link rel="stylesheet" type="text/css" href="static/css/news.css">
    <div class="main">
        <?php include 'inc/navbar.php'; ?>
        <div class="content">
            <!-- 錯誤訊息 -->
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
            <!--  -->
            <div class="left_form">
                <!-- switch -->
                <div class="list-group file_control" id="list-tab" role="tablist">
                  <a class="active" id="list-home-list" data-toggle="list" href="#list-home" role="tab" aria-controls="home">內容</a>
                  <a class="" id="list-profile-list" data-toggle="list" href="#list-profile" role="tab" aria-controls="profile">照片</a>
                </div>
                <!--  -->
            <?php if (isset($current_edit_news)): ?>
                <h3>活動分類</h3>
                <h5>編輯活動第《 <?php echo $current_edit_news['order_num']; ?> 》個 </h5>
                <div class="add_form">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $current_edit_news['id']?>" method="post" autocomplete="off" enctype="multipart/form-data">
                        <!-- 1 -->
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="list-home" role="tabpanel" aria-labelledby="list-home-list">
                                <!-- 1 -->
                        <p>封面照</p>
                        <div class="form-group">
                            <img class="help-block thumbnail look_img" width="400px" src="<?php echo $current_edit_news['banner']; ?>">
                            <input class="up_banner banner" type="file" class="form-control-file" name="banner" id="banner" accept="image/*">
                        </div>
                        <p>LOGO</p>
                        <div class="form-group">
                            <img class="help-block thumbnail look_img" width="120px" src="<?php echo $current_edit_news['min_icon']; ?>">
                            <br>
                            <input class="up_logo logo" type="file" class="form-control-file" name="logo" id="logo" accept="image/*" />
                        </div>
                        <p>備註</p>
                        <input class="alt" type="text" name="img_alt" id="img_alt" placeholder="請敘述活動名稱" value="<?php echo $current_edit_news['img_alt']; ?>">
                        <br>
                        <p>照片順序</p>
                        <input class="number" type="number"  id="order_num" name="order_num" placeholder="順序為1-10" value="<?php echo $current_edit_news['order_num']; ?>">
                        <br>
                    </div>
                    <div class="tab-pane fade" id="list-profile" role="tabpanel" aria-labelledby="list-profile-list">
                        <p>情境照</p>
                        <div class="form-group form-group_live">
                            <img class="help-block thumbnail edit_live look_img" width="400px" src="<?php echo $current_edit_news['news_live']; ?>">
                            <input class="up_img img edit_img" type="file" class="form-control-file" name="img[]" id="img" accept="image/*" />
                        </div>
                        <!-- 隱藏的儲存刪除的值位置 -->
                        <input type="text" class="del_num" id="del_num" name="del_live_img">
                        <button type="submit" class="btn btn-secondary">保存</button>
                        <a href="/news.php" class="btn btn-light re_web">取消編輯</a>
                    </div>
                </div>
                    </form>
                </div>
            <?php else: ?>
                <h3>活動分類</h3>
                <h5>添加活動項目</h5>
                <div class="add_form">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off" enctype="multipart/form-data">
                        <!-- table_1 -->
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="list-home" role="tabpanel" aria-labelledby="list-home-list">
                                <p>封面照</p>
                                <div class="form-group">
                                    <img class="help-block thumbnail look_img" width="400px" style="display: none">
                                    <input class="up_banner banner" type="file" class="form-control-file" name="banner" id="banner" accept="image/*" />
                                </div>
                                <p>LOGO</p>
                                <div class="form-group">
                                    <img class="help-block thumbnail look_img" width="120px" style="display: none">
                                    <br>
                                    <input class="up_logo logo" type="file" class="form-control-file" name="logo" id="logo" accept="image/*" />
                                </div>
                                <p>備註</p>
                                <input class="alt" type="text" name="img_alt" id="img_alt" placeholder="建議輸入產品名稱">
                                <br>
                                <p>照片順序</p>
                                <input class="number" type="number" name="order_num" id="order_num" placeholder="順序為1-10">
                                <br>
                            </div>
                            <div class="tab-pane fade" id="list-profile" role="tabpanel" aria-labelledby="list-profile-list">
                                <p>情境照</p>
                                <div class="form-group form-group_live">
                                    <input class="up_img img" type="file" class="form-control-file" name="img[]" id="img" accept="image/*" />
                                </div>
                                <button type="submit" class="btn btn-secondary">添加</button>
                                <a href="/news.php" class="btn btn-light re_web">取消編輯</a>
                            </div>
                        </div>
                    </form>
                </div>
            <?php endif ?>
            </div>
            <div class="right_form">
                <table>
                    <div class="btn_action">
                        <a id="btn_delete" class="all_del btn btn-danger" style="display: none" href="/news-delete.php">批量刪除</a>
                    </div>
                    <thead>
                        <tr>
                            <th class="checkbox"><input type="checkbox" name=""></th>
                            <th class="see_img">封面照</th>
                            <th class="min_icon">按鈕LOGO</th>
                            <th class="products_name">備註</th>
                            <th class="num">順序</th>
                            <th class="th_btn">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($news as $item ): ?>
                        <tr>
                            <th><input type="checkbox" name="" data-id="<?php echo $item['id']; ?>"></th>
                            <th class="see_img"><img src="<?php echo $item['banner']; ?>"></th>
                            <th class="min_icon"><img src="<?php echo $item['min_icon']; ?>"></th>
                            <th class="products_name"><?php echo $item['img_alt']; ?></th>
                            <th class="num"><?php echo $item['order_num']; ?></th>
                            <th class="btn_ul">
                                <!-- <button type="button" class="btn btn-primary">編輯</button><button type="button" class="btn btn-danger">刪除</button> -->
                                <a href="news.php?id=<?php echo $item['id']; ?>"
                                    class="btn btn-primary"
                                >編輯</a>
                                <a href="news-delete.php?id=<?php echo $item['id']; ?>"
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
    <?php $current_page = 'news'; ?>
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
              // 照片更換時，更新成最新照片
      // 当文件域文件选择发生改变过后，本地预览选择的图片
    $('.banner').on('change', function () {
        var file = $(this).prop('files')[0]
    // 為這個文件對象創建一個 object URl
        var url = URL.createObjectURL(file)
        $(this).siblings('.thumbnail').attr('src', url).fadeIn()
    })
    $('.logo').on('change', function () {
        var file = $(this).prop('files')[0]
        var url = URL.createObjectURL(file)
        $(this).siblings('.thumbnail').attr('src', url).fadeIn()
    })
      //判斷是否已編輯過後
    var edit_two_live = '<?php echo $current_edit_news['news_live']; ?>'



    // 此段需驗證
    //驗證什麼時候會有此情形發生
    // ----------------------------
    if('<?php echo $current_edit_news_live ?>' && !edit_two_live){
        webControl = false;
        //取出值
        var liveV = '<?php echo $current_edit_news_live; ?>'
        liveV = liveV.split(',')
        liveVLong = liveV.length
        liveVDoLong = liveVLong - 1
        //判斷是否超過1
        if(liveVLong > 1){
            //生成第一個之後使用循環新增
            $('.edit_live').attr('src',liveV[0])
            for (var i = liveVDoLong; i > 0; i--) {
                if(i == (liveVDoLong)){
                    $('.edit_img').after('<img class="help-block thumbnail edit_live look_img" style="width: 400px" src="' + liveV[i] + '"></img><input class="up_img img" type="file" name="img[]" id="img" accept="image/*" ></input><input class="up_img img" type="file" name="img[]" id="img" accept="image/*" ></input>')
                }else{
                    $('.edit_img').after('<img class="help-block thumbnail edit_live look_img" style="width: 400px" src="' + liveV[i] + '"></img><input class="up_img img" type="file" name="img[]" id="img" accept="image/*" ></input>')
                }
            }
        }else{
            $('.edit_img').after('<input class="up_img img" type="file" name="img[]" id="img" accept="image/*" ></input>')
        }
      }
    // ----------------------------


      //上傳後判斷news_live是否是陣列
      //edit_two_live 為第二次編輯所有的變數
    edit_two_live_array = edit_two_live.split(',')
    edit_two_live_length = edit_two_live_array.length
    if(edit_two_live_length > 1){
        //取出值
        var liveV = '<?php echo $current_edit_news["news_live"]; ?>'
        liveV = liveV.split(',')
        liveVLong = liveV.length
        liveVDoLong = liveVLong - 1
        //判斷是否超過1
        if(liveVLong > 1){
            //生成第一個之後使用循環新增
            $('.edit_live').attr('src',liveV[0])
            for (var i = liveVDoLong; i > 0; i--) {
                if(i == (liveVDoLong)){
                    $('.edit_img').after('<img class="help-block thumbnail edit_live look_img" style="width: 400px" src="' + liveV[i] + '"></img><input class="up_img img" type="file" name="img[]" id="img" accept="image/*" ></input><p class="alert alert-danger delbutn" data-del_num='+ i +'>刪除</p><input class="up_img img" type="file" name="img[]" id="imgsaasasas" accept="image/*" ></input>')
                }else{
                    //循環更新
                    $('.edit_img').after('<img class="help-block thumbnail edit_live look_img" style="width: 400px" src="' + liveV[i] + '"></img><input class="up_img img" type="file" name="img[]" id="img" accept="image/*" ></input><p class="alert alert-danger delbutn" data-del_num='+ i +'>刪除</p>')
                }
            }
        }else{
            $('.edit_img').after('<input class="up_img img" type="file" name="img[]" id="img" accept="image/*" ></input>')
        }
    }else{
            $('.edit_img').after('<input class="up_img img" type="file" name="img[]" id="img" accept="image/*" ></input>')
    }
        //生成照片及圖片
    $('.form-group_live').on('change', '.img', function () {
        var inputNum = $('.img').length
        var inputNowNum = $('.img').index(this) + 1
        if (inputNum === inputNowNum){
            var file = $(this).prop('files')
            var nowImg = $(this)
            if(file){
                var url = URL.createObjectURL(file[0])
                nowImg.before('<img class="help-block thumbnail edit_live look_img" style="width: 400px" src="' + url + '"></img>').after('<p class="alert alert-danger delbutn">刪除</p><input class="up_img img form-control-file" type="file" name="img[]" id="img" accept="image/*" ></input>')

            }
        }else{
            var file = $(this).prop('files')[0]
            var url = URL.createObjectURL(file)
            $(this).prev('.thumbnail').attr('src', url).fadeIn()
        }
    })
    //刪除鈕動作
    $('.form-group').on('click','.delbutn',function () {
        var del_num = $('.delbutn').index(this)
        var now_del_num = del_num + 1
        //獲取刪除鈕上的id數字
        var now_del_data_num = $(this).data('del_num')
        $('.edit_live').eq(now_del_num).remove()
        $('.up_img').eq(now_del_num).remove()
        $('.delbutn').eq(del_num).remove()
        if(now_del_data_num){
            $('.del_num').val($('.del_num').val() + now_del_data_num);
        }

    })
    </script>


</body>
</html>