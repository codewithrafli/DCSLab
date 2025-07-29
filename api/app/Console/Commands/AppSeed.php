<?php

namespace App\Console\Commands;

use Database\Seeders\BranchSeeder;
use Database\Seeders\BrandSeeder;
use Database\Seeders\CompanySeeder;
use Database\Seeders\CustomerGroupSeeder;
use Database\Seeders\ProductCategorySeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SupplierSeeder;
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
    protected $signature = 'app:seed {args?}';

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
                return;
            }
        }

        $argsArr = [];

        if (! empty($this->argument('args'))) {
            if (str_contains($this->argument('args'), ',')) {
                $argsArr = explode(',', $this->argument('args'));
            } else {
                $argsArr = [
                    $this->argument('args'),
                ];
            }
            $this->runWithArgs($argsArr);
        } else {
            $this->runDefault();
        }

        $this->info('Done!');

        return Command::SUCCESS;
    }

    private function runWithArgs(array $argsArr)
    {
        foreach ($argsArr as $args) {
            switch (strtolower($args)) {
                case 'user':
                case 'usertableseeder':
                    $this->runUserTableSeederInteractive();
                    break;
                case 'role':
                case 'roletableseeder':
                    $this->runRoleTableSeederInteractive();
                    break;
                case 'company':
                case 'companytableseeder':
                    $this->runCompanyTableSeederInteractive();
                    break;
                case 'branch':
                case 'branchtableseeder':
                    $this->runBranchTableSeederInteractive();
                    break;
                case 'warehouse':
                case 'warehousetableseeder':
                    $this->runWarehouseTableSeederInteractive();
                    break;
                case 'productcategory':
                case 'productcategorytableseeder':
                    $this->runProductCategoryTableSeederInteractive();
                    break;
                case 'brand':
                case 'brandtableseeder':
                    $this->runBrandTableSeederInteractive();
                    break;
                case 'unit':
                case 'unittableseeder':
                    $this->runUnitTableSeederInteractive();
                    break;
                case 'product':
                case 'producttableseeder':
                    $this->runProductTableSeederInteractive();
                    break;
                case 'supplier':
                case 'suppliertableseeder':
                    $this->runSupplierTableSeederInteractive();
                    break;
                case 'customergroup':
                case 'customergrouptableseeder':
                    $this->runCustomerTableSeederInteractive();
                    break;
                default:
                    $this->info('Cannot find seeder for '.$args);
                    break;
            }
        }
    }

    private function runDefault()
    {
        $total = 4;
        $this->info('');
        $progressBar = $this->output->createProgressBar($total);
        $progressBar->start();

        $this->runUserTableSeeder(false, 5);
        $progressBar->advance();
        $this->runRoleTableSeeder(true, 5);
        $progressBar->advance();
        $this->runCompanyTableSeeder(5, 0);
        $progressBar->advance();
        $this->runBranchTableSeeder(5, 0);
        $progressBar->advance();
        $this->runWarehouseTableSeeder(5, 0);
        $progressBar->advance();
        $this->runProductCategoryTableSeeder(5, 0);
        $progressBar->advance();
        $this->runBrandTableSeeder(5, 0);
        $progressBar->advance();
        $this->runUnitTableSeeder(5, 0);
        $progressBar->advance();
        $this->runProductTableSeeder(5, 0);
        $progressBar->advance();
        $this->runCustomerGroupTableSeeder(5, 0);
        $progressBar->advance();
        // $this->runSupplierTableSeeder(5, 0);
        // $progressBar->advance();

        $progressBar->finish();
        $this->info('');
        $this->info('');
    }

    private function runUserTableSeederInteractive()
    {
        $this->info('Starting UserTableSeeder');
        $truncate = $this->confirm('Do you want to truncate the users table first?', false);
        $count = $this->ask('How many data:', 5);

        $this->info('Seeding...');

        $this->runUserTableSeeder($truncate, $count);

        $this->info('UserTableSeeder Finish.');
    }

    private function runUserTableSeeder($truncate, $count)
    {
        $seeder = new UserSeeder();
        $seeder->callWith(UserSeeder::class, [$truncate, $count]);
    }

    private function runRoleTableSeederInteractive()
    {
        $this->info('Starting RoleTableSeeder');
        $randomPermission = true;
        $count = $this->ask('How many data:', 5);

        $this->info('Seeding...');

        $this->runRoleTableSeeder($randomPermission, $count);

        $this->info('RoleTableSeeder Finish.');
    }

    private function runRoleTableSeeder($randomPermission, $count)
    {
        $seeder = new RoleSeeder();
        $seeder->callWith(RoleSeeder::class, [$randomPermission, $count]);
    }

    private function runCompanyTableSeederInteractive()
    {
        $this->info('Starting CompanyTableSeeder');
        $companiesPerUsers = $this->ask('How many companies for each users:', 3);
        $userId = $this->ask('Only to this userId (0 to all):', 0);

        $this->info('Seeding...');

        $this->runCompanyTableSeeder($companiesPerUsers, $userId);

        $this->info('CompanyTableSeeder Finish.');
    }

    private function runCompanyTableSeeder($companiesPerUsers, $userId)
    {
        $seeder = new CompanySeeder();
        $seeder->callWith(CompanySeeder::class, [$companiesPerUsers, $userId]);
    }

    private function runBranchTableSeederInteractive()
    {
        $this->info('Starting BranchTableSeeder');
        $branchPerCompanies = $this->ask('How many branches per company (0 to skip) :', 3);
        $onlyThisCompanyId = $this->ask('Only for this companyId (0 to all):', 0);

        $this->info('Seeding...');

        $this->runBranchTableSeeder($branchPerCompanies, $onlyThisCompanyId);

        $this->info('BranchTableSeeder Finish.');
    }

    private function runBranchTableSeeder($branchPerCompanies, $onlyThisCompanyId)
    {
        $seeder = new BranchSeeder();
        $seeder->callWith(BranchSeeder::class, [$branchPerCompanies, $onlyThisCompanyId]);
    }

    private function runWarehouseTableSeederInteractive()
    {
        $this->info('Starting WarehouseTableSeeder');
        $warehousePerCompanies = $this->ask('How many warehouse per company (0 to skip) :', 3);
        $onlyThisCompanyId = $this->ask('Only for this companyId (0 to all):', 0);

        $this->info('Seeding...');

        $this->runWarehouseTableSeeder($warehousePerCompanies, $onlyThisCompanyId);

        $this->info('WarehouseTableSeeder Finish.');
    }

    private function runWarehouseTableSeeder($warehousePerCompanies, $onlyThisCompanyId)
    {
        $seeder = new WarehouseSeeder();
        $seeder->callWith(WarehouseSeeder::class, [$warehousePerCompanies, $onlyThisCompanyId]);
    }

    private function runProductCategoryTableSeeder($productCategoryPerCompanies, $onlyThisCompanyId)
    {
        $seeder = new ProductCategorySeeder();
        $seeder->callWith(ProductCategorySeeder::class, [$productCategoryPerCompanies, $onlyThisCompanyId]);
    }

    private function runBrandTableSeeder($brandPerCompanies, $onlyThisCompanyId)
    {
        $seeder = new BrandSeeder();
        $seeder->callWith(BrandSeeder::class, [$brandPerCompanies, $onlyThisCompanyId]);
    }

    private function runUnitTableSeeder($unitPerCompanies, $onlyThisCompanyId)
    {
        $seeder = new UnitSeeder();
        $seeder->callWith(UnitSeeder::class, [$unitPerCompanies, $onlyThisCompanyId]);
    }

    private function runProductTableSeeder($productPerCompanies, $onlyThisCompanyId)
    {
        $seeder = new ProductSeeder();
        $seeder->callWith(ProductSeeder::class, [$productPerCompanies, $onlyThisCompanyId]);
    }

    private function runSupplierTableSeeder($supplierPerCompanies, $onlyThisCompanyId)
    {
        $seeder = new SupplierSeeder();
        $seeder->callWith(SupplierSeeder::class, [$supplierPerCompanies, $onlyThisCompanyId]);
    }

    private function runCustomerGroupTableSeeder($customerGroupPerCompanies, $onlyThisCompanyId)
    {
        $seeder = new CustomerGroupSeeder();
        $seeder->callWith(CustomerGroupSeeder::class, [$customerGroupPerCompanies, $onlyThisCompanyId]);
    }
}
