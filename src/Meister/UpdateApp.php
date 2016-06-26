<?php

namespace Meister\Meister;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;

class UpdateApp{

    public static function afterInstall(PackageEvent $packageEvent){
//        file_put_contents('teste',$vendorDir = $event->getComposer()->getConfig()->get('vendor-dir'));
        file_put_contents('teste',$packageEvent->getOperation()->getPackage());
    }

}