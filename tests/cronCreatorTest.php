<?php



use PHPUnit\Framework\TestCase;
$testparams = "foo";
setGlobals();
require_once "cronCreator.php";
function prepareTestCron() {
    rmdir("/tmp/testCron");
    mkdir("/tmp/testCron");
    mkdir("/tmp/testCron/cron.01min");
    mkdir("/tmp/testCron/cron.03min");
    mkdir("/tmp/testCron/cron.10min");
    mkdir("/tmp/testCron/cron.13min"); //not our folder
    mkdir("/tmp/testCron/cron.15min");
    mkdir("/tmp/testCron/cron.30min");
    mkdir("/tmp/testCron/cron.hourly");
    mkdir("/tmp/testCron/cron.hourly");
    touch("/tmp/testCron/cron.13min/Vitoconnect2-summary");
    touch("/tmp/testCron/cron.03min/Vitoconnect2-summary");
    touch("/tmp/testCron/cron.03min/Vitoconnect2-mqttPoll");
    touch("/tmp/testCron/cron.03min/Vitoconnect-summary");
}
class cronCreatorTest extends TestCase
{
    public function testWipeApll() {
        prepareTestCron();
        $minuteCron = new CronConfig(CronInterval::Minute_1, "dummy");
        $minuteCron->deleteOldCronEntry(true,"/tmp/testCron/");
        $this->assertTrue(file_exists("/tmp/testCron"));
        $this->assertTrue(file_exists("/tmp/testCron/cron.01min"));
        $this->assertTrue(file_exists("/tmp/testCron/cron.03min"));
        $this->assertTrue(file_exists("/tmp/testCron/cron.10min"));
        $this->assertTrue(file_exists("/tmp/testCron/cron.15min"));
        $this->assertTrue(file_exists("/tmp/testCron/cron.30min"));
        $this->assertTrue(file_exists("/tmp/testCron/cron.hourly"));
        $this->assertTrue(file_exists("/tmp/testCron/cron.hourly"));
        $this->assertFalse(file_exists("/tmp/testCron/cron.03min/Vitoconnect2-summary"));
        $this->assertFalse(file_exists("/tmp/testCron/cron.03min/Vitoconnect2-mqttPoll"));
        $this->assertTrue(file_exists("/tmp/testCron/cron.03min/Vitoconnect-summary"));
        $this->assertTrue(file_exists("/tmp/testCron/cron.13min/Vitoconnect2-summary")); //only in the typical files, things get wiped
    }

    public function testWipeSpecific() {
        prepareTestCron();
        $minuteCron = new CronConfig(CronInterval::Minute_3, "summary");
        $minuteCron->deleteOldCronEntry(false,"/tmp/testCron/");
        $this->assertTrue(file_exists("/tmp/testCron"));
        $this->assertTrue(file_exists("/tmp/testCron/cron.01min"));
        $this->assertTrue(file_exists("/tmp/testCron/cron.03min"));
        $this->assertTrue(file_exists("/tmp/testCron/cron.10min"));
        $this->assertTrue(file_exists("/tmp/testCron/cron.15min"));
        $this->assertTrue(file_exists("/tmp/testCron/cron.30min"));
        $this->assertTrue(file_exists("/tmp/testCron/cron.hourly"));
        $this->assertTrue(file_exists("/tmp/testCron/cron.hourly"));
        $this->assertFalse(file_exists("/tmp/testCron/cron.03min/Vitoconnect2-summary"));
        $this->assertTrue(file_exists("/tmp/testCron/cron.03min/Vitoconnect2-mqttPoll"));
    }

    public function testCronFileName() {
        $threeMinuteCron = new CronConfig(CronInterval::Minute_3, "summary");
        $this->assertEquals($threeMinuteCron->getCronFilename("/tmp/testCron/"),"/tmp/testCron/cron.03min/Vitoconnect2-summary");
        $fiveMinuteCron = new CronConfig(CronInterval::Minute_5, "summary");
        $this->assertEquals($fiveMinuteCron->getCronFilename("/tmp/testCron/"),"/tmp/testCron/cron.05min/Vitoconnect2-summary");
        $hourCron = new CronConfig(CronInterval::Minute_60, "summary");
        $this->assertEquals($hourCron->getCronFilename("/tmp/testCron/"),"/tmp/testCron/cron.hourly/Vitoconnect2-summary");

        $oneMinuteCron = new CronConfig(CronInterval::Minute_1, "rambazamba");
        $this->assertEquals($oneMinuteCron->getCronFilename("/tmp/testCron/"),"/tmp/testCron/cron.01min/Vitoconnect2-rambazamba");
    }

    public function testCronFileCreation() {
        prepareTestCron();
        $minuteCron = new CronConfig(CronInterval::Minute_1, "summary");
        $minuteCron->createCronConfig("/tmp/testCron/");
        $this->assertTrue(file_exists("/tmp/testCron/cron.01min/Vitoconnect2-summary"));
        $cronData = file_get_contents("/tmp/testCron/cron.01min/Vitoconnect2-summary");
        $this->assertEquals($cronData, "#!/bin/bash
cd /opt/loxberry/webfrontend/htmlauth/plugins/Vitoconnect2
php /opt/loxberry/webfrontend/htmlauth/plugins/Vitoconnect2/vitoconnect.php action=summary
");
    }
};


function setGlobals() {
    global $testOverrideLogLevel;
    $testOverrideLogLevel = 7;
}