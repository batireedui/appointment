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
    $cc,
    "SELECT id, name, email, phone, role, tuluv FROM adminusers",
    $uid,
    $uname,
    $uemail,
    $uphone,
    $urole,
    $utuluv
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
    <h3>ХЭРЭГЛЭГЧДИЙН БҮРТГЭЛ</h3>
</div>
<div class="row">
    <div style="text-align: end;">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal" onclick='changeRole()'>ХЭРЭГЛЭГЧ НЭМЭХ</button>
    </div>
</div>
<div class="row">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Нэр</th>
                <th>Email</th>
                <th>Утас</th>
                <th>Төрөл</th>
                <th>Төлөв</th>
                <th>Эрх</th>
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
                    <td id="u1<?= $uid ?>"><?= $uname ?></td>
                    <td id="u2<?= $uid ?>"><?= $uemail ?></td>
                    <td id="u3<?= $uid ?>"><?= $uphone ?></td>
                    <td id="u4<?= $uid ?>"><?= $urole ?></td>
                    <td>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" <?php echo $utuluv == 1 ? "checked" : "" ?> disabled>
                        </div>
                    </td>
                    <td><small><?php
                        _select(
                            $sss,
                            $ccc,
                            "SELECT name FROM `admintax` INNER JOIN category ON admintax.category_id = category.id WHERE userid = ?",
                            "i",
                            [$uid],
                            $cate_name
                        );
                        while (_fetch($sss)) {
                            echo $cate_name . ", ";
                        }
                        ?></small></td>
                    <td>
                        <span class="btn btn-success btn-sm mx-3" data-bs-toggle="modal" data-bs-target="#editModal" onclick='edit(<?= $uid ?>, <?= $urole ?>, <?= $utuluv ?>)'>ЗАСАХ</span>
                    </td>
                    <td>
                        <?php if ($uid != $_SESSION["user_id"]) { ?><span class="btn btn-danger btn-sm" onclick="deleteUser(<?= $uid ?>)">УСТГАХ</span><?php } ?>
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
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addlLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addlLabel">ХЭРЭГЛЭГЧ БҮРТГЭХ</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalbody">
                <form class="row g-3" action="uaction" method="post">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="name" name="name" placeholder="Нэр энд бичнэ үү" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" id="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" id="phone" name="phone" placeholder="Утасны дугаар" required>
                    </div>
                    <div class="mb-3">
                        <select class="form-select" id="role" name="role" onchange="changeRole()">
                            <option value="2">Нийтлэгч</option>
                            <option value="1">Админ</option>
                        </select>
                    </div>
                    <div class="mb-3" id="meta">
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Хаах</button>
                <button type="submit" class="btn btn-primary" name="userAdd">Бүртгэх</button>
            </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editModalLabel">ХЭРЭГЛЭГЧ ЗАСАХ</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalbody">
                <form class="row g-3" action="uaction" method="post">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="ename" name="ename" placeholder="Нэр энд бичнэ үү" required>
                        <input type="text" id="eid" name="eid" value="" style="display: none;" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" id="eemail" name="eemail" placeholder="Email" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" id="ephone" name="ephone" placeholder="Утасны дугаар" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col mb-3">
                            <select class="form-select" id="erole" name="erole" onchange="changeRole()">
                                <option value="2">Нийтлэгч</option>
                                <option value="1">Админ</option>
                            </select>
                        </div>
                        <div class="col mb-3">
                            <label>Төлөв</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="ephone" name="etuluv" role="switch" <?php echo $utuluv == 1 ? "checked" : "" ?>>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3" id="emeta">
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Хаах</button>
                <button type="submit" class="btn btn-primary" name="userEdit">ЗАСАХ</button>
            </div>
            </form>
        </div>
    </div>
</div>
<?php
require ROOT . "/pages/footer.php"; ?>
<script>
    async function edit(id, role, tuluv) {
        $("#eid").val(id);
        $("#ename").val($('#u1' + id).text());
        $("#eemail").val($('#u2' + id).text());
        $("#ephone").val($('#u3' + id).text());
        $("#erole").val(role);
        await changeRolea(id);
    }

    function changeRolea(id) {
        let role = $('#erole').val();
        $('#meta').html("");
        if (role == "1") {
            $('#emeta').html("");
        } else {
            $.ajax({
                url: "/menu/menutype",
                type: "POST",
                data: {
                    menu: 'category'
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                },
                beforeSend: function() {

                },
                success: function(data) {
                    $('#emeta').html(data);
                    $.ajax({
                        url: "uaction",
                        type: "POST",
                        data: {
                            getEdit: 'getEdit',
                            id: id
                        },
                        error: function(xhr, textStatus, errorThrown) {
                            console.log(errorThrown);
                        },
                        beforeSend: function() {

                        },
                        success: function(data) {
                            let cate = $('[name="cate_id[]"]');
                            console.log(data);
                            data = JSON.parse(data);
                            for (let i = 0; i < cate.length; i++) {
                                for (let j = 0; j < data.length; j++) {
                                    if (cate[i].value == data[j]) {
                                        cate[i].checked = true;
                                    }
                                }
                            }
                        },
                        async: true
                    });
                },
                async: true
            });
        }
    }

    function changeRole() {
        let role = $('#role').val();
        if (role == "1") {
            $('#meta').html("");
        } else {
            typeSelect();
        }
    }
    typeSelect();

    function typeSelect() {
        $.ajax({
            url: "/menu/menutype",
            type: "POST",
            data: {
                menu: 'category'
            },
            error: function(xhr, textStatus, errorThrown) {
                console.log(errorThrown);
            },
            beforeSend: function() {

            },
            success: function(data) {
                $('#meta').html(data);
            },
            async: true
        });
    };

    function deleteUser(id) {
        if (confirm("Хэрэгч устгахдаа итгэлтэй байна уу!") == true) {
            $.ajax({
                url: "uaction",
                type: "POST",
                data: {
                    deleteUser: "deleteUser",
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
                    } else console.log(data);
                },
                async: true
            });
        }
    }
</script>
<?php
require ROOT . "/pages/end.php"; ?>