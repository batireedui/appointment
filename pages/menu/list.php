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
    "SELECT id, slug, name, name_en, tai, meta, turul, tuluv, parent_id, erembe FROM menu WHERE parent_id = '0' ORDER BY erembe",
    $id,
    $slug,
    $name,
    $name_en,
    $tai,
    $meta,
    $turul,
    $tuluv,
    $parent_id,
    $erembe
);
$carray = array();

while (_fetch($stCa)) {
    $row = new stdClass();
    $row->id = $id;
    $row->name = $name;
    $row->name_en = $name_en;
    $row->tai = $tai;
    $row->turul = $turul;
    $row->slug = $slug;
    $row->tuluv = $tuluv;
    $row->parent_id = $parent_id;
    $row->erembe = $erembe;
    array_push($carray, $row);
}

function select_submenuTable($prefix, $cate_id, $d)
{
    global $menuTypeArr;
    global $medeeMenuId;
    global $userMenuId;
    $val = "";

    _selectNoParam(
        $stCa,
        $coCA,
        "SELECT id, slug, name, name_en, tai, meta, turul, tuluv, parent_id, erembe FROM menu WHERE parent_id = '$cate_id' ORDER BY erembe",
        $id,
        $slug,
        $name,
        $name_en,
        $tai,
        $meta,
        $turul,
        $tuluv,
        $parent_id,
        $erembe
    );

    $dd = 0;
    while (_fetch($stCa)) {
        $dd++;

        _selectRowNoParam(
            "SELECT name FROM menu WHERE id = '$parent_id'",
            $parent_name
        );

        $val .= "<tr>
                <th scope='row'>$prefix&nbsp;&nbsp; $d.$dd</th>
                <td id='cate1-$id'>$name</td>
                <td id='cate2-$id'>$name_en</td>
                <td id='cate3-$id'><small>$tai</small></td>
                <td id='cate4-$id'>" . $menuTypeArr[$turul] . "</td>
                <td>$parent_name</td>
                <td><div class='editcell' onblur='updateValue(this, \"$id\", \"erembe\")' contenteditable>$erembe</div></td>
                <td>
                <div class='form-check form-switch'>
                    <input class='form-check-input' type='checkbox' role='switch' onchange='tuluvChange($id, this)'";
        $tuluv == 1 ? $val .= 'checked>' : $val .= '>';
        $val .= "</div>
                </td>
                <td><a href='/menu/addmenu?edit=$slug'<span class='btn btn-success btn-sm mx-3'>ЗАСАХ</span></a>";
        if ($id != $medeeMenuId && $id != $userMenuId) {
            $val .= "<span class='btn btn-danger btn-sm' onclick='deleteMenu($id)'>УСТГАХ</span>";
        }
        $val .= "</td></tr>";
        $val .= select_submenuTable("$prefix&nbsp;&nbsp;", $id, $d . "." . $dd);
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
    <h3>ЦЭС</h3>
</div>
<div class="row">
    <div style="text-align: end;">
        <a href="addmenu">
            <button class="btn btn-success">ЦЭС ҮҮСГЭХ</button>
        </a>
    </div>
</div>
<div class="row">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Нэр</th>
                <th>Нэр /Англи/</th>
                <th style="width: 200px;">Тайлбар</th>
                <th>Төрөл</th>
                <th>Дээд цэс</th>
                <th>Эрэмбэ</th>
                <th>Төлөв</th>
                <th></th>
            </tr>
        </thead>
        <tbody class="table-group-divider">
            <?php
            $dd = 1;
            foreach ($carray as $el) { ?>
                <tr>
                    <td class="fw-bold text-primary"><?= $dd ?></td>
                    <td class="fw-bold text-primary" id="cate1-<?= $el->id ?>"><?= $el->name ?></td>
                    <td class="fw-bold text-primary" id="cate2-<?= $el->id ?>"><?= $el->name_en ?></td>
                    <td class="fw-bold text-primary" id="cate3-<?= $el->id ?>"><small><?= $el->tai ?></small></td>
                    <td class="fw-bold text-primary" id="cate4-<?= $el->id ?>"><?= $menuTypeArr[$el->turul] ?></td>
                    <td></td>
                    <td>
                        <div class='editcell fw-bold  text-primary' onblur='updateValue(this, "<?= $el->id ?>", "erembe")' contenteditable><?= $el->erembe ?></div>
                    </td>
                    <td>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" onchange='tuluvChange(<?= $el->id ?>, this)' <?= $el->tuluv == 1 ? "checked" : ""; ?>>
                        </div>
                    </td>
                    <td><a href="/menu/addmenu?edit=<?= $el->slug ?>"><span class="btn btn-success btn-sm mx-3">ЗАСАХ</span></a>
                        <?php if ($el->id != $medeeMenuId && $el->id != $userMenuId) { ?><span class="btn btn-danger btn-sm" onclick="deleteMenu(<?= $el->id ?>)">УСТГАХ</span><?php } ?>
                    </td>
                </tr>
            <?php echo select_submenuTable("", $el->id, $dd);
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