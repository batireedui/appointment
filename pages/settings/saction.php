<?php
if (isset($_SESSION['user_id'])) {
    if (isset($_POST["saveSetting"])) {
        try {
            $success = _exec(
                "UPDATE main SET name=?, name_en=?, address=?, address_en=?, email=?, phone=?, facebook=?, youtube=?, meta=?",
                "sssssssss",
                [$_POST['name'], $_POST['name_en'], $_POST['address'], $_POST['address_en'], $_POST['email'], $_POST['phone'], $_POST['facebook'], $_POST['youtube'], $_POST['meta']],
                $affect
            );
            $_SESSION['messages'] = ["Амжилттай хадгалагдлаа."];
        } catch (Exception $e) {
            $_SESSION['messages'] = ["Алдаа гарлаа."];
            echo "Алдаа: " . $e->getMessage() . ' : ' . $e->getFile() . ' : ' . $e->getLine() . ' : Code ' . $e->getCode();
        } finally {
            if (isset($e)) {
                logError($e);
            }
        }
        redirect("/settings/main");
    }
} else redirect("/login");