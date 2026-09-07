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
<link rel="stylesheet" type="text/css" href="/styles/dataTable.css">
<style>
    .dt-buttons {
        text-align: end;
        margin-bottom: 15px
    }
</style>
<?php
require ROOT . "/pages/header.php";

_selectNoParam(
    $stCa,
    $coCA,
    "SELECT id, name, tai, parent_id, tuluv, slug FROM category WHERE parent_id = '0'",
    $cid,
    $cname,
    $ctai,
    $cparent_id,
    $ctuluv,
    $slug
);
$carray = array();

while (_fetch($stCa)) {
    $row = new stdClass();
    $row->id = $cid;
    $row->cname = $cname;
    $row->ctai = $ctai;
    $row->cparent_id = $cparent_id;
    $row->ctuluv = $ctuluv;
    $row->slug = $slug;
    array_push($carray, $row);
}

function select_subcateTable($prefix, $cate_id, $d)
{

    $val = "";

    _selectNoParam(
        $stCa,
        $coCA,
        "SELECT id, name, tai, parent_id, tuluv, slug FROM category WHERE parent_id = '$cate_id'",
        $cid,
        $cname,
        $ctai,
        $cparent_id,
        $ctuluv,
        $slug
    );

    $dd = 0;
    while (_fetch($stCa)) {
        $dd++;

        _selectRowNoParam(
            "SELECT name FROM category WHERE id = '$cparent_id'",
            $parent_name
        );

        $val .= "<tr>
                <th scope='row'>$prefix&nbsp;&nbsp; $d.$dd</th>
                <td id='cate1-$cid'>$cname</td>
                <td id='cate2-$cid'>$ctai</td>
                <td>$parent_name</td>
                <td>
                <div class='form-check form-switch'>
                    <input class='form-check-input' type='checkbox' role='switch' onchange='tuluvChange($cid, this)'";
        $ctuluv == 1 ? $val .= 'checked>' : $val .= '>';
        $val .= "</div>
                </td>
                <td>
                <span class='btn btn-success btn-sm mx-3'  data-bs-toggle='modal' data-bs-target='#editModal' onclick='edit($cid, \"$cparent_id\")'>ЗАСАХ</span>";
        if ($cid > 9) {
            $val .= "<span class='btn btn-danger btn-sm' onclick='deleteCate($cid)'>УСТГАХ</span>";
        }
        $val .=   "</td>
            </tr>";
        $val .= select_subcateTable("$prefix&nbsp;&nbsp;", $cid, $d . "." . $dd);
    }
    return $val;
}

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
    <h3>АНГИЛАЛ</h3>
</div>
<div class="row">
    <form class="row g-3" action="caction" method="post">
        <div class="col">
            <div class="form-floating">
                <select class="form-select" id="dca" name="dca">
                    <option selected value="0">Байхгүй</option>
                    <?php foreach ($carray as $el) { ?>
                        <option value="<?= $el->id ?>"><?= $el->cname ?></option>
                    <?php echo select_subcate("", $el->id);
                    } ?>
                </select>
                <label for="dca">Дээд ангилал</label>
            </div>
        </div>
        <div class="col">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="cname" name="cname" placeholder="Цаг үеийн мэдээ" required>
                <label for="cname">Ангилалын нэр</label>
            </div>
        </div>
        <div class="col">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="ctai" name="ctai" placeholder="Цаг үеийн мэдээ" required>
                <label for="ctai">Ангилалын тайлбар</label>
            </div>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary mb-3" name="categoryAdd">Бүртгэх</button>
        </div>
    </form>
</div>
<div class="row">
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Нэр</th>
                <th>Тайлбар</th>
                <th>Дээд ангилал</th>
                <th>Төлөв</th>
                <th></th>
            </tr>
        </thead>
        <tbody class="table-group-divider">
            <?php
            $dd = 1;
            foreach ($carray as $el) { ?>
                <tr>
                    <th scope="row"><?= $dd ?></th>
                    <td id="cate1-<?= $el->id ?>"><?= $el->cname ?></td>
                    <td id="cate2-<?= $el->id ?>"><?= $el->ctai ?></td>
                    <td></td>
                    <td>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" onchange='tuluvChange(<?= $el->id ?>, this)' <?= $el->ctuluv == 1 ? "checked" : ""; ?>>
                        </div>
                    </td>
                    <td>
                        <span class="btn btn-success btn-sm mx-3" data-bs-toggle="modal" data-bs-target="#editModal" onclick='edit(<?= $el->id ?>, "<?= $el->cparent_id ?>")'>ЗАСАХ</span>
                        <?php if ($el->id > 9) { ?> <span class="btn btn-danger btn-sm" onclick="deleteCate(<?= $el->id ?>)">УСТГАХ</span> <?php } ?>
                    </td>
                </tr>
            <?php echo select_subcateTable("", $el->id, $dd);
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
<!-- Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editModalLabel">Ангилал засах</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalbody">
                <div class="col">
                    <div class="form-floating mb-3">
                        <select class="form-select" id="edca">
                            <option selected value="0">Байхгүй</option>
                            <?php foreach ($carray as $el) { ?>
                                <option value="<?= $el->id ?>"><?= $el->cname ?></option>
                            <?php echo select_subcate("", $el->id);
                            } ?>
                        </select>
                        <label for="edca">Дээд ангилал</label>
                    </div>
                </div>
                <div class="col">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="ename" placeholder="Цаг үеийн мэдээ" required>
                        <label for="ename">Ангилалын нэр</label>
                        <input type="text" id="eid" style="display: none;" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="etai" placeholder="Цаг үеийн мэдээ" required>
                        <label for="etai">Ангилалын тайлбар</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Хаах</button>
                <button type="button" class="btn btn-primary" onclick="editChange()">Хадгалах</button>
            </div>
        </div>
    </div>
</div>
<?php
require ROOT . "/pages/footer.php"; ?>
<?php require ROOT . "/pages/dataTablefooter.php"; ?>
<script>
    function tshow() {
        var toastElList = [].slice.call(document.querySelectorAll('.toast'))
        var toastList = toastElList.map(function(toastEl) {
            return new bootstrap.Toast(toastEl)
        })
        toastList.forEach(toast => toast.show())
    }

    function edit(id, parent_id) {
        console.log(id);
        $("#edca").val(parent_id);
        $("#ename").val($("#cate1-" + id).text());
        $("#etai").val($("#cate2-" + id).text());
        $("#eid").val(id);
    }

    function editChange() {
        $.ajax({
            url: "caction",
            type: "POST",
            data: {
                editChange: "editChange",
                eid: $("#eid").val(),
                edca: $("#edca").val(),
                etai: $("#etai").val(),
                ename: $("#ename").val()
            },
            error: function(xhr, textStatus, errorThrown) {
                console.log(errorThrown);
            },
            beforeSend: function() {

            },
            success: function(data) {
                location.reload();
            },
            async: true
        });
    }

    function deleteCate(id) {
        if (confirm("Ангилал устгахдаа итгэлтэй байна уу!") == true) {
            $.ajax({
                url: "caction",
                type: "POST",
                data: {
                    deleteCate: "deleteCate",
                    did: id
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