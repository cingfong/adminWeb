<?php


// 接受客戶端的 AJAX 請求 返回評論數據



// 載入封裝的所有函數
require_once '../function.php';

//取得客戶端傳遞過來的分頁頁碼
$page = empty($_GET['page']) ? 1 : intval($_GET['page']);

$length = 10;
// 根據頁碼計算越過多少條
$offset = ($page - 1) * $length;

$sql = sprintf('select
  products.id,
  products.name,
  products.material,
  products.spec,
  products.price,
  products.color,
  products.color,
  catena.name as catena_name,
  kind.name as kind_name,
  products.status
from products
inner join catena on products.catena_id = catena.product_id
inner join kind on products.kind_id = kind.product_id
limit %d, %d;', $offset, $length);
// 查詢所有的評論數據
$products = lop_fetch_all($sql);


//先查詢到所有的數據的數量
$total_count = lop_fetch_one('select count(1) as count
from products')['count'];
$total_pages = ceil($total_count / $length);
// 蘇然返回式數據類型 float 但是數字一定是一個整數

// 因為網路之間傳輸的只能是字符串
// 所以我們先將數據轉換成字符串

// $json = json_encode($comments);

$json = json_encode(array(
    'total_pages' => $total_pages,
    'products' => $products
));

// 設置響應的響應體類型為 json
header('Content-Type: appliction/json');
// header('Content-type: text/html; charset=utf8');

// 響應給客戶端
echo $json;