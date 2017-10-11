<?php
namespace Baytek\Laravel\Content\Contracts;

interface InstallerContract
{
    public function handle();
    public function publish();
    public function migrate();
    public function seed();
    public function seedFake();
    public function protect();
    public function shouldPublish();
    public function shouldMigrate();
    public function shouldSeed();
    public function shouldProtect();
}
