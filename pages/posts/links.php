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
$id = '';
$image = '';
$name = '';
$name_en = '';
$url = '';
$turul = '';
_selectRowNoParam(
    "SELECT id, image, name, name_en, url, turul FROM links where id = '$myslug'",
    $id,
    $image,
    $name,
    $name_en,
    $url,
    $turul
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
    <h3><?php echo $id != "" ? "Хамтрагч байгууллага/Холбоос ЗАСАХ" : "Хамтрагч байгууллага/Холбоос НЭМЭХ" ?></h3>
</div>
<form method="post" action="aaction">
    <div class="row mb-3">
        <div class="col">
            <div class="mb-3">
                <label class="form-label" for="name">Нэр</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= $name ?>" required>
                <input type="text" name="eid" value="<?= $id ?>" style="display: none" <?php echo $id != "" ? "required" : "" ?>>
            </div>
        </div>
        <div class="col">
            <div class="mb-3">
                <label class="form-label" for="name_en">Нэр /Англи/</label>
                <input type="text" class="form-control" id="name_en" name="name_en" value="<?= $name_en ?>" required>
            </div>
        </div>
        <div class="col">
            <div class="row mb-3">
                <div class="col-auto">
                    <label class="form-label">Төрөл</label>
                    <select class="form-control" name="turul">
                        <option value="1" <?php echo $turul == "1" ? " selected" : ""?>>Хамтрагч байгууллага</option>
                        <option value="2" <?php echo $turul == "2" ? " selected" : ""?>>Холбоос</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <div class="mb-3">
                <label class="form-label" for="url">Холбоос /URL/</label>
                <input type="text" class="form-control" id="url" name="url" value="<?= $url ?>" required>
            </div>
        </div>
        <div class="col">
            <label class="form-label">Лого</label>
            <div class="d-flex">
                <input type="text" class="form-control" id="image" name="image" value="<?= $image ?>" />
                <div id="btn" class="btn btn-primary" style="opacity:0.2;cursor:default">Сонгох</div>
                <div id="loading" style="font-size:12px">Түр хүлээнэ үү...</div>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <div class="form-floating mb-3">
                <input type="submit" class="btn btn-success w-100" name="<?php echo $id != "" ? "editLink" : "saveLink" ?>" value="ХАДГАЛАХ">
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
</script>

<script src="/ckeditor/ckeditor.js"></script>
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