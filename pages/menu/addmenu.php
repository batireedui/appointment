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

$myslug = $_GET["edit"] ?? "0";

$edit = 0;

$eid = "";
$ename = "";
$ename_en = "";
$etai = "";
$eparent_id = "";
$etuluv = "";
$eturul = "";
$cateArray = [];
$emeta = "";
$icon = "";
if ($myslug != "0") {
    $edit = 1;
    _selectRowNoParam(
        "SELECT id, name, name_en, tai, parent_id, tuluv, turul, meta, icon FROM menu WHERE slug = '$myslug'",
        $eid,
        $ename,
        $ename_en,
        $etai,
        $eparent_id,
        $etuluv,
        $eturul,
        $emeta,
        $icon
    );

    if ($eid != "") {
        _select(
            $sss,
            $ccc,
            "SELECT cate_id FROM `menu_cate` WHERE menu_id = ?",
            "i",
            [$eid],
            $cate_id
        );
        while (_fetch($sss)) {
            array_push($cateArray, $cate_id);
        }
    }
}
_selectNoParam(
    $stCa,
    $coCA,
    "SELECT id, name, tai, parent_id, tuluv, slug FROM menu WHERE parent_id = '0'",
    $cid,
    $cname,
    $ctai,
    $cparent_id,
    $ctuluv,
    $slug
);
$carray = array();
if ($coCA > 0) {
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
}
?>
<div class="row mt-3">
    <h3><?php echo $eid != "" ? "ЦЭС ЗАСАХ" : "ЦЭС НЭМЭХ" ?></h3>
</div>
<div class="row">
    <form class="row g-3" action="maction" method="post">
        <div class="col">
            <div class="form-floating mb-3">
                <select class="form-select" id="turul" name="turul" onchange="typeSelect()">
                    <option value="submenu" <?php echo $eturul == "submenu" ? " selected" : "" ?>>Дээд цэс (Дотроо дэд цэстэй)</option>
                    <option value="category" <?php echo $eturul == "category" ? " selected" : "" ?>>Ангилал</option>
                    <option value="post" <?php echo $eturul == "post" ? " selected" : "" ?>>Нийтлэл</option>
                    <option value="link" <?php echo $eturul == "link" ? " selected" : "" ?>>Холбоос</option>
                    <option value="page" <?php echo $eturul == "page" ? " selected" : "" ?>>Хуудас</option>
                </select>
                <label for="turul">Цэсний төрөл</label>
            </div>
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="ner" name="ner" placeholder="Цаг үеийн мэдээ" value="<?= $ename ?>" required>
                <label for="ner">Цэсний нэр</label>
                <input type="text" name="eid" value="<?= $eid ?>" style="display: none" <?php echo $eid != "" ? "required" : "" ?>>
            </div>
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="name_en" name="name_en" value="<?= $ename_en ?>" placeholder="Цаг үеийн мэдээ" required>
                <label for="name_en">Цэсний нэр /Англи/</label>
            </div>
            <div class="form-floating mb-3">
                <select class="form-select" id="parent_id" name="parent_id">
                    <option selected value="0">Байхгүй</option>
                    <?php foreach ($carray as $el) { ?>
                        <option value="<?= $el->id ?>" <?php echo $eparent_id == $el->id ? " selected" : "" ?>><?= $el->cname ?></option>
                    <?php echo select_submenu("", $el->id, $eparent_id);
                    } ?>
                </select>
                <label for="parent_id">Дээд цэс</label>
            </div>
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="tai" name="tai" value="<?= $etai ?>" placeholder="Цаг үеийн мэдээ">
                <label for="tai">Цэсний тайлбар</label>
            </div>
            <div class="mb-3">
                <label class="form-label" for="icon">Icon (<a href="https://fontawesome.com/v6/search?q=docs&o=r&m=free" target="_blank">fontawesome.com сайтаас icon хайж html оруулж болно</a>)</label>
                <input type="text" class="form-control" id="icon" name="icon" value='<?= $icon ?>' placeholder='Жишээ нь: <i class="fa-solid fa-file"></i>'>
            </div>
        </div>
        <div class="col">
            <div id="item" class="form-floating mb-3">

            </div>
        </div>
        <div class="col">
            <button type="submit" class="btn btn-primary mb-3" name="<?php echo $myslug != "0" ? "menuEdit" : "menuAdd"; ?>">ХАДГАЛАХ</button>
        </div>
    </form>
</div>

<div class=" toast-container position-fixed bottom-0 end-0 p-3">
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
    function myFunction() {
        let arr = document.getElementsByClassName('form-check postlist');
        let input = document.getElementById("searcher");
        let filter = input.value.toUpperCase();
        for (i = 0; i < arr.length; i++) {
            val = arr[i].getElementsByTagName('label')[0];
            txtValue = val.textContent || val.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                arr[i].style.display = "";
            } else {
                arr[i].style.display = "none";
            }
        }
    }

    let edit = <?= $edit ?>;
    let cateArray = <?php echo json_encode($cateArray); ?>;

    typeSelect();

    function typeSelect() {
        let menu = $('#turul').val();
        $.ajax({
            url: "menutype",
            type: "POST",
            data: {
                menu: menu
            },
            error: function(xhr, textStatus, errorThrown) {
                console.log(errorThrown);
            },
            beforeSend: function() {

            },
            success: function(data) {
                $('#item').html(data)
                if (edit == 1) {
                    check(menu);
                }
            },
            async: true
        });
    }

    function check(turul) {
        if (turul == 'category') {
            let cate = $('[name="cate_id[]"]');
            for (let i = 0; i < cate.length; i++) {
                for (let j = 0; j < cateArray.length; j++) {
                    if (cate[i].value == cateArray[j]) {
                        cate[i].checked = true;
                    }
                }
            }
        } else if (turul == 'post' || turul == 'page') {
            let cate = $('[name="meta"]');

            for (let i = 0; i < cate.length; i++) {
                if (cate[i].value == "<?= $emeta ?>") {
                    cate[i].checked = true;
                }

            }
        } else if (turul == 'link') {
            $('#metahtml').val("<?= $emeta ?>");
        }
        console.log(turul);
    }
</script>
<?php
require ROOT . "/pages/end.php"; ?>