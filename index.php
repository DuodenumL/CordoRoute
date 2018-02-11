<?php
/**
 * Created by PhpStorm.
 * User: duodenum
 * Date: 2018/2/11
 * Time: 下午9:51
 */

define('BASEPATH', '/Users/duodenum/PhpstormProjects/CordoRoute/controller');

define('INDEX_NAME', 'index.php');

$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER['SCRIPT_NAME'];
$replaceCount = 1;
$requestBody = explode('?', str_replace($scriptName, '', $requestUri, $replaceCount))[0];

$uri = explode('/', ltrim($requestBody, '/'));
$filename = BASEPATH;
$classname = '';
$params = [];
$function = 'index';
$kind = 0b100;

const SEG_PATH = 0b100;
const SEG_FUNCTION = 0b010;
const SEG_PARAM = 0b001;

/**
 * /index.php/test/index/hhhh
 * 文件名: Test.php
 * 方法名: index
 * 参数: [hhhh]
 * todo: RESTFUL
 * 默认方法是index，但是如果有参数的话就必须显式写出index
 */

foreach ($uri as $seg) {
    if ($kind & SEG_PATH) {
        $tempFilename = $filename . '/' . ucfirst($seg) . '.php';
        if (is_file($tempFilename)) {
            $filename = $tempFilename;
            $classname = ucfirst($seg);
            $kind >>= 1;
        } else {
            $filename .= "/$seg";
        }
    } elseif ($kind & SEG_FUNCTION) {
        $function = $seg;
        $kind >>= 1;
    } elseif ($kind & SEG_PARAM) {
        $params [] = $seg;
        $kind >>= 1;
    }
}

//如果没找到文件，$kind值依然为SEG_PATH
if ($kind & SEG_PATH) {
    die('file not found');
}

include_once $filename;

if (!class_exists($classname)) {
    die('class not found');
}

$module = new $classname();

if (!method_exists($module, $function)) {
    die('function not found');
}

$module->$function(...$params);
