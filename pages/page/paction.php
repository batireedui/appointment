<?php
if (isset($_SESSION['user_id'])) {
    if (isset($_POST["editPage"])) {
        //$slug = slug_create($_POST['cname'], "category", "slug");
        try {
            $success = _exec(
                "UPDATE posts SET name=?, name_en=?, `body`=?, `body_en`=? WHERE id =?",
                "ssssi",
                [$_POST['name'], $_POST['name_en'], $_POST['body'], $_POST['body_en'], $_POST['id']],
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
        redirect("/page/list");
    }
} else redirect("/login");