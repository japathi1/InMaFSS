<?php

/* =================================================================================*\
  |* This file is part of InMaFSS                                                    *|
  |* InMaFSS - INformation MAnagement for School Systems - Keep yourself up to date! *|
  |* ############################################################################### *|
  |* Copyright (C) flx5                                                              *|
  |* E-Mail: me@flx5.com                                                             *|
  |* ############################################################################### *|
  |* InMaFSS is free software; you can redistribute it and/or modify                 *|
  |* it under the terms of the GNU Affero General Public License as published by     *|
  |* the Free Software Foundation; either version 3 of the License,                  *|
  |* or (at your option) any later version.                                          *|
  |* ############################################################################### *|
  |* InMaFSS is distributed in the hope that it will be useful,                      *|
  |* but WITHOUT ANY WARRANTY; without even the implied warranty of                  *|
  |* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                            *|
  |* See the GNU Affero General Public License for more details.                     *|
  |* ############################################################################### *|
  |* You should have received a copy of the GNU Affero General Public License        *|
  |* along with InMaFSS; if not, see http://www.gnu.org/licenses/.                   *|
  \*================================================================================= */

define('DS', DIRECTORY_SEPARATOR);
define('CWD', dirname(__FILE__) . DS);
define('INC', CWD . "inc" . DS);
define('PLUGIN_DIR', CWD . DS . "plugins" . DS);

$www = "/" . substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT']) + 1);
define('WWW', $www);

#register_shutdown_function('Shutdown');
set_error_handler('error_handler');
date_default_timezone_set('Europe/Berlin');

header('Content-Type: text/html');

if (file_exists(CWD . "install.php") && file_exists(CWD . "inc/config.php")) {
    die("ERROR: YOU HAVE TO REMOVE THE install.php BEFORE YOU WILL BE ABLE TO USE THIS!");
}

if (!file_exists(CWD . "inc/config.php") && !file_exists(CWD . "install.php")) {
    die("ERROR: CONFIG AND INSTALLER NOT FOUND!");
}

if (!file_exists(CWD . "inc/config.php") && file_exists(CWD . "install.php")) {
    header("Location: " . WWW . "/install.php");
    exit;
}

require_once("inc/variables.php");
require_once("inc/class.config.php");
require_once("inc/core.php");
require_once("inc/sql.php");
require_once("inc/lang.php");
require_once("inc/tpl.php");
require_once("inc/update.php");
require_once("inc/plugin.php");
require_once("inc/parse.php");

$config = new config();
$vars = new variables(new core(), new lang($config->Get("lang")), new MySQL(), new tpl(), new Update(), new pluginManager(), false);

vars::Init($vars);

getVar("sql")->connect($config->Get("dbhost"), $config->Get("dbusr"), $config->Get("dbpass"), $config->Get("dbname"));
getVar("pluginManager")->Init();
getVar("update")->Init();


session_start();

if (isset($_SESSION['user']) && isset($_SESSION['timestamp'])) {
    define('LOGGED_IN', true);
    define('USERNAME', $_SESSION['user']);
} else {
    define('LOGGED_IN', false);
    define('USERNAME', 'GUEST');
}

function lang() {
    return getVar("lang");
}

function filter($input) {
    return getVar("core")->filter($input);
}

function dbquery($input) {
    if (getVar("PLUGIN") || !getVar("sql")->connected) {
        return null;
    }

    return getVar("sql")->dbquery($input);
}

function config($var) {
    if (getVar("PLUGIN")) {
        return null;
    }

    global $config;
    return $config->Get($var);
}

class vars {
    private static $vars;

    public static function Init($vars) {
        self::$vars = $vars;
    }
    
    public static function getVar($var) {
        return self::$vars->Get($var);
    }

    public static function setVar($var, $val) {
        self::$vars->Set($var, $val);
    }
}

function getVar($var) {
    return vars::getVar($var);
}

function setVar($var, $val) {
    vars::setVar($var, $val);
}

function setPlugin($val, $actor) {
    global $vars;
    $vars->setPlugin($val, $actor);
}

function getVersion() {
    include("inc/version.php");
    return $version;
}

function error_handler($errno, $errstr, $errfile, $errline) {
    if (error_reporting() == 0)
        return true; // Ignore Messages with an @ before!

    return false;
}

function Shutdown() {
    $error = error_get_last();
    if ($error != null) {
        ob_end_clean();
        core::SystemError($error['message'], ' in ' . $error['file'] . ' on line ' . $error['line']);
    }
}

?>