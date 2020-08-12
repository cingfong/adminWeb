<?php

require_once 'function.php';

//判斷用戶是否登入，一定是最優先
lop_get_current_user();

//判斷是否為需要編輯的數據
//===========================
function edit_product () {
    global $current_edit_product;
    //當前時間戳
    $name_data = time();
    //接收並保存
    $id = $current_edit_product['id'];
    //所有文字，空值則以舊值有則採取新值
    $product_categories = empty($_POST['categories']) ? $current_edit_product['catena_id'] : $_POST['categories'];
    $product_kind = empty($_POST['kind']) ? $current_edit_product['kind_id'] : $_POST['kind'];
    $product_name = empty($_POST['product_name']) ? $current_edit_product['name'] : $_POST['product_name'];
    $product_material = empty($_POST['product_material']) ? $current_edit_product['material'] : $_POST['product_material'];
    $product_spec = empty($_POST['product_spec']) ? $current_edit_product['spec'] : $_POST['product_spec'];
    $product_price = empty($_POST['product_price']) ? $current_edit_product['price'] : $_POST['product_price'];
    $product_status = empty($_POST['status']) ? $current_edit_product['status_id'] : $_POST['status'];

    //因顏色直接賦予值故不需要只用取代
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


    //顏色內照片上傳方式
    include_once 'api/upload.func_product_color.php';
    $old_product_img = lop_fetch_one('select product_img from products where id = ' . $_GET['id'])['product_img'];
    $old_product_img_array = explode('&&',$old_product_img);
    $old_product_img_array_length = count($old_product_img_array);
    for ($img_i = 0; $img_i < $old_product_img_array_length; $img_i++) {
        if($old_product_img_array[$img_i]){
            $old_product_img_array_array = explode(',',$old_product_img_array[$img_i]);
            //name 從1開始
            $i = $img_i + 1;
            $product_del_num_val = $_POST['color' . $i . '_del_num'];
            $product_del_num_array = str_split($product_del_num_val);

            if(!empty($product_del_num_array)){

                foreach($product_del_num_array as $del_num_value){

                    unset($old_product_img_array_array[$del_num_value]);

                }
            }
            $old_product_img_array_array_new = array_values($old_product_img_array_array);
        }
        //需先將字串變成值
        $old_product_img_array_str = implode(',',$old_product_img_array_array_new);
        //將刪除完的陣列變成字串回填至原本的變數中
        $old_product_img_array[$img_i] = $old_product_img_array_str;
    }
    //全局變數
    for ($colI=1; $colI < 10; $colI++) {
        // 交給空值時使用的數字
        $colI_null_num = $colI - 1;
        if(!empty($_FILES["color".$colI."_img"])){
            $files_product_num = 0;
            $color_img_files = get_color_img_Files($colI);
            //解析舊資料變成陣列以及拿出長度值
            $old_product_value = $old_product_img_array[$colI_null_num];
            $old_product_array = explode(',',$old_product_value);
            $old_product_length = count($old_product_array);
            foreach ($color_img_files as $fileInfo){
                $product_res = upload_color_img_File($fileInfo);
                // echo $product_res['mes'] . '<br>';
                if (!empty($product_res['dest'])){
                    $uploadFilesProduct[] = $product_res['dest'];
                    // echo $uploadFilesProduct;
                }elseif($old_product_length > $files_product_num){
                    $uploadFilesProduct[] = $old_product_array[$files_product_num];
                }
                $files_product_num = $files_product_num +1;
            }
            $uploadFilesList = implode(',',$uploadFilesProduct);
            $uploadFilesListArray[] = $uploadFilesList;
            unset($uploadFilesProduct);
            unset($uploadFilesList);
            //顏色預設最多只能10個
        }else if($colI == 9){
        }
    }
    $product_img = implode('&&',$uploadFilesListArray);


    //商品情境照多個檔案上傳方式
    include_once 'api/upload.func_product_live.php';
    $live_files = get_live_Files();
    //--
    $old_product_live = lop_fetch_one('select live_img from products where id = ' . $_GET['id'])['live_img'];
    $old_product_live_array =  explode(",",$old_product_live);
    //獲取有被刪除的值
    $live_del_num_val = $_POST['live_del_num'];
    $live_del_num_array = str_split($live_del_num_val);
    if(!empty($live_del_num_array)){
        foreach ($live_del_num_array as $value) {
            unset($old_product_live_array[$value]);
        }
    }
    $old_product_live_array = array_values($old_product_live_array);
    $old_product_live_length = count($old_product_live_array);
    $files_live_num = 0;
    //--
    foreach ($live_files as $fileInfo_live){
        $resLive = upload_live_File($fileInfo_live);
        // echo $resLive['mes'] . '<br>';
        if (!empty($resLive['dest'])){
            $uploadFilesLive[] = $resLive['dest'];
        }elseif($old_product_live_length > $files_live_num){
            $uploadFilesLive[] = $old_product_live_array[$files_live_num];
        }
        $files_live_num = $files_live_num + 1;
    }
        $live_img = implode(',',$uploadFilesLive);
    //商品頭貼照片驗證
    if (empty($_FILES['one_img']['error'])) {
        $temp_file_one_img = $_FILES['one_img']['tmp_name'];
        $target_file_one_img = './static/img/product/' . $name_data . $_FILES['one_img']['name'];
        //承上
        move_uploaded_file($temp_file_one_img, $target_file_one_img);
    }
        // 接收数据
    $one_img = isset($target_file_one_img) ? $target_file_one_img : $current_edit_product['one_img'];
        //更新成最新數據
        //edit不用更新當前頁面資料故而註解
        // $current_edit_product['one_img'] = $one_img;
        // $current_edit_product['order_num'] = $order_num;
        // $current_edit_product['img_alt'] = $img_alt;

//-------------
    $rows = lop_execute("update products set name = '{$product_name}', material = '{$product_material}', spec = '{$product_spec}', price = '{$product_price}', color = '{$product_color}', catena_id = '{$product_categories}', kind_id = '{$product_kind}', status_id = '{$product_status}', one_img = '{$one_img}', live_img = '{$live_img}', product_img = '{$product_img}' where id = $id");
    $GLOBALS['success'] = !$row > 0;
    function upmissData () {
        echo "<script>alert('編輯失敗')</script>";
    }
    function upSuccessData () {
        echo "<script>
                alert('編輯成功');
                location.href='/all_products.php?page=1'
            </script>";
    }
    !$rows <= 0 ? upmissData() : upSuccessData();
}

