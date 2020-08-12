<?php


require_once 'outer/config.php';

@session_start();



function lop_get_current_user() {
    if(empty($_SESSION['current_login_user'])){
      //沒有當前登入用戶信息，意味沒有登入
      header('Location: /login.php');
        echo '<h5 style="
                    color: #721c24;
                    font-size: 32px;
                    background-color: #f8d7da;
                    padding: 10px 15px"
            >請重新登入</h5>';
      exit(); //沒有必要再執行之後的動作
    }
    return $_SESSION['current_login_user'];
}


//通過一個數據庫查詢獲取數據
//=> 索引數組套關聯數組
function lop_fetch_all ($sql){
    $conn = mysqli_connect(LOP_DB_HOST, LOP_DB_USER, LOP_DB_PASS, LOP_DB_NAME);
    if(!$conn) {
        exit('連結失敗');
    }
    $query = mysqli_query($conn, $sql);
    if(!$query) {
        // 查詢失敗
        return false;
    }

    $result = array();


    while ($row = mysqli_fetch_assoc($query)){
        $result[] = $row;
    }


    mysqli_free_result($query);
    mysqli_close($conn);

    return $result;
}

//獲取單條數據
//=>關聯數組
function lop_fetch_one ($sql){
    $res = lop_fetch_all($sql);
    return isset($res[0]) ? $res[0] : null;
}


//執行增刪改
function lop_execute($sql){
    //由此行開始重複語句，可封裝成動作
    $conn = mysqli_connect(LOP_DB_HOST, LOP_DB_USER, LOP_DB_PASS, LOP_DB_NAME);
    if(!$conn) {
        exit('連結失敗');
    }
    $query = mysqli_query($conn, $sql);
    if(!$query) {
        // 查詢失敗
        return false;
    }
    //至此
    //對於增加刪除修改的操作都是獲取受影響行數
    $affected_rows = mysqli_affected_rows($conn);

    mysqli_close($conn);

    return $result;

}

ob_end_flush();