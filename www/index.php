<?php
session_start();

//$_SESSION['user_id'] = 1;

date_default_timezone_set('Asia/Singapore');
ini_set('display_errors', 1);
define('ROOT', dirname(dirname(__FILE__)));

require ROOT . '/inc/conf.php';
require ROOT . '/inc/db.php';

//use Ausi\SlugGenerator\SlugGenerator;

$page = @$_SERVER['REDIRECT_URL'];

$favi = DOMAIN . "/images/logo.png";

$menuTypeArr = ['category' => 'Ангилал', 'post' => 'Нийтлэл', 'link' => 'Холбоос', 'submenu' => 'Дээд цэс'];
$userTypeArr = ['1' => 'Admin', '2' => 'Нийтлэгч'];

$linkArr = ['1' => 'Хамтрагч байгууллага', '2' => 'Холбоос'];
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'mn';
}

$lang = "";
if ($_SESSION['lang'] == 'mn') {
    $lang = "";
} else $lang = "_en";


$local = array("lang" => "lang");

$webname = "ХАА";

if (empty($page)) {
    require ROOT . '/pages/home.php';
} else {
    $script = ROOT . "/pages$page.php";
    if (file_exists($script)) {
        require $script;
    } else {
        require ROOT . '/pages/404.php';
    }
}
/*
function slug_create($ner, $table, $field)
{
    $generator = new SlugGenerator;
    $slug = mb_strtolower($ner);
    $slug = str_replace("ө", "u", $slug);
    $slug = str_replace("ү", "u", $slug);
    $slug = substr($slug, 0, 199);
    $slug = $generator->generate($slug);
    _selectRowNoParam(
        "SELECT COUNT(id) FROM $table WHERE $field = '$slug'",
        $cateToo
    );
    $dd = 1;
    if ($cateToo > 0) {
        $slug = substr($slug, 0, 195);
        $slug .= $dd;
        $slug = slug_create($slug, $table, $field);
    }
    return $slug;
}
*/
function slug_create($ner, $table, $field)
{
    $length = 18;
    $stringSpace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $pieces = [];
    $max = mb_strlen($stringSpace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $pieces[] = $stringSpace[random_int(0, $max)];
    }

    $slug = implode('', $pieces);

    _selectRowNoParam(
        "SELECT COUNT(id) FROM $table WHERE $field = '$slug'",
        $cateToo
    );
    $dd = 1;
    if ($cateToo > 0) {
        $slug = substr($slug, 0, 195);
        $slug .= $dd;
        $slug = slug_create($slug, $table, $field);
    }
    return $slug;
}

function dayofweek($ognoo)
{
    $day = date('w', strtotime($ognoo));
    switch ($day) {
        case 1:
            return "Даваа";
        case 2:
            return "Мягмар";
        case 3:
            return "Лхагва";
        case 4:
            return "Пүрэв";
        case 5:
            return "Баасан";
        case 6:
            return "Бямба";
        case 0:
            return "Ням";
        default:
            return $day;
    }
}

function logError($e)
{
    _exec(
        "insert into error set
        ognoo = now(),
        ip=?,
        error_code=?,
        error=?,
        file=?,
        line=?,
        site='user'
    ",
        'sissi',
        [getIpAddress(), $e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine()],
        $count
    );
}

function redirect($url)
{
    header("Location: $url");
    exit;
}

function dd($arr, $exit = false)
{
    echo '<pre>';
    print_r($arr);
    if ($exit) {
        exit;
    }
}

function post($name, $length = null)
{
    $value = $_POST[$name];

    $value = addslashes($value);

    if (!is_null($length) && mb_strlen($value) > $length) {
        $value = mb_substr($value, 0, $length);
        // Security alert! DB write, email send
        echo "<br>security alert : $name индекстэй өгөгдөл $length уртаас хэтэрсэн өгөгдөлтэй байна!<br>";
    }

    return $value;
}