//判斷是編輯還是添加
if (empty($_GET['id'])) {

    header('Location: /add_products.php');
} else {
  //編輯
  //客戶端通過 URL 傳遞了一個 ID => 客戶端是要來拿一個修改數據表單
  // => 客戶端是要來拿一個修改數據的表單
  // => 需要拿到用戶想要修改的數據
  $current_edit_product =  lop_fetch_one('select * from products where id = ' . $_GET['id']);
  if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    edit_product();
  }else{
    //新增在另一頁面，故而註解
    // $current_edit_product_live =  lop_fetch_one('select news_live from news where id = ' . $_GET['id'])['news_live'];
  }
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
    <link rel="stylesheet" type="text/css" href="static/css/edit_products.css">
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
        <?php if (isset($current_edit_product)): ?>
            <h3>編輯商品《 <?php echo $current_edit_product['name']; ?> 》 </h3>
            <div class="list-group title" id="myList" role="tablist">
                <a class="title_li active" data-toggle="list" href="#home" role="tab">商品資料</a>
                <a class="title_li " data-toggle="list" href="#profile" role="tab">商品顏色</a>
                <a class="title_li product_img_title" data-toggle="list" href="#messages" role="tab">商品預覽圖</a>
                <a class="title_li " data-toggle="list" href="#settings" role="tab">商品相冊</a>
            </div>
            <div class="table">
                <form class="tab-content" action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $current_edit_product['id']?>" method="post" autocomplete="off" enctype="multipart/form-data">
                    <div class="tab-pane active" id="home" role="tabpanel">
                        <div class="from">
                            <p>商品圖*</p>
                            <div class="form-group">
                                <img class="help-block thumbnail look_one_img"
                                    width="400px"
                                    src="<?php echo $current_edit_product['one_img']; ?>">
                                <br>
                                <input class="up_img up_one_img one_img"        type="file"
                                        class="form-control-file"
                                        name="one_img"
                                        id="one_img"
                                        accept="image/*">
                            </div>
                        </div>
                        <p>商品系列*</p>
                        <div class="form-group">
                            <select name="categories">
                                <?php foreach ($categories as $item): ?>
                                    <option
                                        <?php echo $current_edit_product['catena_id'] == $item['product_id'] ? ' selected' : '' ?>
                                        value="<?php echo $item['product_id'] ?>"
                                    >
                                    <?php echo $item['name'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <p>商品分類*</p>
                        <div class="form-group">
                            <select name="kind">
                                <?php foreach ($kind as $item): ?>
                                    <option
                                        <?php echo $current_edit_product['kind_id'] == $item['product_id'] ? ' selected' : '' ?>
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
                                    value="<?php echo $current_edit_product['name']; ?>"
                            >
                        </div>
                        <p>商品材質*</p>
                        <div class="form-group">
                            <input class="alt" type="text"
                                    name="product_material"
                                    id="product_material"
                                    value="<?php echo $current_edit_product['material']; ?>"
                            >
                        </div>
                        <p>商品規格*</p>
                        <div class="form-group">
                            <input class="alt" type="text"
                                    name="product_spec"
                                    id="product_spec"

                                    value='<?php echo $current_edit_product['spec']; ?>'
                            >
                    <!--因為spec裡面有雙引號所以只有這一個外面是使用單引號 -->
                        </div>
                        <p>產品價格*</p>
                        <div class="form-group">
                            <input class="alt" type="text"
                                    name="product_price"
                                    id="product_price"
                                    value=<?php echo $current_edit_product['price']; ?>
                            >
                        </div>
                        <p>產品狀態*</p>
                        <div class="form-group">
                            <select name="status">
                                <?php foreach ($status as $item): ?>
                                    <option
                                        <?php echo $current_edit_product['status_id'] == $item['product_id'] ? ' selected' : '' ?>
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
                                <br>
                                <p class="color_num">顏色 1</p>
                                <input class="product_color alt" type="text"
                                        name="product_color[]"
                                        id="product_color"
                                        placeholder="請輸入色票號碼"
                                >
                                <p style="display: none" class='alert alert-danger delbutn'>刪除</p>
                                <p class='error_color'>請輸入正確的色碼</p>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane tab-product_img" id="messages" role="tabpanel">
                        <div class="from color_photo">
                            <p class="color_title">照片</p>
                            <div class="photo_file">
                                <p>顏色 1 照片</p>
                                <!-- <img class="help-block product_thumbnail look_product_img" width="400px" style="display: none"> -->
                                <!-- <br> -->
                                <input class="product_img_file product_file_listOne" type="file" name="color1_img[]" id="product_img_file" accept="image/*" >
                                <input type="text" name="color1_del_num" id="color_del_num" class="color_del_num">
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="settings" role="tabpanel">
                        <div class="from live_photo">
                            <p class="live_title">商品情境照</p>
                            <div class="live_photo_file">
                                <p>照片</p>
                                <!-- <img class="help-block product_live_img look_live_img" width="400px"> -->
                                <img class="help-block look_live_img product_live_img" width="400px" src="<?php echo $current_edit_product['live_img']; ?>"></img>
                                <input class="edit_live_img up_live_img img live_img" type="file" class="form-control-file" name="live_img[]" id="live_img" accept="image/*" />
                                <input type="text" class="live_del_num" id="live_del_num" name="live_del_num">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary send">送出</button>
                        <a href="/all_products.php" class="btn btn-light re_web">取消編輯</a>
                    </div>
                </form>
            </div>
        <!-- 理應不應該有以下這段html判斷 -->
        <?php else: ?>

            <?php endif ?>
        </div>
    </div>
    <?php $current_page = 'all_pro'; ?>
    <script src="static/venders/jquery/jquery-1.12.4.js"></script>
    <!-- <script src="static/venders/popper/popper.min.js"></script> -->
    <script src="static/venders/bootstrap-4.2.1-dist/js/bootstrap.js"></script>
    <script src="static/js/index.js"></script>
    <?php include 'inc/slides.php'; ?>
    <script>
    //獲取資料庫中color且實體化
        var edit_color = '<?php echo $current_edit_product['color']; ?>'
    if(edit_color){
        edit_color_Ar = edit_color.split(',')
        edit_color_length = edit_color_Ar.length;
        // 判斷最後一個顏色數字
        var edit_color_length_last = edit_color_length+1
        $('.color_num').html("顏色 "+ edit_color_length_last+" ")
        for(var i = 0; i < edit_color_length; i++){
            // 當下編號
            var color_num = i+1
            if(i == 0){
                $('.color_list').eq(i).before("<div class='color_list'><br><p class='color_num'>顏色 " + color_num + " </p><div class='blockColor'></div><input class='product_color alt' type='text' name='product_color[]' id='product_color' value='"+edit_color_Ar[i]+"'><p class='error_color'>請輸入正確的色碼</p></div>")
            }else{
                $('.color_list').eq(i).before("<div class='color_list'><br><p class='color_num'>顏色 " + color_num + " </p><div class='blockColor'></div><input class='product_color alt' type='text' name='product_color[]' id='product_color' value='"+edit_color_Ar[i]+"'><p style='display: inline-block' class='alert alert-danger delbutn'>刪除</p><p class='error_color'>請輸入正確的色碼</p></div>")
            }
            if(edit_color_Ar[i][0] === '#'){
                $('.color_list').eq(i).children('.blockColor').css({"opacity":"1",
                                                "background-color": edit_color_Ar[i]})
            }else if(edit_color_Ar[i][0] === 'h'){
                $('.color_list').eq(i).children('.blockColor').css({"opacity":"1",
                                                "background-image": "url("+edit_color_Ar[i]+")",
                                                "background-size": "100%",
                                                "background-position": "center"})
            }
        }
    }

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
                nowImg.before('<img class="help-block product_thumbnail look_product_img" style="width: 500px" src="' + url + '"></img>').after('<p class="alert alert-danger product_img_delbutn">刪除</p><input class="product_img_file" type="file" name="color' + thisDadyNum + '_img[]" id="product_img_file" accept="image/*" ></input>')
            }
        }else{
            var file = $(this).prop('files')[0]
            var url = URL.createObjectURL(file)
            $(this).prev('.look_product_img').attr('src', url).fadeIn()
        }
    })
        //情境照生成照片及圖片
    $('.live_photo_file').on('change', '.up_live_img', function () {
        var inputLiveNum = $('.up_live_img').length
        var inputNowLiveNum = $('.up_live_img').index(this) + 1
        if (inputLiveNum === inputNowLiveNum){
            var file = $(this).prop('files')
            var nowImg = $(this)
            if(file){
                var url = URL.createObjectURL(file[0])
                nowImg.before('<img class="help-block product_live_img look_live_img" style="width: 400px" src="' + url + '"></img>').after('<p class="alert alert-danger product_img_delbutn">刪除</p><input class="up_live_img img live_img form-control-file" type="file" name="live_img[]" id="live_img" accept="image/*" ></input>')

            } 
        }else{
            var file = $(this).prop('files')[0]
            var url = URL.createObjectURL(file)
            $(this).prev('.product_live_img').attr('src', url).fadeIn()
        }
    })
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
        //控制第一次執行舊資料更新的開關
        var edit_product_old_control = true
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
                $('.photo_file').eq(photoFileLong - 1).after("<div class='photo_file'><hr><p>顏色 " + j + "照片</p><input class='product_img_file product_file_listOne' type='file' name='color" + j + "_img[]' id='product_img_file' acceot='image/*'><input type='text' name='color" + j + "_del_num' id='color_del_num' class='color_del_num'></div>")

            }
        // }
        }else if(colorNumLong > 0 && photoFileLong > colorNumLong){
            var colorGap = photoFileLong - colorNumLong
            for(var i = 0; i < colorGap; i++){
                //eq要-1
                $('.photo_file').eq(photoFileNum - i).remove()
            }
        }
        //獲取產品照片並分割實現化
        var edit_product_img = '<?php echo $current_edit_product['product_img']; ?>'
        if(edit_product_img && edit_product_old_control){
            edit_product_old_control = false
            var product_color_num = edit_product_img.split('&&')
            var product_color_length = product_color_num.length
            for(var i = 0; i < product_color_length; i++){
                var product_img_list = product_color_num[i]
                var product_img_list_Ar = product_img_list.split(',')
                var product_img_list_length = product_img_list_Ar.length
                //name命名使用
                var k = i + 1
                for(var j = 0; j < product_img_list_length; j++){
                    if(j > 0){
                        $('.photo_file').eq(i).children('.product_file_listOne').before('<img class="help-block product_thumbnail look_product_img" style="width: 500px" src="' + product_img_list_Ar[j] + '"></img>').before('<input class="product_img_file" type="file" name="color' + k + '_img[]" id="product_img_file" accept="image/*" ></input><p class="alert alert-danger product_img_delbutn" data-product_img_del_num='+ j +'>刪除</p>')
                    }else{
                        $('.photo_file').eq(i).children('.product_file_listOne').before('<br><img class="help-block product_thumbnail look_product_img" style="width: 500px" src="' + product_img_list_Ar[j] + '"></img>').before('<input class="product_img_file" type="file" name="color' + k + '_img[]" id="product_img_file" accept="image/*" ></input>')

                    }
                }
            }

        }
    })

    //情境照取出資料庫資料並顯示
    var live_img = '<?php echo $current_edit_product['live_img']; ?>'
    var live_img_array = live_img.split(',')
    var live_img_length = live_img_array.length
    if(live_img_length > 0 && live_img){
        //取出值
        liveVDoLong = live_img_length - 1
        //判斷是否超過1
        if(live_img_length > 1){
            //生成第一個之後使用循環新增
            $('.product_live_img').attr('src',live_img_array[0])
            for (var i = liveVDoLong; i > 0; i--) {
                if(i == (liveVDoLong)){
                    $('.edit_live_img').after('<img class="help-block product_live_img look_live_img" style="width: 400px" src="' + live_img_array[i] + '"></img><input class="up_live_img img live_img" type="file" name="live_img[]" id="live_img" accept="image/*" ></input><p class="alert alert-danger live_delbutn" data-live_del_num='+ i +'>刪除</p><input class="up_live_img img live_img" type="file" name="live_img[]" id="live_img" accept="image/*" ></input>')
                }else{
                    $('.edit_live_img').after('<img class="help-block product_live_img look_live_img" style="width: 400px" src="' + live_img_array[i] + '"></img><input class="up_live_img img live_img" type="file" name="live_img[]" id="live_img" accept="image/*" ></input><p class="alert alert-danger live_delbutn" data-live_del_num='+ i +'>刪除</p>')
                }
            }
        }else{
            $('.edit_live_img').after('<input class="up_live_img img live_img" type="file" name="live_img[]" id="live_img" accept="image/*" ></input>')
        }
    }else{
            // $('.edit_live_img').after('<input class="up_live_img img live_img" type="file" name="live_img[]" id="live_img" accept="image/*" ></input>')

    }
    //產品照刪除鈕
    $('.live_photo_file').on('click','.live_delbutn',function () {
        var del_num = $('.live_delbutn').index(this)
        var now_del_num = del_num + 1
        //獲取刪除鈕上的id數字
        var now_del_data_num = $(this).data('live_del_num')
        $('.look_live_img').eq(now_del_num).remove()
        $('.up_live_img').eq(now_del_num).remove()
        $('.live_delbutn').eq(del_num).remove()
        if(now_del_data_num){
            $('.live_del_num').val($('.live_del_num').val() + now_del_data_num);
        }
    })
    $('.color_photo').on('click','.product_img_delbutn',function () {
        //獲取刪除鈕上的id數字
        var now_del_data_num = $(this).data('product_img_del_num')
        var now_daddy_num = $(this).parent().index()
        var del_num = $(this).parent().children('.product_img_delbutn').index(this)
        var now_del_num = del_num + 1
        $(this).parent().children('.look_product_img').eq(now_del_num).remove()
        $(this).parent().children('.product_img_file').eq(now_del_num).remove()
        $(this).parent().children('.product_img_delbutn').eq(del_num).remove()
        var now_daddy_num_textV = now_daddy_num - 1
        if(now_del_data_num){
            var now_text = $('.photo_file').eq(now_daddy_num_textV).children('.color_del_num')
            now_text.val(now_text.val() + now_del_data_num)
        }
})
    </script>
</body>
</html>