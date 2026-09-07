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
    $stCa,
    $coCA,
    "SELECT id, `key`, mn, en FROM language",
    $id,
    $key,
    $mn,
    $en
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
    <h3>ҮНДСЭН СТАТИК ҮГНИЙ ОРЧУУЛГА</h3>
</div>
<div class="row">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Нэр</th>
                <th>Нэр /Англи/</th>
                <th>Тайлбар</th>
            </tr>
        </thead>
        <tbody class="table-group-divider">
            <?php
            $dd = 1;
            while (_fetch($stCa)) { ?>
                <tr>
                    <td class="fw-bold text-primary"><?= $dd ?></td>
                    <td><?= $key ?></td>
                    <td><div class='editcell' onblur='updateValue(this, "<?= $id ?>", "mn")' contenteditable><?= $mn ?></div></td>
                    <td>
                        <div class='editcell' onblur='updateValue(this, "<?= $id ?>", "en")' contenteditable><?= $en ?></div>
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
require ROOT . "/pages/footer.php"; ?>
<script>
    function updateValue(element, id, turul) {
        var value = element.innerText;
        $.ajax({
            url: '/updateVal',
            type: 'post',
            data: {
                type: "translate",
                turul: turul,
                id: id,
                value: value
            },
            error: function(xhr, textStatus, errorThrown) {
                console.log(errorThrown);
            },
            beforeSend: function() {

            },
            success: function(data) {
                alert(data);
            },
            async: true
        })
    }

    function tshow() {
        var toastElList = [].slice.call(document.querySelectorAll('.toast'))
        var toastList = toastElList.map(function(toastEl) {
            return new bootstrap.Toast(toastEl)
        })
        toastList.forEach(toast => toast.show())
    }

    function deleteMenu(id) {
        if (confirm("Цэс устгахдаа итгэлтэй байна уу!") == true) {
            $.ajax({
                url: "maction",
                type: "POST",
                data: {
                    deleteMenu: "deleteMenu",
                    id: id
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                },
                beforeSend: function() {

                },
                success: function(data) {
                    if (data == "Амжилттай устгагдлаа") {
                        location.reload();
                    } else alert(data);
                },
                async: true
            });
        }
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