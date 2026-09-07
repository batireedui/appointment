<?php
if (isset($_SESSION['user_id'])) {
    if ($_POST['type'] == "menuerembe") {
        $id = $_POST['id'];
        $turul = $_POST['turul'];
        $value = $_POST['value'];
        $sqlval = $turul . "='" . $value . "'";
        $sqlaa = "UPDATE menu SET $sqlval WHERE id = '$id'";
        if ($con->query($sqlaa)) {
            echo "ok";
        } else {
        }
    }
    if ($_POST['type'] == "translate") {
        $id = $_POST['id'];
        $turul = $_POST['turul'];
        $value = $_POST['value'];
        $sqlval = $turul . "='" . $value . "'";
        $sqlaa = "UPDATE language SET $sqlval WHERE id = '$id'";
        if ($con->query($sqlaa)) {
            echo "Амжилттай";
        } else {
            echo "Алдаа гарлаа!";
        }
    }
} else redirect("/login");
