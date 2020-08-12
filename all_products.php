<?php

require_once 'function.php';

//判斷用戶是否登入，一定是最優先
lop_get_current_user();


//接收篩選參數
//====================
$where = '1 = 1';
$search = '';


//分類篩選
if(isset($_GET['class']) && $_GET['class'] !== 'all'){
  $where .= ' and products.catena_id = ' . $_GET['class'];
  $search .= '&class=' . $_GET['class'];
}

if(isset($_GET['kind']) && $_GET['kind'] !== 'all'){
  $where .= " and products.kind_id = '{$_GET['kind']}'";
  $search .= '&kind=' . $_GET['kind'];
}


//處理分頁參數

$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];


if ($page < 1) {
  header('Location: /all_products.php?page=1' . $search);
}


$size = 10;



//end 必須 <= 最大頁數，求出最大頁碼
//最大的頁數 $total_pages = celi($total_count / $size)
$total_count = (int)lop_fetch_one("select count(1) as num from products
inner join catena on products.catena_id = catena.product_id
inner join kind on products.kind_id = kind.product_id
where {$where};")['num'];
$total_pages = (int)ceil($total_count / $size);

//新增若無值則出現alert且轉跳到all第一頁
if($total_count == ''){
  echo "<script>
          alert('沒有這分類產品');
          location.href='/all_products.php?page=1';
        </script>";
}elseif($page > $total_pages){
  header('Location: /all_products.php?page=' . $total_pages . $search);
}
//計算橫跨多少條
$offset = ($page - 1 ) * $size;





$posts = lop_fetch_all("select
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
  kind.name as kind_name,
  status.name as status_name,
  status.class_name as status_class
from products
inner join catena on products.catena_id = catena.product_id
inner join kind on products.kind_id = kind.product_id
inner join status on products.status_id = status.product_id
where {$where}
limit {$offset}, {$size};");

$categories = lop_fetch_all('select * from catena');


$kind = lop_fetch_all('select * from kind');



$visiables = 9;

//計算最大和最小展示的頁碼
$begin = $page - ($visiables - 1) / 2;
$end = $begin + $visiables - 1;
//考慮合理性問題
//begin > 0 end <= total_pages
$begin = $begin < 1 ? 1 : $begin; //確保 begin 不會小於 1
$end = $begin + $visiables - 1;                 //因為 上行可能導致 begin 變化，這裡同步兩者關係
$end = $end > $total_pages ? $total_pages : $end;//確保 end 不會大於 total_pages
$begin = $end - $visiables + 1;                //因為 上行 可能改變了 end， 也就有可能打破 begin 和 end 的關係
$begin = $begin < 1 ? 1 : $begin;






// 狀態
// function conver_status ($status) {
//   $dict = array(
//                 '1' => '顯示',
//                 '0' => '隱藏'
//           );
//   return isset($dict[$status]) ? $dict[$status] : '未知';
// }
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
    <link rel="stylesheet" type="text/css" href="/static/css/all_products.css">
    <div class="main">
        <?php include 'inc/navbar.php'; ?>
        <div class="content">
            <h2>所有產品</h2>
            <a href="/add_products.php"><div class="alert alert-primary" role="alert">新增產品</div></a>
            <a class="alert alert-danger delbutn" style="display: none;" id="btn_delete" href="/products-delete.php">批量刪除</a>
            <form>
                <div class="input-group">
                  <select class="custom-select options" id="inputGroupSelect02" name="class">
                    <option value="all">所有系列</option>
                    <?php foreach ($categories as $item): ?>
                      <option
                        value="<?php echo $item['product_id'] ?>"
                        <?php echo isset($_GET['class']) && $_GET['class'] == $item['product_id'] ? ' selected' : '' ?>
                      >
                        <?php echo $item['name']; ?>
                      </option>
                    <?php endforeach ?>
                  </select>
                  <select class="custom-select options" id="inputGroupSelect02" name="kind">
                    <option value="all">所有產品</option>
                    <?php foreach ($kind as $item): ?>
                      <option
                        value="<?php echo $item['product_id'] ?>"
                        <?php echo isset($_GET['kind']) && $_GET['kind'] == $item['product_id'] ? ' selected' : '' ?>
                      >
                        <?php echo $item['name']; ?>
                      </option>
                    <?php endforeach ?>
                  </select>
                  <div class="input-group-append">
                    <button class="input-group-text" for="inputGroupSelect02">搜尋</button>
                  </div>
                    <ul class="pagination page">
                      <?php if ($page - 1 > 0) : ?>
                      <li><a class="page-link" href="?page=<?php echo ($page - 1). $search; ?>">上一页</a></li>
                      <?php endif; ?>
                      <?php for ($i = $begin; $i <= $end; $i++): ?>
                      <li<?php echo $i === $page ? ' class="active"' : ''; ?>><a class="page-link" href="?page=<?php echo $i . $search; ?>"><?php echo $i; ?></a></li>
                      <?php endfor ?>
                      <?php if ($page + 1 <= $total_pages) : ?>
                      <li><a class="page-link" href="?page=<?php echo ($page + 1). $search; ?>">下一页</a></li>
                      <?php endif; ?>
                    </ul>
                </div>
            </form>
            <table>
                <thead>
                    <tr>
                        <td>選項</td>
                        <td>商品照片</td>
                        <td>商品名稱</td>
                        <td>系列</td>
                        <td>產品分類</td>
                        <td>價格</td>
                        <td>商品顯示</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $item): ?>
                    <tr class="<?php echo $item['status_class'] == 'status_none'? 'warn' : '' ?>">
                        <td><input type="checkbox" data-id="<?php echo $item['id']; ?>" name=""></td>
                        <td class="table_ph"><img src="<?php echo $item['one_img']; ?>"></td>
                        <td class="table_name"><?php echo $item['name']; ?></td>
                        <td class="table_catena"><?php echo $item['catena_name'];?></td>
                        <td class="table_category"><?php echo $item['kind_name'];?></td>
                        <td class="table_price">$<?php echo $item['price'];?></td>
                        <td class="table_status"><?php echo $item['status_name'];?></td>
                        <td class="table_ac">
                            <a href="edit_products.php?id=<?php echo $item['id']; ?>">編輯商品</a>
                            <a href="products-delete.php?id=<?php echo $item['id']; ?>">刪除商品</a>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php $current_page = 'all_pro'; ?>
    <script src="/static/venders/jquery/jquery-1.12.4.js"></script>
    <script src="/static/venders/popper/popper.min.js"></script>
    <script src="/static/venders/bootstrap-4.2.1-dist/js/bootstrap.js"></script>
    <script type="static/venders/twbs-pagination/jquery.twbsPagination.js"></script>
    <script src="/static/js/index.js"></script>
    <?php include 'inc/slides.php'; ?>
    <script>
      // 1. 不要重複使用無意義的選擇操作，應該採用變量去本地化
      $(function ($) {
          //有任意一個 checkbox選終究顯示，反之隱藏

          var $tbodyCheckboxs = $('tbody input')
          var $btnDelete = $('#btn_delete')

          //定義一個數組，記錄被選中的
          var allCheckeds = []
          $tbodyCheckboxs.on('change', function () {

            var id = $(this).data('id')

            //根據有沒有選中當前這個 checkbox 決定是添加還是移除
            if ($(this).prop('checked')) {
              //判斷數組內有無重複
              allCheckeds.includes(id) || allCheckeds.push(id)
            } else {
              allCheckeds.splice(allCheckeds.indexOf(id), 1)
            }

            //根據剩下多少選中的 checkbox 決定是否顯示刪除
            allCheckeds.length ? $btnDelete.fadeIn() : $btnDelete.fadeOut()
            $btnDelete.prop('search', '?id=' + allCheckeds)
          })

          //找一個合適的時機 做一件合適的事情
          //全選和全不選
          $('thead input').on('change', function () {
            // 1.獲取當前選中狀態
            var checked = $(this).prop('checked');
            // 2.設置給標體中的每一個
            $tbodyCheckboxs.prop('checked', checked).trigger('change')
          })
      })
    </script>
</body>
</html>