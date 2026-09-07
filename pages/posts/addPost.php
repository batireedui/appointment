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
$myslug = $_GET["edit"] ?? 0;
$id = "";
$name = "";
$name_en = "";
$slug = "";
$tuluv = "";
$body = "";
$body_en = "";
$ognoo = date('Y-m-d');
$image = "";
$icon = "";
_selectRow(
    "SELECT id, name, name_en, slug, tuluv, body, body_en, ognoo, image, icon FROM posts WHERE slug = ? and turul = ?",
    "ss",
    [$myslug, 'post'],
    $id,
    $name,
    $name_en,
    $slug,
    $tuluv,
    $body,
    $body_en,
    $ognoo,
    $image,
    $icon
);

$cateArray = [];

if ($id != "") {
    _select(
        $sss,
        $ccc,
        "SELECT cate_id FROM `post_cate` WHERE post_id = ?",
        "i",
        [$id],
        $cate_id
    );
    while (_fetch($sss)) {
        array_push($cateArray, $cate_id);
    }
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
    <h3><?php echo $id != "" ? "МЭДЭЭ ЗАСАХ" : "МЭДЭЭ НЭМЭХ" ?></h3>
</div>
<form method="post" action="aaction">
    <div class="row mb-3">
        <div class="col">
            <div class="mb-3">
                <label class="form-label" for="name">Гарчиг</label>
                <input type="text" class="form-control" id="cname" name="name" value="<?= $name ?>" required>
                <input type="text" name="eid" value="<?= $id ?>" style="display: none" <?php echo $id != "" ? "required" : "" ?>>
            </div>
            <div class="mb-3">
                <label class="form-label" for="name_en">Гарчиг /Англи/</label>
                <input type="text" class="form-control" id="ctai" name="name_en" value="<?= $name_en ?>" required>
            </div>
            <div class="row mb-3">
                <div class="col-auto">
                    <label class="form-label">Огноо</label>
                    <input type="date" class="form-control" name="ognoo" value="<?= date('Y-m-d', strtotime($ognoo)) ?>">
                </div>
                <div class="col">
                    <label class="form-label">Онцлох зураг (<a href="https://www.iloveimg.com/" target="_blank">Хэмжээ шахах сайт</a>)</label>
                    <div class="d-flex">
                        <input type="text" class="form-control" id="image" name="image" value="<?= $image ?>" />
                        <div id="btn" class="btn btn-primary" style="opacity:0.2;cursor:default">Сонгох</div>
                        <div id="loading" style="font-size:12px">Түр хүлээнэ үү...</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <label class="form-label">Ангилал сонгох</label>
            <div id="category">
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Мэдээлэл</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Мэдээлэл /Англи/</button>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                <textarea id="editor" name="body"><?= $body ?></textarea>
            </div>
            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <textarea id="editor_en" name="body_en"><?= $body_en ?></textarea>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <div class="form-floating mb-3">
                <input type="submit" class="btn btn-success w-100" name="<?php echo $id != "" ? "editPost" : "savePost" ?>" value="ХАДГАЛАХ">
            </div>
        </div>
    </div>
</form>
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

<script src="/ckeditor/ckeditor.js"></script>
<script>
    let cateArray = <?php echo json_encode($cateArray); ?>;

    typeSelect();

    function typeSelect() {
        let menu = $('#menuType').val();
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
                $('#category').html(data);
                checkCategory()
            },
            async: true
        });
    };

    function checkCategory() {
        let cate = $('[name="cate_id[]"]');
        for (let i = 0; i < cate.length; i++) {
            for (let j = 0; j < cateArray.length; j++) {
                if (cate[i].value == cateArray[j]) {
                    cate[i].checked = true;
                }
            }
        }
    }

    CKEDITOR.replace('editor', {
        extraPlugins: "file-manager",
        Flmngr: {
            apiKey: "FLMNGR", // Default free key
            urlFileManager: '<?= $urlFileManager ?>',
            urlFiles: '<?= $urlFiles ?>',
            onFinish: (files) => {
                console.log("User picked:");
                console.log(files);
            }
        },
    });

    CKEDITOR.replace('editor_en', {
        extraPlugins: "file-manager",
        Flmngr: {
            apiKey: "zAo7V85b", // Default free key
            urlFileManager: '<?= $urlFileManager ?>',
            urlFiles: '<?= $urlFiles ?>',
            onFinish: (files) => {
                console.log("User picked:");
                console.log(files);
            }
        },
    });
</script>
<script id="rendered-js" type="module">
    // In real app replace with:
    // import Flmngr from "flmngr";
    import Flmngr from "https://cdn.skypack.dev/flmngr";

    Flmngr.load({
            apiKey: "zAo7V85b",
            urlFileManager: '<?= $urlFileManager ?>',
            urlFiles: '<?= $urlFiles ?>'
        },

        {
            onFlmngrLoaded: () => {
                attachOnClickListenerToButton();
            }
        });


    function attachOnClickListenerToButton() {
        let elBtn = document.getElementById("btn");

        // Style button as ready to be pressed
        elBtn.style.opacity = 1;
        elBtn.style.cursor = "pointer";
        let elLoading = document.getElementById("loading");
        elLoading.parentElement.removeChild(elLoading);

        // Add a listener for selecting files
        elBtn.addEventListener("click", () => {
            selectFiles();
        });
    }

    function selectFiles() {
        Flmngr.open({
            isMultiple: false,
            acceptExtensions: ["png", "jpeg", "jpg", "webp", "gif"],
            onFinish: files => {
                showSelectedImage(files);
            }
        });

    }

    function showSelectedImage(files) {
        let file = files[0];
        $('#image').val(file.url);
    }
    //# sourceURL=pen.js
</script>

<?php
require ROOT . "/pages/end.php"; ?>