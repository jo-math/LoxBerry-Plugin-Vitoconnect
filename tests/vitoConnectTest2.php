<?php



use PHPUnit\Framework\TestCase;
$testparams = "foo";
setGlobals();
//$argv=["action=setvalue","option=heating.circuits.0.operating.programs.reduced","value=17"];
$argv=["action=summary"];
require_once "vitoconnect.php";


class phpMQTTTest2 extends TestCase
{
    public function testCall() {

    }

};


function setGlobals() {
    global $testOverrideLogLevel;
    $testOverrideLogLevel = 7;
}