<?php

/*
 * Время от времени возникает задача в которой требуется
 * выполнить какие-то действия в отрыве от движка, но для
 * которых требуются стандартные классы. Раньше я копипастил
 * все это из index.php теперь я вынес это в отдельный файл
 * и могу просто подключать.
 */


/**
 * Абсолютный путь к корню сайта.
 */
//$_root = $_SERVER['DOCUMENT_ROOT'];
$_root = "/var/www/test.apteka03plus.ru/data/www/test.apteka03plus.ru/";

/**
 * Если мы не в корне, добавлять слэши к концу строки.
 */
if ($_root[strlen($_root) - 1] != '/')
    $_root .= '/';

/**
 * Определяем константы.
 * ROOT_DIR - корневая папка.
 */
define('ROOT_DIR', $_root);
define("FRAMEWORK_DIR", ROOT_DIR . "system/framework/");
define("FRAMEWORK_CONST", FRAMEWORK_DIR . "FConstants.php");
/**
 * Папка с шаблонами.
 */
define('TPL_DIR', ROOT_DIR . "templs/");


/**
 * Папки с кешом
 */
define('EL_CACHE_DIR', TPL_DIR . "cache/el_cache/");
define('EL_CACHE_MAPS_DIR', EL_CACHE_DIR . "maps/");
define('EL_CACHE_PAGES_DIR', EL_CACHE_DIR . "pages/");
define('EL_CACHE_MODULES_DIR', EL_CACHE_DIR . "modules/");
define('EL_CACHE_PAGES_COMMON_DIR', EL_CACHE_PAGES_DIR . "common/");
define('EL_CACHE_PAGES_USER_DIR', EL_CACHE_PAGES_DIR . "user/");
define('EL_CACHE_MODULES_COMMON_DIR', EL_CACHE_MODULES_DIR . "common/");
define('EL_CACHE_MODULES_USER_DIR', EL_CACHE_MODULES_DIR . "user/");

/**
 * Папка с шаблонами пользовательской части сайта.
 */
define('USER_TPL_DIR', TPL_DIR . "users/");
define('ENGINE_TPL_DIR', TPL_DIR . "engine/");
define('ENGINE_MODULE_TPL_DIR', ENGINE_TPL_DIR . "modules/");
define('DYN_TPL_DIR', TPL_DIR . "dynamic/");

define('SYS_DIR', ROOT_DIR . "system/");
define('ELIB_DIR', SYS_DIR . "elib/");
define('ENGINE_DIR', SYS_DIR . "engine/");
define('KERNEL_DIR', SYS_DIR . "kernel/");
define('IMAGES_DIR', ROOT_DIR . "images/");
define('FILES_DIR', ROOT_DIR . "files/");

define('FILES_TEMP_DIR', FILES_DIR . "temp/");
define('SITE_OFF_DIR', FILES_DIR . "siteoff/");

define('MODULES_DIR', ROOT_DIR . "modules/");

define('JS_DIR', "/js/");
define('JS_MOD_DIR', JS_DIR . "modules/");

define('JS_DIR_BACKUP', ROOT_DIR . "js/");
define('JS_MOD_DIR_BACKUP', JS_DIR_BACKUP . "modules/");

define('ADMIN_DIR', ROOT_DIR . "engine/");

define('CAPTCHA_DIR', IMAGES_DIR . "captcha/");

/**
 * Папка с формами
 */
define("FORM_DIR", ROOT_DIR . "system/xml/forms/");

/**
 * Подключаем файл с настройками сайта.
 */
require_once("config.php");

/**
 * Автоподключение всех вызываемых классов.
 */
 spl_autoload_register(function ($className) {
    /**
     * Ищем в папке system/kernel/
     */
    if (file_exists(ROOT_DIR . "system/kernel/" . "$className" . '.php')) {
        require_once(ROOT_DIR . "system/kernel/" . "$className" . '.php');
    }
    /**
     * Если не находим, то в папке system/elib/
     */ else if (file_exists(ROOT_DIR . "system/elib/" . "$className" . '.php')) {
        require_once(ROOT_DIR . "system/elib/" . "$className" . '.php');
    }
    /**
     * Если не находим, то в папке system/engine/
     */ else if (file_exists(ROOT_DIR . "system/engine/" . "$className" . '.php')) {
        require_once(ROOT_DIR . "system/engine/" . "$className" . '.php');
    }
    /**
     * Если не находим, то в папке plugins/
     */ else if (file_exists(ROOT_DIR . "plugins/$className/" . "$className" . '.php')) {
        require_once(ROOT_DIR . "plugins/$className/" . "$className" . '.php');
    }
    /**
     * Иначе прерываем работу скрипта и выдаем сообщение, что файл не найден.
     */ else if (file_exists(ROOT_DIR . "engine/" . "$className" . '.php')) {
        require_once(ROOT_DIR . "engine/" . "$className" . '.php');
    }
    /**
     * Иначе прерываем работу скрипта и выдаем сообщение, что файл не найден.
     */ else if (file_exists(ROOT_DIR . "system/smarty/" . "$className" . '.php')) {
        require_once(ROOT_DIR . "system/smarty/" . "$className" . '.php');
    }
});

require_once(FRAMEWORK_CONST); // main framework constants.
?>
