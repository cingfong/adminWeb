<?php

require_once '../outer/config.php';

$connect = new mysqli(LOP_DB_HOST, LOP_DB_USER, LOP_DB_PASS, LOP_DB_NAME);

if ($connect->connect_error) {
    die("連線失敗: " . $connect->connect_error);
}
// echo "連線成功";

//設定連線編碼，防止中文字亂碼
$connect->query("SET NAMES 'utf8'");

if(empty(!$_GET['class'])){
    $where = "catena.name = '{$_GET['class']}'";
}elseif(empty(!$_GET['kind'])){
    $where = "kind.name = '{$_GET['kind']}'";
}

$selectSql = "select
                products.id,
                products.name,
                products.material,
                products.spec,
                products.price,
                products.color,
                products.one_img,
                products.live_img,
                products.product_img,
                catena.name as catena_name,
                kind.name as kind_name
                from products
                inner join catena on products.catena_id = catena.product_id
                inner join kind on products.kind_id = kind.product_id
                where {$where}";
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
        $show[] = array(
                        'productId'=>$row['id'],
                        'productName'=>$row['name'],
                        'material'=>$row['material'],
                        'spec'=>$row['spec'],
                        'price'=>$row['price'],
                        'color'=>$row['color'],
                        'catena_name'=>$row['catena_name'],
                        'kind_name'=>$row['kind_name'],
                        'one_img'=>$row['one_img'],
                        'live_img'=>$row['live_img'],
                        'product_img'=>$row['product_img']
                    );
    };
} else {
    echo '0筆資料';
};

        echo json_encode($show,JSON_UNESCAPED_UNICODE);
