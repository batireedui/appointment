<?php
if (isset($_SESSION['user_id'])) {
    if (isset($_POST["savePost"])) {
        //print_r($_POST);
        $cate_id = $_POST['cate_id'] ?? [];
        $icon = $_POST['icon'] ?? "";
        
        $slug = slug_create($_POST['name'], "category", "slug");
        try {
            $success = _exec(
                "INSERT INTO posts (slug, name, name_en, body, body_en, ognoo, image, turul, userid, tuluv, icon) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                "ssssssssiis",
                [$slug, $_POST['name'], $_POST['name_en'], $_POST['body'], $_POST['body_en'], $_POST['ognoo'], $_POST['image'], 'post', $_SESSION['user_id'], 1, $icon],
                $affect
            );

            $succes = _exec(
                "DELETE FROM post_cate WHERE post_id = ?",
                "i",
                [$affect],
                $af
            );

            foreach ($cate_id as $el) {
                $success = _exec(
                    "INSERT INTO post_cate (post_id, cate_id) VALUES (?, ?)",
                    "ii",
                    [$affect, $el],
                    $ss
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
        redirect("/posts/list");
    } else if (isset($_POST["editPost"])) {
        //print_r($_POST);
        $cate_id = $_POST['cate_id'] ?? [];
        $eid = $_POST['eid'];
        $icon = $_POST['icon'] ?? "";

        $slug = slug_create($_POST['name'], "category", "slug");
        try {
            $success = _exec(
                "UPDATE posts SET slug=?, name=?, name_en=?, body=?, body_en=?, ognoo=?, image=?, turul=?, userid=?, tuluv=?, icon=? WHERE id = ?",
                "ssssssssiisi",
                [$slug, $_POST['name'], $_POST['name_en'], $_POST['body'], $_POST['body_en'], $_POST['ognoo'], $_POST['image'], 'post', $_SESSION['user_id'], 1, $icon, $eid],
                $affect
            );

            $succes = _exec(
                "DELETE FROM post_cate WHERE post_id = ?",
                "i",
                [$eid],
                $af
            );

            foreach ($cate_id as $el) {
                $success = _exec(
                    "INSERT INTO post_cate (post_id, cate_id) VALUES (?, ?)",
                    "ii",
                    [$eid, $el],
                    $ss
                );
            }

            $_SESSION['messages'] = ["Амжилттай засварлагдлаа."];
        } catch (Exception $e) {
            $_SESSION['messages'] = ["Алдаа гарлаа."];
            echo "Алдаа: " . $e->getMessage() . ' : ' . $e->getFile() . ' : ' . $e->getLine() . ' : Code ' . $e->getCode();
        } finally {
            if (isset($e)) {
                logError($e);
            }
        }
        redirect("/posts/list");
    } else if (isset($_POST["deletePost"])) {
        $did = $_POST['id'];
        try {
            $succes = _exec(
                "DELETE FROM posts WHERE id = ? and turul = 'post'",
                "i",
                [$did],
                $affect
            );
            $succes = _exec(
                "DELETE FROM post_cate WHERE post_id = ?",
                "i",
                [$did],
                $affect
            );
            echo "Амжилттай";
        } catch (Exception $e) {
            echo "Алдаа: " . $e->getMessage() . ' : ' . $e->getFile() . ' : ' . $e->getLine() . ' : Code ' . $e->getCode();
        } finally {
            if (isset($e)) {
                logError($e);
            }
        }
    } else if (isset($_POST["saveLink"])) {
        try {
            $succes = _exec(
                "INSERT INTO links (image, name, name_en, url, turul) VALUES (?, ?, ?, ?, ?)",
                "ssssi",
                [$_POST['image'], $_POST['name'], $_POST['name_en'], $_POST['url'], $_POST['turul']],
                $affect
            );
            //echo "Амжилттай!";
        } catch (Exception $e) {
            echo "Алдаа: " . $e->getMessage() . ' : ' . $e->getFile() . ' : ' . $e->getLine() . ' : Code ' . $e->getCode();
        } finally {
            if (isset($e)) {
                logError($e);
            }
        }
        redirect("/posts/linkList");
    } else if (isset($_POST["editLink"])) {
        try {
            $succes = _exec(
                "UPDATE links SET image=?, name=?, name_en=?, url=?, turul=? WHERE id=?",
                "ssssii",
                [$_POST['image'], $_POST['name'], $_POST['name_en'], $_POST['url'], $_POST['turul'], $_POST['eid']],
                $affect
            );
            //echo "Амжилттай!";
        } catch (Exception $e) {
            echo "Алдаа: " . $e->getMessage() . ' : ' . $e->getFile() . ' : ' . $e->getLine() . ' : Code ' . $e->getCode();
        } finally {
            if (isset($e)) {
                logError($e);
            }
        }
        redirect("/posts/linkList");
    } else if (isset($_POST["deleteLink"])) {
        $did = $_POST['id'];
        try {
            $succes = _exec(
                "DELETE FROM links WHERE id = ?",
                "i",
                [$did],
                $affect
            );
            echo "Амжилттай";
        } catch (Exception $e) {
            echo "Алдаа: " . $e->getMessage() . ' : ' . $e->getFile() . ' : ' . $e->getLine() . ' : Code ' . $e->getCode();
        } finally {
            if (isset($e)) {
                logError($e);
            }
        }
    } else redirect("/posts/list");
} else redirect("/login");
