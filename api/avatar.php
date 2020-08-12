<?php


//根據用戶信箱獲取用戶頭像
// email => image

require_once '../outer/config.php';


// 1.接收傳送過來的信箱
if (empty($_GET['email'])){
    exit('缺少必要參數');
}
$email = $_GET['email'];


// 2. 查詢對應的頭像地址
$conn = mysqli_connect(LOP_DB_HOST, LOP_DB_USER, LOP_DB_PASS, LOP_DB_NAME);
if(!$conn) {
    exit('連結數據庫失敗');
}
$res = mysqli_query($conn, "select avatar from users where email = '{$email}' limit 1;");
if (!$res) {
    exit('查詢失敗');
}
$row = mysqli_fetch_assoc($res);

echo $row['avatar'];