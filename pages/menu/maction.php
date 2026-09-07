<?php
if (isset($_SESSION['user_id'])) {
    if (isset($_POST["menuAdd"])) {
        //print_r($_POST);
        $cate_id = $_POST['cate_id'] ?? [];
        $parent_id = $_POST['parent_id'] ?? 0;
        $meta = $_POST['meta'] ?? 0;
        $icon = $_POST['icon'] ?? "";
        $slug = slug_create($_POST['ner'], "menu", "slug");

        try {
            $success = _exec(
                "INSERT INTO menu (slug, name, name_en, tai, meta, turul, tuluv, parent_id, erembe, icon) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                "ssssssiiis",
                [$slug, $_POST['ner'], $_POST['name_en'], $_POST['tai'], $meta, $_POST['turul'], 1, $parent_id, 1, $icon],
                $affect
            );
            if (count($cate_id) > 0) {
                foreach ($cate_id as $el) {
                    $success = _exec(
                        "INSERT INTO menu_cate (menu_id, cate_id) VALUES (?, ?)",
                        "ii",
                        [$affect, $el],
                        $ss
                    );
                }
            } else {
                $success = _exec(
                    "DELETE FROM menu_cate WHERE menu_id=?",
                    "i",
                    [$affect],
                    $lr
                );
            }
            $_SESSION['messages'] = ["Амжилттай хадгалагдлаа."];
        } catch (Exception $e) {
            $_SESSION['messages'] = ["Алдаа гарлаа."];
            echo "Алдаа: " . $e->getMessage() . ' : ' . $e->getFile() . ' : ' . $e->getLine() . ' : Code ' . $e->getCode();
        } finally {
            if (isset($e)) {
                logError($e);
            }
        }
        redirect("/menu/list");
    }
    else if (isset($_POST["menuEdit"])) {
        // print_r($_POST);
        $cate_id = $_POST['cate_id'] ?? [];
        $parent_id = $_POST['parent_id'] ?? 0;
        $meta = $_POST['meta'] ?? 0;
        $slug = slug_create($_POST['ner'], "menu", "slug");
        $icon = $_POST['icon'] ?? "";
        $eid = $_POST['eid'];

        try {
            $success = _exec(
                "UPDATE menu SET slug=?, name=?, name_en=?, tai=?, meta=?, turul=?, tuluv=?, parent_id=?, icon=? WHERE id=?",
                "ssssssiisi",
                [$slug, $_POST['ner'], $_POST['name_en'], $_POST['tai'], $meta, $_POST['turul'], 1, $parent_id, $icon, $eid],
                $affect
            );

            $success = _exec(
                "DELETE FROM menu_cate WHERE menu_id=?",
                "i",
                [$eid],
                $lr
            );

            if (count($cate_id) > 0) {
                foreach ($cate_id as $el) {
                    $success = _exec(
                        "INSERT INTO menu_cate (menu_id, cate_id) VALUES (?, ?)",
                        "ii",
                        [$eid, $el],
                        $ss
                    );
                }
            }
            $_SESSION['messages'] = ["Амжилттай засагдлаа."];
        } catch (Exception $e) {
            $_SESSION['messages'] = ["Алдаа гарлаа."];
            echo "Алдаа: " . $e->getMessage() . ' : ' . $e->getFile() . ' : ' . $e->getLine() . ' : Code ' . $e->getCode();
        } finally {
            if (isset($e)) {
                logError($e);
            }
        }
        redirect("/menu/list");
    } else if (isset($_POST["deleteMenu"])) {
        $id = $_POST['id'];
        try {
            $success = _exec(
                "DELETE FROM menu WHERE id=?",
                "i",
                [$id],
                $affect
            );

            $success = _exec(
                "DELETE FROM menu_cate WHERE menu_id=?",
                "i",
                [$id],
                $lr
            );
            echo "Амжилттай устгагдлаа";
        } catch (Exception $e) {
            //$_SESSION['messages'] = ["Алдаа гарлаа."];
            echo "Алдаа: " . $e->getMessage() . ' : ' . $e->getFile() . ' : ' . $e->getLine() . ' : Code ' . $e->getCode();
        } finally {
            if (isset($e)) {
                logError($e);
            }
        }
    } else echo "NONO";
} else redirect("/login");
