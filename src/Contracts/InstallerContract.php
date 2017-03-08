<?php
namespace Baytek\Laravel\Content\Contracts;

interface InstallerContract
{
    public function installCommand();
    public function shouldPublish();
    public function shouldMigrate();
    public function shouldSeed();
    public function shouldProtect();
}