function get($name, $length = null)
{
    $value = $_GET[$name];

    $value = addslashes($value);

    if (!is_null($length) && mb_strlen($value) > $length) {
        $value = mb_substr($value, 0, $length);
        // Security alert! DB write, email send
        echo "<br>security alert : $name индекстэй өгөгдөл $length уртаас хэтэрсэн өгөгдөлтэй байна!<br>";
    }

    return $value;
}

function getIpAddress()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) // check ip from share internet
    {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) // to check ip is pass from proxy
    {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    return $_SERVER['REMOTE_ADDR'];
}

function formatMoney($value)
{
    if ($value == '0') {
        return '';
    } else {
        $value = number_format(sprintf('%0.2f', preg_replace("/[^0-9.]/", "", $value)), 0);
        return $value;
    }
}
function ognoo()
{
    $tz = 'Asia/Ulaanbaatar';
    $timestamp = time();
    $dt = new DateTime("now", new DateTimeZone($tz)); //first argument "must" be a string
    $dt->setTimestamp($timestamp); //adjust the object to correct timestamp
    return $dt->format('Y/m/d H:i:s');
}
function ognooday()
{
    $tz = 'Asia/Ulaanbaatar';
    $timestamp = time();
    $dt = new DateTime("now", new DateTimeZone($tz)); //first argument "must" be a string
    $dt->setTimestamp($timestamp); //adjust the object to correct timestamp
    return $dt->format('Y/m/d');
}

function select_subcate($prefix, $cate_id, $active = null)
{

    $val = "";

    _selectNoParam(
        $stCa,
        $coCA,
        "SELECT id, name, tai, parent_id, tuluv FROM category WHERE parent_id = '$cate_id'",
        $cid,
        $cname,
        $ctai,
        $cparent_id,
        $ctuluv
    );

    $dd = 0;
    while (_fetch($stCa)) {
        $dd++;
        $val .= "<option value='$cid'";
        $cid == $active ? $val .= " selected" : "";
        $val .= ">$prefix&nbsp;&nbsp; $cname</option>";
        $val .= select_subcate("$prefix&nbsp;&nbsp;", $cid);
    }
    return $val;
}

function select_submenu($prefix, $cate_id, $active = null)
{

    $val = "";

    _selectNoParam(
        $stCa,
        $coCA,
        "SELECT id, name, tai, parent_id, tuluv FROM menu WHERE parent_id = '$cate_id'",
        $cid,
        $cname,
        $ctai,
        $cparent_id,
        $ctuluv
    );

    $dd = 0;
    while (_fetch($stCa)) {
        $dd++;
        $val .= "<option value='$cid'";
        $cid == $active ? $val .= " selected" : "";
        $val .= ">$prefix&nbsp;&nbsp; $cname</option>";
        $val .= select_subcate("$prefix&nbsp;&nbsp;", $cid);
    }
    return $val;
}

function select_subcatecheck(int $prefix, $cate_id, $active = null)
{

    $val = "";

    _selectNoParam(
        $stCa,
        $coCA,
        "SELECT id, name, tai, parent_id, tuluv FROM category WHERE parent_id = '$cate_id'",
        $cid,
        $cname,
        $ctai,
        $cparent_id,
        $ctuluv
    );

    $dd = 0;
    $prefix += 1;
    while (_fetch($stCa)) {
        $dd = $dd + 1;
        $val .= "<div class='form-check' style='margin-left: $prefix" . "rem'><input class='form-check-input' type='checkbox' value='$cid' name='cate_id[]'  id='cate$cid'";
        $cid == $active ? $val .= " checked" : "";
        $val .= "><label class='form-check-label' for='cate$cid'>$cname</label></div>";
        $val .= select_subcatecheck($prefix, $cid);
    }

    return $val;
}
function _format_date($ognoo){
    return date_format(date_create($ognoo),"Y.m.d");
}