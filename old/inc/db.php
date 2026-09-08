<?php
mysqli_report(MYSQLI_REPORT_OFF);

@$con = mysqli_connect(
    DB_HOST,
    DB_USER,
    DB_PASSWORD,
    DB_NAME
);

if (!$con) {

    $error = mysqli_connect_errno();

    if ($error === 1049) {
        die('Ийм нэртэй баз байхгүй');
    }

    if ($error === 1045) {
        die('Хэрэглэгчийн мэдээлэл буруу байна');
    }

    die('Өгөгдлийн сантай холбогдоход алдаа гарлаа');
}

$con->set_charset('utf8mb4');

function _sqlError($stmt = null)
{
    global $con;

    if ($stmt instanceof mysqli_stmt) {
        return mysqli_stmt_error($stmt);
    }

    return mysqli_error($con);
}


/*
|--------------------------------------------------------------------------
| SQL log
|--------------------------------------------------------------------------
*/

function _logSql($sql, $error = null)
{
    $message = date('Y-m-d H:i:s')
        . ' | SQL: '
        . $sql;

    if ($error) {
        $message .= ' | ERROR: ' . $error;
    }

    error_log($message);
}


/*
|--------------------------------------------------------------------------
| Prepare
|--------------------------------------------------------------------------
*/

function _prepare($sql)
{
    global $con;

    $stmt = mysqli_prepare($con, $sql);

    if (!$stmt) {

        $error = _sqlError();

        _logSql($sql, $error);

        throw new Exception(
            'SQL Prepare Error: ' . $error
        );
    }

    return $stmt;
}


/*
|--------------------------------------------------------------------------
| Bind parameters
|--------------------------------------------------------------------------
*/

function _bind($stmt, $types, $params)
{
    if (
        $types === null ||
        $types === '' ||
        empty($params)
    ) {
        return true;
    }

    if (strlen($types) !== count($params)) {

        throw new Exception(
            'SQL parameter count mismatch'
        );
    }

    if (
        !mysqli_stmt_bind_param(
            $stmt,
            $types,
            ...$params
        )
    ) {

        $error = _sqlError($stmt);

        _logSql('', $error);

        throw new Exception(
            'SQL Bind Error: ' . $error
        );
    }

    return true;
}


/*
|--------------------------------------------------------------------------
| SELECT
|--------------------------------------------------------------------------
|
| Жишээ:
|
| _select(
|     $stmt,
|     $count,
|     "SELECT id, name FROM doctors WHERE id=?",
|     "i",
|     [$doctorId],
|     $id,
|     $name
| );
|
*/

function _select(
    &$stmt,
    &$count,
    $sql,
    $types = null,
    $sqlParams = [],
    &...$bindParams
) {

    $stmt = _prepare($sql);

    _bind(
        $stmt,
        $types,
        $sqlParams
    );

    if (!mysqli_stmt_execute($stmt)) {

        $error = _sqlError($stmt);

        _logSql($sql, $error);

        mysqli_stmt_close($stmt);

        throw new Exception(
            'SQL Execute Error: ' . $error
        );
    }

    mysqli_stmt_store_result($stmt);

    $count = mysqli_stmt_num_rows($stmt);

    if (!empty($bindParams)) {

        if (
            !mysqli_stmt_bind_result(
                $stmt,
                ...$bindParams
            )
        ) {

            $error = _sqlError($stmt);

            _logSql($sql, $error);

            mysqli_stmt_close($stmt);

            throw new Exception(
                'SQL Bind Result Error: ' . $error
            );
        }
    }

    return $stmt;
}


/*
|--------------------------------------------------------------------------
| SELECT нэг мөр
|--------------------------------------------------------------------------
*/

function _selectRow(
    $sql,
    $types = null,
    $sqlParams = [],
    &...$bindParams
) {

    _select(
        $stmt,
        $count,
        $sql,
        $types,
        $sqlParams,
        ...$bindParams
    );

    $result = _fetch($stmt);

    _close_stmt($stmt);

    return $result;
}


