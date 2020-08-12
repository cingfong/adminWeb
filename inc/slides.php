<link rel="stylesheet" type="text/css" href="./inc/css/sidebar.css">
<?php

$current_page = isset($current_page) ? $current_page : '';

$current_user = lop_get_current_user();

?>
<div class="sidebar">
    <ul class="list-group">
        <li class="list_title">
            <div class="logo">
                <a href="http://lophura.com.tw"><img src="static/img/logo_white.png"></a>
            </div>
            <p>LOPHURA 後台</p>
        </li>
        <a href="/index.php"><li class="list-group-item list liOne
            <?php echo $current_page === 'index' ? ' active' : '' ?>"
        >
            <i></i>概況</li>
        </a>
        <?php $menu_products = array('all_pro','add_pro','class_cat','products_cat', 'bag_cat', 'access_cat') ?>
        <li class="list-group-item list libtn
            <?php echo in_array($current_page, $menu_products) ? ' active menu_on' : ''; ?>"
        >產品項目<i class="fas fa-angle-right"></i></li>
            <ul class="list_child" style="display: none">
                <a href="/all_products.php"><li class="lili_item
                    <?php echo $current_page === 'all_pro' ? ' active_li' : ''; ?>"
                >所有產品</li></a>
                <a href="/add_products.php"><li class="lili_item
                    <?php echo $current_page === 'add_pro' ? ' active_li' : ''; ?>"
                >新增</li></a>
                <a href="/class_categories.php"><li class="lili_item
                    <?php echo $current_page === 'class_cat' ? ' active_li' : ''; ?>"
                >分類</li></a>
                <a href="/bag_categories.php"><li class="lili_item
                    <?php echo $current_page === 'bag_cat' ? ' active_li' : ''; ?>"
                >包包類</li></a>
                <a href="/access_categories.php"><li class="lili_item
                    <?php echo $current_page === 'access_cat' ? ' active_li' : ''; ?>"
                >配件類</li></a>
            </ul>
        <?php $menu_settings = array('password_reset','slides','index_products','news') ?>
        <li class="list-group-item list libtn
            <?php echo in_array($current_page, $menu_settings) ? ' active menu_on' : ''; ?>"
        >設定<i class="fas fa-angle-right"></i></li>
            <ul class="list_child" style="display: none">
                <a href="/password_reset.php"><li class="lili_item
                    <?php echo $current_page === 'password_reset' ? ' active_li' : ''; ?>"
                >密碼設定</li></a>
                <a href="/slides.php"><li class="lili_item
                    <?php echo $current_page === 'slides' ? ' active_li' : ''; ?>"
                >首頁輪播圖</li></a>
                <a href="/index_products.php"><li class="lili_item
                    <?php echo $current_page === 'index_products' ? ' active_li' : ''; ?>"
                >首頁產品展示</li></a>
                <a href="/news.php"><li class="lili_item
                    <?php echo $current_page === 'news' ? ' active_li' : ''; ?>"
                >活動新增</li></a>
            </ul>
    </ul>
</div>
<script>
(function () {
    var liSwitch = true
    $('.libtn').click(function () {
        if(liSwitch){
            liSwitch = false
            var $state = $(this).next().css('display')
            $i = $(this).children('i')
            $(this).next().slideToggle(300,function () {
                liSwitch = true
            })
            if($state === 'none'){
                $i.addClass('open_i').removeClass('close_i')
            }else{
                $i.addClass('close_i').removeClass('open_i')
            }
        }
    })
    var menuOn = '<?php echo $current_page; ?>'
    if (menuOn) {
        var $pro = $('.menu_on')
        $pro.next().css('display','block')
        $pro.children('i').addClass('open_i')


    }
}())
</script>