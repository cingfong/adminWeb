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
$selectSql = "SELECT * FROM slides";
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
        $show[] = array('slide_img'=>$row['slide_img'],'order_num'=>$row['order_num']);
    };
} else {
    echo '0筆資料';
};

        echo json_encode($show,JSON_UNESCAPED_UNICODE);