/*
|--------------------------------------------------------------------------
| SELECT нэг мөр - параметргүй
|--------------------------------------------------------------------------
*/

function _selectRowNoParam(
    $sql,
    &...$bindParams
) {

    return _selectRow(
        $sql,
        null,
        [],
        ...$bindParams
    );
}


/*
|--------------------------------------------------------------------------
| SELECT олон мөр - параметргүй
|--------------------------------------------------------------------------
*/

function _selectNoParam(
    &$stmt,
    &$count,
    $sql,
    &...$bindParams
) {

    return _select(
        $stmt,
        $count,
        $sql,
        null,
        [],
        ...$bindParams
    );
}


/*
|--------------------------------------------------------------------------
| FETCH
|--------------------------------------------------------------------------
*/

function _fetch($stmt)
{
    return mysqli_stmt_fetch($stmt);
}


/*
|--------------------------------------------------------------------------
| Close statement
|--------------------------------------------------------------------------
*/

function _close_stmt($stmt)
{
    if ($stmt instanceof mysqli_stmt) {
        mysqli_stmt_close($stmt);
    }
}


/*
|--------------------------------------------------------------------------
| Execute INSERT / UPDATE / DELETE
|--------------------------------------------------------------------------
|
| Буцаах:
|   true / false
|
| $insertId-ийг reference-р авна.
|
*/

function _exec(
    $sql,
    $types = null,
    $sqlParams = [],
    &$insertId = null
) {

    $stmt = _prepare($sql);

    _bind(
        $stmt,
        $types,
        $sqlParams
    );

    $success = mysqli_stmt_execute($stmt);

    if (!$success) {

        $error = _sqlError($stmt);

        _logSql($sql, $error);

        mysqli_stmt_close($stmt);

        throw new Exception(
            'SQL Execute Error: ' . $error
        );
    }

    $insertId = mysqli_stmt_insert_id($stmt);

    mysqli_stmt_close($stmt);

    return true;
}


/*
|--------------------------------------------------------------------------
| Affected rows
|--------------------------------------------------------------------------
*/

function _execAffected(
    $sql,
    $types = null,
    $sqlParams = [],
    &$affectedRows = null
) {

    $stmt = _prepare($sql);

    _bind(
        $stmt,
        $types,
        $sqlParams
    );

    $success = mysqli_stmt_execute($stmt);

    if (!$success) {

        $error = _sqlError($stmt);

        _logSql($sql, $error);

        mysqli_stmt_close($stmt);

        throw new Exception(
            'SQL Execute Error: ' . $error
        );
    }

    $affectedRows =
        mysqli_stmt_affected_rows($stmt);

    mysqli_stmt_close($stmt);

    return true;
}


/*
|--------------------------------------------------------------------------
| Transaction
|--------------------------------------------------------------------------
*/

function _begin()
{
    global $con;

    if (!mysqli_begin_transaction($con)) {

        throw new Exception(
            'Transaction эхлүүлэхэд алдаа гарлаа'
        );
    }
}


/*
|--------------------------------------------------------------------------
| Commit
|--------------------------------------------------------------------------
*/

function _commit()
{
    global $con;

    if (!mysqli_commit($con)) {

        throw new Exception(
            'Transaction commit хийхэд алдаа гарлаа'
        );
    }
}


/*
|--------------------------------------------------------------------------
| Rollback
|--------------------------------------------------------------------------
*/

function _rollback()
{
    global $con;

    mysqli_rollback($con);
}


/*
|--------------------------------------------------------------------------
| Close connection
|--------------------------------------------------------------------------
*/

function _close($stmt = null)
{
    global $con;

    if ($stmt instanceof mysqli_stmt) {
        mysqli_stmt_close($stmt);
    }

    if ($con instanceof mysqli) {
        mysqli_close($con);
    }
}
