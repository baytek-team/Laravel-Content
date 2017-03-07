<?php

namespace Baytek\Laravel\Content;

use Baytek\Laravel\Content\Contracts\InstallerContract;

use Artisan;

abstract class Installer implements InstallerContract
{
    public function installCommand()
    {
        $installer = $this;
        Artisan::command('install:'.strtolower($this->name), function () use ($installer) {
            if(method_exists($installer, 'beforeInstallation')) {
                $installer->beforeInstallation();
            }

            $this->info("Installing $installer->name package.");
            $this->comment('Doing checks to see if migrations, seeding and publishing need to happen.');

            if(app()->environment() === 'production') {
                $this->error('You are in a production environment, aborting.');
                exit();
            }

            $this->line('');
            $this->line('Checking if migrations are required: ');
            $this->info($installer->migrate() ? 'Yes! Running Migrations.' : 'No! Skipping.');


            $this->line('');
            $this->line('Checking if base data seeding is required: ');
            $this->info($installer->seed() ? 'Yes! Running Seeder.' : 'No! Skipping.');

            if($installer->shouldPublish()) {
                if($this->confirm('Would your like to publish and/or overwrite publishable assets?')) {
                    $this->info('Publishing Assets.');
                    $installer->publish();
                }
            }

            $this->line('');
            $this->info('Installation Complete.');

            if(method_exists($installer, 'afterInstallation')) {
                $installer->afterInstallation();
            }

        })->describe("Install $this->name package.");
    }

    public function migrate()
    {
        if($this->shouldMigrate()) {
            Artisan::call('migrate');
            // Artisan::call('migrate', ['--path' => $this->migrationPath]);
            return true;
        }

        return false;
    }

    public function publish()
    {
        Artisan::call('vendor:publish', ['--tag' => 'views', '--provider' => $this->provider]);
        return true;
    }

    public function seed()
    {
        if($this->shouldSeed()) {
            (new $this->seeder)->run();

            return true;
        }

        return false;
    }

}
