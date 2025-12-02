<?php

namespace App\Console\Commands;

use Database\Seeders\BranchSeeder;
use Database\Seeders\BrandSeeder;
use Database\Seeders\CompanySeeder;
use Database\Seeders\CustomerGroupSeeder;
use Database\Seeders\ProductCategorySeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UnitSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\WarehouseSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class AppSeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Data Seeding';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (App::environment('prod', 'production')) {
            $this->info('**************************************');
            $this->info('*     Application In Production!     *');
            $this->info('**************************************');

            $runInProd = $this->confirm('Do you really wish to run this command?', false);

            if (! $runInProd) {
                return Command::SUCCESS;
            }
        }

        $this->runDefault();

        $this->info('Done!');

        return Command::SUCCESS;
    }

    private function runDefault()
    {
        $total = 10;
        $this->info('Starting data seeding...');
        $this->info('');
        $progressBar = $this->output->createProgressBar($total);
        $progressBar->start();

        // (new UserSeeder())->run(truncate: false, count: 5);
        // $progressBar->advance();

        // (new RoleSeeder())->run(randomPermission: true, count: 5);
        // $progressBar->advance();

        (new CompanySeeder())->run(companiesPerUsers: 1, userId: 0);
        $progressBar->advance();

        (new BranchSeeder())->run(branchPerCompanies: 5, onlyThisCompanyId: 0);
        $progressBar->advance();

        (new WarehouseSeeder())->run(warehousePerCompanies: 5, onlyThisCompanyId: 0);
        $progressBar->advance();

        (new ProductCategorySeeder())->run(companyId: null, qtyPerCompany: 5);
        $progressBar->advance();

        (new BrandSeeder())->run(companyId: null, qtyPerCompany: 5);
        $progressBar->advance();

        (new UnitSeeder())->run(companyId: null, qtyPerCompany: 5);
        $progressBar->advance();

        // (new ProductSeeder())->run(companyId: null, qtyPerCompany: 5);
        // $progressBar->advance();

        // (new CustomerGroupSeeder())->run(companyId: null, qtyPerCompany: 5);
        // $progressBar->advance();

        $progressBar->finish();
        $this->info('');
        $this->info('');
    }
}
