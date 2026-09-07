<?php
require ROOT . "/pages/start.php"; ?>
<link rel="stylesheet" type="text/css" href="/styles/dataTable.css">
<style>
    .dt-buttons {
        text-align: end;
        margin-bottom: 15px
    }
</style>
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

$sqlw = "";
if ($_SESSION['user_role'] != "1") {
    $sqlw = " and userid = " . $_SESSION['user_id'];
}

_selectNoParam(
    $st,
    $co,
    "SELECT posts.id, posts.name, posts.name_en, posts.slug, posts.tuluv, adminusers.name FROM posts INNER JOIN adminusers ON posts.userid = adminusers.id WHERE turul = 'post' $sqlw ORDER BY ognoo DESC",
    $id,
    $name,
    $name_en,
    $slug,
    $tuluv,
    $uname
);

?>
<?php if (isset($_SESSION['messages'])) : ?>
    <div class="col">
        <div class="alert alert-primary alert-dismissible" role="alert">
            <?php foreach ($_SESSION['messages'] as $v) {
                echo "$v";
            } ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php unset($_SESSION['messages']);
endif ?>
<div class="row mt-3">
    <h3>МЭДЭЭ, МЭДЭЭЛЭЛ</h3>
</div>
<div class="row">
    <table id="datalist" class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Нэр</th>
                <th>Англи нэр</th>
                <th>Ангилал</th>
                <th>Төлөв</th>
                <th>Нэмсэн</th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody class="table-group-divider">
            <?php
            $dd = 1;
            while (_fetch($st)) { ?>
                <tr>
                    <th scope="row"><?= $dd ?></th>
                    <td><?= $name ?></td>
                    <td><?= $name_en ?></td>
                    <td><?php
                        _selectNoParam(
                            $cst,
                            $cco,
                            "SELECT name FROM `post_cate` INNER JOIN category ON post_cate.cate_id = category.id WHERE post_cate.post_id = '$id'",
                            $cname
                        );
                        $taslal = 0;
                        while (_fetch($cst)) {
                            $taslal++;
                            echo $cname;
                            if ($cco > $taslal) echo ", ";
                        } ?>
                    <td>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" onchange='tuluvChange(<?= $id ?>, this)' <?= $tuluv == 1 ? "checked" : ""; ?>>
                        </div>
                    </td>
                    <td><?= $uname ?></td>
                    <td>
                        <a href="addPost?edit=<?= $slug ?>"><span class="btn btn-success btn-sm mx-3">ЗАСАХ</span></a>
                    </td>
                    <td>
                        <span class="btn btn-danger btn-sm" onclick="deletePost(<?= $id ?>)">УСТГАХ</span>
                    </td>
                </tr>
            <?php
                $dd++;
            } ?>
        </tbody>
    </table>
</div>
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-warning">
            <strong class="me-auto">Мэдээлэл</strong>
            <small>Төлөв</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastbody">

        </div>
    </div>
</div>

<?php
require ROOT . "/pages/footer.php";
require ROOT . "/pages/dataTablefooter.php";
?>
<script>
    function deletePost(id) {
        if (confirm("Нийтлэл устгахдаа итгэлтэй байна уу!") == true) {
            $.ajax({
                url: "aaction",
                type: "POST",
                data: {
                    deletePost: "deletePost",
                    id: id
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                },
                beforeSend: function() {

                },
                success: function(data) {
                    if (data == "Амжилттай") {
                        location.reload();
                    } else alert(data);
                },
                async: true
            });
        }
    }

    function tshow() {
        var toastElList = [].slice.call(document.querySelectorAll('.toast'))
        var toastList = toastElList.map(function(toastEl) {
            return new bootstrap.Toast(toastEl)
        })
        toastList.forEach(toast => toast.show())
    }

    function tuluvChange(id, tuluv) {
        console.log(tuluv.checked);
        $.ajax({
            url: "aaction",
            type: "POST",
            data: {
                tuluvChange: "tuluvChange",
                cid: id,
                tuluv: tuluv.checked
            },
            error: function(xhr, textStatus, errorThrown) {
                console.log(errorThrown);
            },
            beforeSend: function() {

            },
            success: function(data) {
                console.log(data);
                $('#toastbody').html(data);
                tshow();
            },
            async: true
        });
    }
</script>
<?php
require ROOT . "/pages/end.php"; ?>