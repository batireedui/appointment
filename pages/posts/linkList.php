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
    "SELECT id, image, name, name_en, url, turul FROM links",
    $id,
    $image,
    $name,
    $name_en,
    $url,
    $turul
);
$carray = array();
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
    <h3>ХОЛБООС</h3>
</div>
<div class="row">
    <div style="text-align: end;">
        <a href="links">
            <button class="btn btn-success">ХОЛБООС НЭМЭХ</button>
        </a>
    </div>
</div>
<div class="row">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Лого</th>
                <th>Нэр</th>
                <th>Нэр /Англи/</th>
                <th>Төрөл</th>
                <th>Холбоос</th>
                <th></th>
            </tr>
        </thead>
        <tbody class="table-group-divider">
            <?php
            $dd = 1;
            while (_fetch($stCa)) { ?>
                <tr>
                    <td class="fw-bold text-primary"><?= $dd ?></td>
                    <td><img src="<?= $image ?>" style="height: 50px;"></td>
                    <td><?= $name ?></td>
                    <td><?= $name_en ?></td>
                    <td><?= $linkArr[$turul] ?></td>
                    <td><?= $url ?></td>
                    <td><a href="/posts/links?edit=<?= $id ?>"><span class="btn btn-success btn-sm mx-3">ЗАСАХ</span></a>
                    <span class="btn btn-danger btn-sm" onclick="deleteLink(<?= $id ?>)">УСТГАХ</span></td>
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
                type: "menuerembe",
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
                console.log(data);
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

    function deleteLink(id) {
        if (confirm("Холбоос устгахдаа итгэлтэй байна уу!") == true) {
            $.ajax({
                url: "aaction",
                type: "POST",
                data: {
                    deleteLink: "deleteLink",
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
</script>
<?php
require ROOT . "/pages/end.php"; ?>