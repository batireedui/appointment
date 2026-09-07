<?php
if (isset($_SESSION['user_id'])) {
    if (isset($_POST["updateUser"])) {
        try {
            $success = _exec(
                "UPDATE adminusers SET name=?, email=?, phone=? WHERE id = ?",
                "sssi",
                [$_POST['name'], $_POST['email'], $_POST['phone'], $_SESSION['user_id']],
                $affect
            );
            $_SESSION['user_name'] = $_POST['name'];
            $_SESSION['messages'] = ["Амжилттай хадгалагдлаа."];
        } catch (Exception $e) {
            $_SESSION['messages'] = ["Алдаа гарлаа."];
            echo "Алдаа: " . $e->getMessage() . ' : ' . $e->getFile() . ' : ' . $e->getLine() . ' : Code ' . $e->getCode();
        } finally {
            if (isset($e)) {
                logError($e);
            }
        }
        redirect("/user/config");
    } else if (isset($_POST["passChange"])) {
        $p1 = trim($_POST["oldpass"]);
        $p2 = trim($_POST["pass"]);
        $p3 = trim($_POST["newpass"]);
        if ($p2 != $p3) {
            $_SESSION['messages'] = ["Шинэ нууц үг тохирохгүй байна."];
        } else {
            _selectRowNoParam(
                "SELECT pass FROM adminusers WHERE id = " . $_SESSION['user_id'],
                $hash_pass
            );

            if (!empty($hash_pass)) {
                if (password_verify($p1, $hash_pass)) {
                    try {
                        _exec("UPDATE adminusers SET pass = ? WHERE id=?", "si", [password_hash($p2, PASSWORD_BCRYPT, ["cost" => 8]), $_SESSION['user_id']], $c);
                        $_SESSION['messages'] = ["Нууц үг солигдлоо."];
                    } catch (Exception $e) {
                        $_SESSION['messages'] = ["Алдаа гарлаа."];
                        echo "Алдаа: " . $e->getMessage() . ' : ' . $e->getFile() . ' : ' . $e->getLine() . ' : Code ' . $e->getCode();
                    } finally {
                        if (isset($e)) {
                            logError($e);
                        }
                    }
                    redirect("/user/config");
                } else {
                    $_SESSION['messages'] = ["Хуучин үг тохирохгүй байна."];
                    redirect("/user/config");
                }
            } else {
                $_SESSION['messages'] = ["Хуучин үг тохирохгүй байна."];
                redirect("/user/config");
            }
        }
    } else if (isset($_POST["userAdd"])) {
        $cate_id = $_POST['cate_id'] ?? [];
        try {
            $success = _exec(
                "INSERT INTO adminusers (name, email, phone, role, tuluv, pass ) VALUES (?, ?, ?, ?, ?, ?)",
                "sssiis",
                [$_POST['name'], $_POST['email'], $_POST['phone'], $_POST['role'], 1, password_hash($_POST['phone'], PASSWORD_BCRYPT, ["cost" => 8])],
                $affect
            );

            foreach ($cate_id as $el) {
                $success = _exec(
                    "INSERT INTO admintax (userid, category_id) VALUES (?, ?)",
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
        redirect("/user/list");
    } else if (isset($_POST["userEdit"])) {
        $cate_id = $_POST['cate_id'] ?? [];
        //print_r($_POST);
        if (isset($_POST["etuluv"])) {
            if ($_POST["etuluv"] == "on")
                $etuluv = 1;
            else $etuluv = 0;
        } else $etuluv = 0;
        try {
            $success = _exec(
                "UPDATE adminusers SET name=?, email=?, phone=?, role=?, tuluv=? WHERE id=?",
                "sssiii",
                [$_POST['ename'], $_POST['eemail'], $_POST['ephone'], $_POST['erole'], $etuluv, $_POST['eid']],
                $affect
            );
            _exec(
                "DELETE FROM admintax WHERE userid = ?",
                "i",
                [$_POST['eid']],
                $ss
            );
            foreach ($cate_id as $el) {
                $success = _exec(
                    "INSERT INTO admintax (userid, category_id) VALUES (?, ?)",
                    "ii",
                    [$_POST['eid'], $el],
                    $ss
                );
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
        redirect("/user/list");
    } else if (isset($_POST["deleteUser"])) {
        $too = 0;
        _selectRowNoParam(
            "SELECT count(id) FROM posts WHERE userid = " . $_POST['id'],
            $too
        );
        if ($too > 0) {
            echo "Тухайн хэрэглэгч нийтлэл бичсэн тул устгах боломжгүй! Төлөв өөрчилж болно.";
        } else {
            try {
                $success = _exec(
                    "DELETE FROM adminusers WHERE id =?",
                    "i",
                    [$_POST['id']],
                    $affect
                );
                $success = _exec(
                    "DELETE FROM admintax WHERE userid =?",
                    "i",
                    [$_POST['id']],
                    $affect
                );
                echo "Амжилттай";
            } catch (Exception $e) {
                echo "Алдаа гарлаа.";
                echo "Алдаа: " . $e->getMessage() . ' : ' . $e->getFile() . ' : ' . $e->getLine() . ' : Code ' . $e->getCode();
            } finally {
                if (isset($e)) {
                    logError($e);
                }
            }
        }
    } else if (isset($_POST["getEdit"])) {
        $cateArray = [];

        _select(
            $sss,
            $ccc,
            "SELECT category_id FROM `admintax` WHERE userid = ?",
            "i",
            [$_POST['id']],
            $cate_id
        );
        while (_fetch($sss)) {
            array_push($cateArray, $cate_id);
        }
        echo json_encode($cateArray);
    }
} else redirect("/login");
