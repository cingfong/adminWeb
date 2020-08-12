<?php

require_once '../outer/config.php';

$connect = new mysqli(LOP_DB_HOST, LOP_DB_USER, LOP_DB_PASS, LOP_DB_NAME);

if ($connect->connect_error) {
    die("連線失敗: " . $connect->connect_error);
}
// echo "連線成功";

//設定連線編碼，防止中文字亂碼
$connect->query("SET NAMES 'utf8'");

//選擇資料表user，條件是欄位id = 1的

// $selectSql = "SELECT * FROM";
$selectSql = "SELECT * FROM news";
//呼叫query方法(SQL語法)
$memberData = $connect->query($selectSql);
//有資料筆數大於0時才執行
$arr = array();

if ($memberData->num_rows > 0) {
//讀取剛才取回的資料
    $show = array();
    while ($row = $memberData->fetch_assoc()) {
        foreach($row as $key => $value){
            if(unserialize($value)){
                $rowV = unserialize($value);
                $row[$key] = $rowV;
            }
        };
        $news_live_img_array = explode(",",$row['news_live']);
            $news_total_live_img = Array();
        foreach ($news_live_img_array as $key => $value) {
            $news_address_live_img = $value;
            array_push($news_total_live_img , $news_address_live_img);
        };
        $row['news_live'] = $news_total_live_img;
        $show[] = array(
                    'banner'=>$row['banner'],
                    'min_icon'=>$row['min_icon'],
                    'news_live'=>$row['news_live'],
                    'order_num'=>$row['order_num'],
                    'img_alt'=>$row['img_alt']);
    };
} else {
    echo '0筆資料';
};

echo json_encode($show,JSON_UNESCAPED_UNICODE);
