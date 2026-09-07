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
_selectRow(
    "SELECT id, name, name_en, slug, tuluv, body, body_en FROM posts WHERE slug = ? and turul = ?",
    "ss",
    [$myslug, 'page'],
    $id,
    $name,
    $name_en,
    $slug,
    $tuluv,
    $body,
    $body_en
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
<form method="post" action="paction">
    <div class="row mb-3">
        <div class="col">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="cname" name="name" value="<?= $name ?>" required>
                <label for="name">Нэр</label>
                <input type="text" name="id" style="display: none;" value="<?= $id ?>" required>
            </div>
        </div>
        <div class="col">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="ctai" name="name_en" value="<?= $name_en ?>" required>
                <label for="name_en">Нэр /Англи/</label>
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
                <input type="submit" class="btn btn-success w-100" name="editPage" value="ХАДГАЛАХ">
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
    CKEDITOR.replace('editor', {
        extraPlugins: "file-manager",
        Flmngr: {
            apiKey: "zAo7V85b", // Default free key
            urlFileManager: 'http://adminagri.org/flmngr',
            urlFiles: 'http://adminagri.org/iree',
            onFinish: (files) => {
                console.log("User picked:");
                console.log(files);
            }
        },

        // Toolbar is configured automatically
    });

    CKEDITOR.replace('editor_en', {
        extraPlugins: "file-manager",
        Flmngr: {
            apiKey: "zAo7V85b", // Default free key
            urlFileManager: 'http://adminagri.org/flmngr',
            urlFiles: 'http://adminagri.org/iree',
            onFinish: (files) => {
                console.log("User picked:");
                console.log(files);
            }
        },

        // Toolbar is configured automatically
    });
</script>
<!--script src="/ckeditor5file/build/ckeditor.js"></script>
<script src="/ckfinder/ckfinder.js"></script>
<script>
    let ckeditor5;
    ClassicEditor.create(
        document.querySelector('#editor'), {

            Flmngr: {
                apiKey: "FLMNFLMN", // default free key
                urlFileManager: 'http://adminagri.org/images', // demo server
                urlFiles: 'http://adminagri.org/images', // demo file storage
            }

        }
    ).then(
        (editor) => {
            ckeditor5 = editor; // or save the link where you need
        }
    ).catch(
        (error) => {
            // ...
        }
    );
</script>-->
<?php
require ROOT . "/pages/end.php"; ?>