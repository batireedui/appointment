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

_selectNoParam(
    $st,
    $co,
    "SELECT id, name, name_en, slug, tuluv FROM posts WHERE turul = 'page'",
    $id,
    $name,
    $name_en,
    $slug,
    $tuluv
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
    <h3>ХУУДАС</h3>
</div>
<div class="row">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Нэр</th>
                <th>Англи нэр</th>
                <th>Төлөв</th>
                <th></th>
                <th>Slug</th>
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
                    <td>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" onchange='tuluvChange(<?= $id ?>, this)' <?= $tuluv == 1 ? "checked" : ""; ?>>
                        </div>
                    </td>
                    <td>
                        <a href="edit?edit=<?= $slug ?>"><span class="btn btn-success btn-sm mx-3">ЗАСАХ</span></a>
                        <span class="btn btn-danger btn-sm" onclick="deleteCate(<?= $id ?>)">УСТГАХ</span>
                    </td>
                    <td><small><?= $slug ?></small></td>
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
require ROOT . "/pages/footer.php"; ?>
<script>
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
            url: "caction",
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