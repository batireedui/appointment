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

_selectRowNoParam(
    "SELECT id, name, email, phone, pass, role, tuluv FROM adminusers WHERE id = " . $_SESSION['user_id'],
    $uid,
    $uname,
    $uemail,
    $uphone,
    $pass,
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
<div class="row">
    <div class="col-md-6">
        <form method="post" action="uaction">
            <div class="col">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="name" name="name" value="<?= $uname ?>">
                    <label for="name">Хэрэглэгчийн нэр</label>
                </div>
            </div>
            <div class="col">
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" name="email" value="<?= $uemail ?>">
                    <label for="email">Email</label>
                </div>
            </div>
            <div class="col">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="phone" name="phone" value="<?= $uphone ?>" required>
                    <label for="phone">Утасны дугаар</label>
                </div>
            </div>
            <div class="col">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="usert" name="usert" value="<?= $userTypeArr[$urole] ?>" readonly required>
                    <label for="usert">Хэрэглэгчийн төрөл</label>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-floating mb-3">
                        <input type="submit" class="btn btn-success w-100" name="updateUser" value="ХАДГАЛАХ">
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-6">
        <form method="post" action="uaction" onsubmit="return checkPass();">
            <div class="col">
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="oldpass" name="oldpass" value="" required>
                    <label for="oldpass">Хуучин нууц үг</label>
                </div>
            </div>
            <div class="col">
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="pass" name="pass" value="" required>
                    <label for="pass">Шинэ нууц үг</label>
                </div>
            </div>
            <div class="col">
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="newpass" name="newpass" value="" required>
                    <label for="newpass">Шинэ нууц үг дахин оруул</label>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-floating mb-3">
                        <input type="submit" class="btn btn-warning w-100" name="passChange" value="НУУЦ ҮГ ӨӨРЧЛӨХ">
                    </div>
                </div>
            </div>
        </form>
    </div>
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
    function tshow() {
        var toastElList = [].slice.call(document.querySelectorAll('.toast'))
        var toastList = toastElList.map(function(toastEl) {
            return new bootstrap.Toast(toastEl)
        })
        toastList.forEach(toast => toast.show())
    }

    function checkPass() {
        if ($('#pass').val() != $('#newpass').val()) {
            alert("Шинэ нууц үг тохирохгүй байна");
            return false;
        }
        return true;
    }
</script>
<?php
require ROOT . "/pages/end.php"; ?>