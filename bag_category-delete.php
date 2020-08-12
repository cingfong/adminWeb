<?php
    //刪除分類
    require_once    'function.php';

    if (empty($_GET['id'])){
        exit('缺少必要參數');
    }

    $id = $_GET['id'];

    $rows = lop_execute('delete from kind where product_id in (' . $id . ');');

    $target = empty($_SERVER['HTTP_REFERER']) ? '/bag_categories.php' : $_SERVER['HTTP_REFERER'];

    header('Location: ' . $target);

    echo json_encode($rows > 0);