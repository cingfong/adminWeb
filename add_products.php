<?php

require_once 'function.php';

//判斷用戶是否登入，一定是最優先
lop_get_current_user();

//判斷是否為需要編輯的數據
//===========================
function add_products(){
    //取出編號查詢有無重複
    // 當前時間戳
    $name_data = time();
    if(empty($_POST['product_name']) ||
        empty($_POST['product_material']) ||
        empty($_POST['product_spec']) ||
        empty($_POST['product_price']) ||
        empty($_POST['product_color'][0]) ||
        empty($_FILES['one_img']) ||
        empty($_FILES['color1_img']['name'][0])
    ){
        $GLOBALS['message'] = '請完整填寫表單';
        return;
    }else if(empty($_FILES['one_img']['error']) ||
            // empty($_FILES['color1_img']['error']) ||
            empty($_FILES['live_img']['error'])){
        // PHP 自動接收客戶端上傳的文件到一個臨時目錄
        $temp_file_one_img = $_FILES['one_img']['tmp_name'];
        // 由臨時目錄轉存到指定目錄中
        $target_file_one_img = './static/img/product/' . $name_data . $_FILES['one_img']['name'];

        if (move_uploaded_file($temp_file_one_img, $target_file_one_img)) {
            $image_file = './static/img/product/' . $name_data . $_FILES['one_img']['name'];
        }

        //情境照多個檔案上傳方式
        include_once 'api/upload.func_product_live.php';
        $live_files = get_live_Files();
        foreach ($live_files as $fileInfo_live){
            $resLive = upload_live_File($fileInfo_live);
            if (!empty($resLive['dest'])){
                $uploadFilesLive[] = $resLive['dest'];
            }
        }
        //顏色內照片上傳方式
        include_once 'api/upload.func_product_color.php';
//全局變數
for ($colI=1; $colI < 10; $colI++) {
    if(!empty($_FILES["color".$colI."_img"])){
        $color_img_files = get_color_img_Files($colI);
        foreach ($color_img_files as $fileInfo){
            $product_res = upload_color_img_File($fileInfo);
            if (!empty($product_res['dest'])){
                $uploadFilesProduct[] = $product_res['dest'];
            }
        }
        $uploadFilesList = implode(',',$uploadFilesProduct);
    $uploadFilesListArray[] = $uploadFilesList;
    unset($uploadFilesProduct);
    unset($uploadFilesList);
    //預設只能傳10個顏色
    }else if($colI == 9){
        // echo $uploadFilesList;
        // die();
    }
}
$product_img = implode('&&',$uploadFilesListArray);

        //color色碼陣列 變字串
        $product_color;
        $product_color_array = $_POST['product_color'];
        foreach ($product_color_array as $color_list){
            if(!empty($product_color)){
                if(!empty($color_list)){
                    $product_color = $product_color.','.$color_list;
                }
            }else{
                $product_color = $color_list;
            }
        }
        $live_img = empty($uploadFilesLive) ? '' : implode(',',$uploadFilesLive);


        $product_name = $_POST['product_name'];
        $product_material = $_POST['product_material'];
        $product_spec = $_POST['product_spec'];
        $product_price = $_POST['product_price'];
        $categories_id = $_POST['categories'];
        $kind_id = $_POST['kind'];
        $status_id = $_POST['status'];
        $one_img = isset($target_file_one_img) ? $target_file_one_img : '';

        $rows = lop_execute("insert into products values('{$product_name}', '{$product_material}', '{$product_spec}', '{$product_price}', '{$product_color}', '{$categories_id}', '{$kind_id}', '{$status_id}', null, '{$one_img}', '{$live_img}', '{$product_img}')");
        $GLOBALS['success'] = !$row > 0;
    function upmissData () {
        echo "<script>alert('新增失敗')</script>";
    }
    function upSuccessData () {
        echo "<script>
                alert('新增成功');
                location.href='/all_products.php?page=1'
            </script>";
    }
        !$rows <= 0 ? upmissData() : upSuccessData();
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
    $img_alt = empty($_POST['img_alt']) ? $current_edit_category['img_alt'] : $_POST['img_alt'];

    //檢查順序編號
    if($_POST['order_num'] > 10 || $_POST['order_num'] < 1 ){
        $GLOBALS['message'] = '請輸入正確編號';
        return;
    }
    $order_num = empty($_POST['order_num']) ? $current_edit_category['order_num'] : $_POST['order_num'];
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
            $live_files = getFiles();
            //取出 live value 值且變成陣列
    $old_news_live =  lop_fetch_one('select news_live from news where id = ' . $_GET['id'])['news_live'];
    $old_news_live_array =  explode(",",$old_news_live);
    //因為與直接套用 array 編號所以數字需減 1 後面取代才不會出現錯誤
    $old_news_live_leng = count($old_news_live_array);
            //計算 for 第幾位
            $files_num = 0;
            foreach ($live_files as $fileInfo){
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
        // 接收数据
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
    add_products();
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

$kind = lop_fetch_all('select * from kind');

$categories = lop_fetch_all('select * from catena');

$status = lop_fetch_all('select * from status');

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
    <link rel="stylesheet" type="text/css" href="static/css/add_products.css">
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
            <!-- 此頁面只有新增功能 -->
        <?php if (isset($current_edit_news)): ?>
        <?php else: ?>

            <h3>新增商品 </h3>
            <div class="list-group title" id="myList" role="tablist">
                <a class="title_li active" data-toggle="list" href="#home" role="tab">商品資料</a>
                <a class="title_li " data-toggle="list" href="#profile" role="tab">商品顏色</a>
                <a class="title_li product_img_title" data-toggle="list" href="#messages" role="tab">商品預覽圖</a>
                <a class="title_li " data-toggle="list" href="#settings" role="tab">商品相冊</a>
            </div>
            <div class="table">
                <form class="tab-content" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off" enctype="multipart/form-data">
                    <div class="tab-pane active" id="home" role="tabpanel">
                        <p>商品圖*</p>
                        <div class="form-group">
                            <img class="help-block thumbnail look_one_img" width="400px" style="display: none">
                            <br>
                            <input class="up_img up_one_img one_img" type="file" class="form-control-file" name="one_img" id="one_img" accept="image/*" />
                        </div>
                        <p>商品系列</p>
                        <div class="form-group">
                            <select name="categories">
                                <!-- <option value=''>無系列</option> -->
                                <?php foreach ($categories as $item): ?>
                                    <option
                                        value="<?php echo $item['product_id'] ?>"
                                    >
                                    <?php echo $item['name'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <p>商品分類</p>
                        <div class="form-group">
                            <select name="kind">
                                <?php foreach ($kind as $item): ?>
                                    <option
                                        value="<?php echo $item['product_id'] ?>"
                                    >
                                    <?php echo $item['name'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <p>商品名稱*</p>
                        <div class="form-group">
                            <input class="alt" type="text"
                                    name="product_name"
                                    id="product_name"
                                    placeholder="建議輸入商品名稱"
                            >
                        </div>
                            <p>商品材質*</p>
                        <div class="form-group">
                            <input class="alt" type="text"
                                    name="product_material"
                                    id="product_material"
                                    placeholder="輸入商品材質"
                            >
                        </div>
                        <p>商品規格*</p>
                        <div class="form-group">
                            <input class="alt" type="text"
                                    name="product_spec"
                                    id="product_spec"
                                    placeholder="輸入商品規格"
                            >
                        </div>
                        <p>產品價格*</p>
                        <div class="form-group">
                            <input class="alt" type="text"
                                    name="product_price"
                                    id="product_price"
                                    placeholder="建議輸入產品價格"
                            >
                        </div>
                        <p>產品狀態*</p>
                        <div class="form-group">
                            <select name="status">
                                <!-- <option value=''>無系列</option> -->
                                <?php foreach ($status as $item): ?>
                                    <option
                                        value="<?php echo $item['product_id'] ?>"
                                    >
                                    <?php echo $item['name'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="tab-pane" id="profile" role="tabpanel">
                        <p>商品顏色(最多10色)</p>
                        <div class="form-group form-group_color">
                            <div class="color_list">
                                <p class="color_num">顏色 1</p>
                                <input class="product_color alt" type="text"
                                        name="product_color[]"
                                        id="product_color"
                                        placeholder="請輸入色票號碼或圖片"
                                >
                                <p class='error_color'>請輸入正確的色碼</p>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane tab-product_img" id="messages" role="tabpanel">
                        <div class="from color_photo">
                            <p class="color_title">商品照片</p>
                            <div class="photo_file">
                                <p>顏色1 照片</p>
                                <!-- <img class="help-block product_thumbnail look_product_img" width="400px" style="display: none"> -->
                                <!-- <br> -->
                                <input class="product_img_file" type="file" name="color1_img[]" id="product_img_file" accept="image/*" >
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="settings" role="tabpanel">
                        <div class="from live_photo">
                            <p class="live_title">商品情境照</p>
                            <div class="live_photo_file">
                                <p>照片</p>
                              <!--   <img class="help-block product_live_img look_live_img" width="400px" style="display: none">
                                <br> -->
                                <input class="up_live_img img live_img" type="file" class="form-control-file" name="live_img[]" id="live_img" accept="image/*" />
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary send">送出</button>
                    </div>
                </form>
            </div>

            <?php endif ?>
        </div>
    </div>
    <?php $current_page = 'add_pro'; ?>
    <script src="static/venders/jquery/jquery-1.12.4.js"></script>
    <!-- <script src="static/venders/popper/popper.min.js"></script> -->
    <script src="static/venders/bootstrap-4.2.1-dist/js/bootstrap.js"></script>
    <script src="static/js/index.js"></script>
    <?php include 'inc/slides.php'; ?>
    <script>
        //顏色產品照的生成照片及圖片
    $('.color_photo').on('change', '.product_img_file', function () {
        //因配合下面的 eq 選擇元素 因而 -1 且牽扯到父元素則由1開始
        var thisDadyNum = $(this).parent('.photo_file').index()
        //因配合下面的 eq 選擇元素 因而 -1 且牽扯到父元素則由1開始
        var thisDadyNumEq = $(this).parent('.photo_file').index() - 1
        //因所取得的值從0開始 而需要 +1
        var inputNum = $('.photo_file').eq(thisDadyNumEq).children('.product_img_file').index(this) + 1
        var inputNowNum = $('.photo_file').eq(thisDadyNumEq).children('.product_img_file').length
        if (inputNum === inputNowNum){
            var file = $(this).prop('files')
            var nowImg = $(this)
            if(file){
                var url = URL.createObjectURL(file[0])
                nowImg.before('<br><img class="help-block product_thumbnail look_product_img" style="width: 500px" src="' + url + '"></img>').after('<input class="product_img_file" type="file" name="color' + thisDadyNum + '_img[]" id="product_img_file" accept="image/*" ></input>')
            }
        }else{
            var file = $(this).prop('files')[0]
            var url = URL.createObjectURL(file)
            $(this).prev('.look_product_img').attr('src', url).fadeIn()
        }
    })
        //情境照生成照片及圖片
    $('.live_photo_file').on('change', '.up_live_img', function () {
        var inputNum = $('.up_live_img').length
        var inputNowNum = $('.up_live_img').index(this) + 1
        if (inputNum === inputNowNum){
            var file = $(this).prop('files')
            var nowImg = $(this)
            if(file){
                var url = URL.createObjectURL(file[0])
                nowImg.before('<img class="help-block product_live_img look_live_img" style="width: 400px" src="' + url + '"></img>').after('<input class="up_live_img img live_img form-control-file" type="file" name="live_img[]" id="live_img" accept="image/*" ></input>')

            }
        }else{
            var file = $(this).prop('files')[0]
            var url = URL.createObjectURL(file)
            $(this).prev('.product_live_img').attr('src', url).fadeIn()
        }
    })
    //照片預覽
    // $('.color_photo').on('change', '.product_img_file', function () {
    //     var file = $(this).prop('files')[0]
    //     var url = URL.createObjectURL(file)
    //     $(this).siblings('.product_thumbnail').attr('src', url).fadeIn()
    // })
    //顏色內照片預覽圖
    $('.one_img').on('change', function () {
        var file = $(this).prop('files')[0]
        var url = URL.createObjectURL(file)
        $(this).siblings('.thumbnail').attr('src', url).fadeIn()
    })
        //生成，因還有圖片問題故不驗證
    $('.form-group_color').on('change', '.product_color', (function () {
        var colorNum = $('.product_color').length
        var colorNowNum = $('.product_color').index(this) + 1
        var blockColorNowNum = colorNowNum + 1
        var colorValue = $(this).val();
        var colorValueArray = colorValue.split('')
        var colorValue0_4Array = colorValueArray.slice(0,4)
        var colorValue0_4String = colorValue0_4Array.join('')

        if(colorValueArray[0] === '#' && colorValueArray.length === 7 || colorValue0_4String === 'http'){
            $('.error_color').css('display','none')
        }else{
            $(this).nextAll('.error_color').css('display','block')
            return
        }
        //生成下一個色碼填空表
        if(colorNum === colorNowNum && colorValue){
            $(this).parent().after("<div class='color_list'><br><p class='color_num'>顏色 " + blockColorNowNum + "</p><input class='product_color alt' type='text' name='product_color[]' id='product_color' placeholder='請輸入色票號碼'><p style='display: none' class='alert alert-danger delbutn'>刪除</p><p class='error_color'>請輸入正確的色碼</p></div>")
            $(this).before("<div class='blockColor'></div>")
            // $(this).before("<div class='blockColor' style='background-color: " + colorValue + ";'></div>")
        }
        if(colorValueArray[0] === '#'){
            $(this).prev('.blockColor').css({"opacity":"1",
                                            "background-color": colorValue})
        }else if(colorValueArray[0] === 'h'){
            $(this).prev('.blockColor').css({"opacity":"1",
                                            "background-image": "url("+colorValue+")",
                                            "background-size": "100%",
                                            "background-position": "center"})
        }else{
            $(this).nextAll('.error_color').css('display','block')
        }
        $(this).next('.delbutn').css({"display":"inline-block"})
    }))
    //刪除顏色
    $('.form-group_color').on('click', '.delbutn', function () {
        var nowListNum = $(this).parent().index()
        $('.color_list').eq(nowListNum).remove()
        var listLength = $('.color_list').length
        for(var i = 0; i < listLength; i++){
            var j = i + 1
            $('.color_list').eq(i).children('.color_num').text("顏色 " + j )
        }
    })
    //自動生成商品預覽圖顏色數量
    $('.product_img_title').click(function () {
        //需少一個空白填入框
        var colorNumLong = $('.color_list').length - 1
        //eq 操作需再減1
        // var colorEqNum = colorNumLong - 1
        //file總數
        var photoFileLong = $('.photo_file').length
        //扣除一開始的上傳框
        var photoFileNum = photoFileLong - 1
        //顏色大於一個，且上傳筆數小於顏色總量
        if (colorNumLong > 1 && photoFileLong < colorNumLong) {
            var colorGap = colorNumLong - photoFileLong
            for(var i = 0; i < colorGap; i++){
                //要加上最一開始的1
                var j = colorNumLong - i
                $('.photo_file').eq(photoFileLong - 1).after("<div class='photo_file'><hr><p>顏色 " + j + "照片</p><input class='product_img_file' type='file' name='color" + j + "_img[]' id='product_img_file' acceot='image/*'></div>")

            }
        // }
        }else if(colorNumLong > 0 && photoFileLong > colorNumLong){
            var colorGap = photoFileLong - colorNumLong
            for(var i = 0; i < colorGap; i++){
                //eq要-1
                $('.photo_file').eq(photoFileNum - i).remove()
            }
        }
    })
    </script>
</body>
</html>