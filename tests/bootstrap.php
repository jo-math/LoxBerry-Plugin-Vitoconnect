<?php
set_include_path(get_include_path() . PATH_SEPARATOR . '/opt/project/webfrontend/htmlauth');
set_include_path(get_include_path() . PATH_SEPARATOR . '/opt/project/tests/sys');
print("Include path: " . get_include_path());
setGlobalsBootstrap();



function setGlobalsBootstrap() {
    global $lbhomedir;
    global $lbpplugindir;
    global $lbsdatadir;
    global $lbconfigdir;
    global $lbplogdir;
    global $lbpconfigdir;
    global $lbpplugindir;
    $lbpplugindir = "Vitoconnect2";
    define ("LBPPLUGINDIR", $lbpplugindir);
    define("LBPHTMLAUTHDIR", "/opt/loxberry/webfrontend/htmlauth/plugins/Vitoconnect2");
    $lbhomedir = __DIR__ . "/lbhomedir";
    $lbconfigdir = __DIR__ . "/lbhomedir/config";
    $lbsdatadir = __DIR__ . "/lbhomedir/data";

    $lbplogdir = __DIR__ . "/lbhomedir/log";
    $lbpconfigdir = $lbhomedir . "/config/plugins/" . LBPPLUGINDIR;
    putenv("LBHOMEDIR=$lbhomedir");
    putenv("LBPPLUGINDIR=$lbpplugindir");

}