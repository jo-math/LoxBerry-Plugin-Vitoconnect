<?php

require_once "loxberry_system.php";
require_once "loxberry_log.php";
require_once "defines.php";


class CronInterval extends Enum{
    private function __construct() {
    }
    const Disabled = "0";
    const Minute_1 = "01min";
    const Minute_3 = "03min";
    const Minute_5 = "05min";
    const Minute_10 = "10min";

    const Minute_15 = "15min";

    const Minute_30 = "30min";

    const Minute_60 = "hourly";

    public static function isCronFolderName($baseFolderName) {
        $prefix = "cron.";
        if (substr($baseFolderName, 0, strlen($prefix)) == $prefix) {
            $shortedString = substr($baseFolderName, strlen($prefix));
            return CronInterval::isValidValue($shortedString, false);
        }
        return false;
    }


}

class CronConfig
{

    public $interval;

    public $action;

    public $isActive;

    public function __toString()
    {
       return ("{CronConfig: {interval : $this->interval\"\", action : \"$this->action\", isActive : $this->isActive}} }");
    }

    public function __construct($intervalParam, $actionParam, $isActive)
    {
        $this->interval = $intervalParam;
        $this->action = $actionParam;
        $this->isActive = $isActive;
    }

    public function getCronFilename($baseCronDir = null)
    {
        $usedBasedCronDir = $baseCronDir ? "$baseCronDir" : LBHOMEDIR . "/system/cron/";
        return "${usedBasedCronDir}cron.{$this->interval}/". LBPPLUGINDIR ."-{$this->action}";
    }

    private static function getActionName($cronFileName)
    {
        $prefix = LBPPLUGINDIR . "-";
        if (substr($cronFileName, 0, strlen($prefix)) == $prefix) {
            return substr($cronFileName, strlen($prefix));
        }
        return false;
    }

    public function deleteOldCronEntry($wipeAll = false, $baseCronDir = null)
    {
        $usedBasedCronDir = $baseCronDir ? "$baseCronDir" : LBHOMEDIR . "/system/cron/";
        LOGINF("Deleting old cron entries $usedBasedCronDir . Wiping all:  $wipeAll");
        $cronSubFolders = array_diff(scandir($usedBasedCronDir), array('..', '.'));
        if ($cronSubFolders) {
            foreach ($cronSubFolders as $cronSubFolder) {
                if (CronInterval::isCronFolderName(basename($cronSubFolder))) {
                    $scanDirResult = scandir("$usedBasedCronDir" . $cronSubFolder . "/");
                    if ($scanDirResult) {
                        $cronFiles = array_diff($scanDirResult, array('..', '.'));
                        foreach ($cronFiles as $cronFile) {
                            $cronFileBasedActionName = CronConfig::getActionName($cronFile);
                            if ($cronFileBasedActionName && ($wipeAll || $cronFileBasedActionName == $this->action)) {
                                $fileNameWipe = "${usedBasedCronDir}${cronSubFolder}/${cronFile}";
                                $result = unlink($fileNameWipe);
                                LOGINF("Deleting the file $fileNameWipe . Success $result");
                            }
                        }
                    }
                }
            }
        }
    }
    public function createCronConfig( $baseCronDir = null)
    {
        $cronpath = $this->getCronFilename($baseCronDir);
        $cronentrystr =
            "#!/bin/bash" . PHP_EOL .
            "cd " . LBPHTMLAUTHDIR . PHP_EOL .
            "php " . LBPHTMLAUTHDIR . "/vitoconnect.php action=". $this->action . PHP_EOL;
        if (file_put_contents($cronpath, $cronentrystr) && chmod("$cronpath", 0755)) {
            LOGINF("Written the file $cronpath successfully (chmod 755).");
        } else {
            LOGERR("Unable to safe cron file under the path $cronpath");
        }
    }

    public function updateCronConfig($wipeAll = false, $baseCronDir = null)
    {
        $this->deleteOldCronEntry($wipeAll, $baseCronDir);
        if ($this->isActive) {
            $this->createCronConfig($baseCronDir);
        }
    }
}
