<?php
if (isset($_SESSION['user_id'])) {
    if (isset($_POST["categoryAdd"])) {
        $slug = slug_create($_POST['cname'], "category", "slug");

        try {
            $success = _exec(
                "INSERT INTO category (name, tai, parent_id, tuluv, slug) VALUES (?, ?, ?, ?, ?)",
                "ssiis",
                [$_POST['cname'], $_POST['ctai'], $_POST['dca'], 1, $slug],
                $affect
            );
        } catch (Exception $e) {
            echo "Алдаа: " . $e->getMessage() . ' : ' . $e->getFile() . ' : ' . $e->getLine() . ' : Code ' . $e->getCode();
        } finally {
            if (isset($e)) {
                logError($e);
            }
        }
        redirect("/category/list");
    } else if (isset($_POST["tuluvChange"])) {
        $tuluv = 0;
        if ($_POST['tuluv'] == "true") {
            $tuluv = 1;
        }
        try {
            $success = _exec(
                "UPDATE category SET tuluv=? WHERE id = ?",
                "ii",
                [$tuluv, $_POST['cid']],
                $affect
            );
            echo "Төлөв амжилттай солигдлоо!";
        } catch (Exception $e) {
            echo "Алдаа: " . $e->getMessage() . ' : ' . $e->getFile() . ' : ' . $e->getLine() . ' : Code ' . $e->getCode();
        } finally {
            if (isset($e)) {
                logError($e);
            }
        }
    } else if (isset($_POST["editChange"])) {
        $eid = $_POST['eid'];
        $edca = $_POST['edca'];
        $etai = $_POST['etai'];
        $ename = $_POST['ename'];

        $slug = slug_create($_POST['ename'], "category", "slug");
        if ($edca == $eid) $edca = 0;
        try {
            $success = _exec(
                "UPDATE category SET name=?, tai=?, parent_id=?, slug=? WHERE id = ?",
                "ssisi",
                [$ename, $etai, $edca, $slug, $eid],
                $affect
            );
            echo "Амжилттай!";
        } catch (Exception $e) {
            echo "Алдаа: " . $e->getMessage() . ' : ' . $e->getFile() . ' : ' . $e->getLine() . ' : Code ' . $e->getCode();
        } finally {
            if (isset($e)) {
                logError($e);
            }
        }
    } else if (isset($_POST["deleteCate"])) {
        $did = $_POST['did'];
        try {
            _selectRowNoParam(
                "SELECT COUNT(id) FROM `category` WHERE parent_id = '$did'",
                $cateToo
            );
            if ($cateToo > 0) {
                echo "Дэд ангилал бүртгэлтэй байна!";
            } else {
                _selectRowNoParam(
                    "SELECT COUNT(id) FROM `post_cate` WHERE cate_id = '$did'",
                    $postToo
                );
                if ($postToo > 0) {
                    echo "Ангилалд нийтлэл бүртгэлтэй байна!";
                } else {
                    _selectRowNoParam(
                        "SELECT COUNT(id) FROM `menu_cate` WHERE cate_id = '$did'",
                        $menuToo
                    );
                    if ($postToo > 0) {
                        echo "Ангилалыг цэсэнд оруулсан байна!";
                    } else {
                        $succes = _exec(
                            "DELETE FROM category WHERE id = ?",
                            "i",
                            [$did],
                            $affect
                        );
                        echo "Амжилттай!";
                    }
                }
            }
        } catch (Exception $e) {
            echo "Алдаа: " . $e->getMessage() . ' : ' . $e->getFile() . ' : ' . $e->getLine() . ' : Code ' . $e->getCode();
        } finally {
            if (isset($e)) {
                logError($e);
            }
        }
    }
} else redirect("/login");
