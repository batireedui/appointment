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
    "SELECT id, name, email, phone, feedback, ognoo, ip FROM feedback",
    $id,
    $name,
    $email,
    $phone,
    $feedback,
    $ognoo,
    $ip
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
    <h3>ИРСЭН САНАЛ ХҮСЭЛТҮҮД</h3>
</div>
<div class="row">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Нэр</th>
                <th>Email</th>
                <th>Утас</th>
                <th>Санал хүсэлт</th>
                <th>Огноо</th>
                <th>Хаяг</th>
            </tr>
        </thead>
        <tbody class="table-group-divider">
            <?php
            $dd = 1;
            while (_fetch($st)) { ?>
                <tr>
                    <th scope="row"><?= $dd ?></th>
                    <td><?= $name ?></td>
                    <td><?= $email ?></td>
                    <td><?= $phone ?></td>
                    <td><?= $feedback ?></td>
                    <td><?= $ognoo ?></td>
                    <td><?= $ip ?></td>
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
    function deletePost(id) {
        if (confirm("Ангилал устгахдаа итгэлтэй байна уу!") == true) {
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
                    if (data == "Амжилттай!") {
                        location.reload();
                    } else alert(data);
                },
                async: true
            });
        }
    }
</script>
<?php
require ROOT . "/pages/end.php"; ?>