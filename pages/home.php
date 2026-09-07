<?php
require ROOT . "/pages/start.php"; ?>
<style>
    .lgtext {
        font-size: 2rem;
    }

    .itext {
        font-size: 3rem;
    }
</style>
<?php
require ROOT . "/pages/header.php"; 
_selectRowNoParam("SELECT count(id) FROM posts", $ptoo);
?>
<div class="row">
    <div class="col bg-primary text-white m-3 p-3 rounded-3 d-flex justify-content-between">
        <div class="">Нийт мэдээ
            <div class="lgtext"><?=$ptoo?></div>
        </div>
        <div class="itext"><i class="fa fa-file-text" aria-hidden="true"></i></div>
    </div>
    <div class="col bg-success text-white m-3 p-3  rounded-3 d-flex justify-content-between">
        <div class="">Сайтын хандалт
            <div class="lgtext">Өнөөдөр</div>
        </div>
        <div class="itext">5</div>
    </div>
    <div class="col bg-warning text-white m-3 p-3  rounded-3 d-flex justify-content-between">
        <div class="">Сайтын хандалт
            <div class="lgtext">Энэ онд</div>
        </div>
        <div class="itext">5</div>
    </div>
    <div class="col bg-danger text-white m-3 p-3  rounded-3 d-flex justify-content-between">
        <div class="">Сайтын хандалт
            <div class="lgtext">Нийт</div>
        </div>
        <div class="itext">5</div>
    </div>
</div>
<?php
require ROOT . "/pages/footer.php";
require ROOT . "/pages/end.php"; ?>