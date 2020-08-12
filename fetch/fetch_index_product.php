<?php

require_once '../outer/config.php';

$connect = new mysqli(LOP_DB_HOST, LOP_DB_USER, LOP_DB_PASS, LOP_DB_NAME);

if ($connect->connect_error) {
    die("連線失敗: " . $connect->connect_error);
}
// echo "連線成功";

//設定連線編碼，防止中文字亂碼
$connect->query("SET NAMES 'utf8'");

//若資料補齊則可使用以下 sql 否則會去除掉沒有相等的產品名稱
$selectSql = "select 
                in_product.order_num,
                in_product.img,
                in_product.img_alt,
                products.id
                from in_product
                inner join products on in_product.img_alt = products.name";

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
        $show[] = array('img'=>$row['img'],'img_alt'=>$row['img_alt'],'order_num'=>$row['order_num'],'id'=>$row['id']);
    };
} else {
    echo '0筆資料';
};

        echo json_encode($show,JSON_UNESCAPED_UNICODE);
