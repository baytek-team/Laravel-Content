<?php
namespace Baytek\Laravel\Content\Commands;

use Baytek\Laravel\Content\Contracts\InstallerContract;
use Illuminate\Console\Command;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use Artisan;

abstract class Installer extends Command implements InstallerContract
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $package;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description;

    /**
     * Installer constructor
     */
    public function __construct()
    {
        $this->package = $this->name;

        if(empty($this->signature)) {
            $this->signature = 'install:'.strtolower($this->name);
        }

        if(empty($this->description)) {
            $this->description = "Install $this->name package.";
        }

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $installer = $this;

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
        $this->line("Checking if $installer->name data seeding is required: ");
        $this->info($installer->seed() ? 'Yes! Running Seeder.' : 'No! Skipping.');

        $this->line('');
        $this->line('Checking if permission seeding is required: ');
        $this->info($installer->protect() ? 'Yes! Generating Permissions.' : 'No! Skipping.');

        // Stop asking if we want to publish, this should be up to the developer.
        // if($installer->shouldPublish()) {
        //     if($this->confirm('Would your like to publish and/or overwrite publishable assets?')) {
        //         $this->info('Publishing Assets.');
        //         $installer->publish();
        //     }
        // }

        $this->line('');
        $this->info('Installation Complete.');

        if(method_exists($installer, 'afterInstallation')) {
            $installer->afterInstallation();
        }

    }

    /**
     * Check and run migration
     *
     * @return bool Result of migration
     */
    public function migrate()
    {
        if($this->shouldMigrate()) {
            Artisan::call('migrate');
            // Artisan::call('migrate', ['--path' => $this->migrationPath]);
            return true;
        }

        return false;
    }

    public function protect()
    {
        if($this->shouldProtect()) {
            $view = Permission::create(['name' => ucwords('view '   . $this->package)]);
            $create = Permission::create(['name' => ucwords('create ' . $this->package)]);
            $update = Permission::create(['name' => ucwords('update ' . $this->package)]);
            $delete = Permission::create(['name' => ucwords('delete ' . $this->package)]);

            Role::findByName('Root')->permissions()->saveMany([$view, $create, $update, $delete]);

            return true;
        }

        return false;
    }

    public function publish()
    {
        if($this->shouldPublish()) {
            // Artisan::call('vendor:publish', ['--tag' => 'views', '--provider' => $this->provider]);
            Artisan::call('vendor:publish', ['--tag' => 'config', '--provider' => $this->provider]);
            return true;
        }

        return false;
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