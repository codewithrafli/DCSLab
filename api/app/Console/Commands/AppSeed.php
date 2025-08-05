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
                    $this->runUserSeederInteractive();
                    break;
                case 'role':
                case 'roletableseeder':
                    $this->runRoleSeederInteractive();
                    break;
                case 'company':
                case 'companytableseeder':
                    $this->runCompanySeederInteractive();
                    break;
                case 'branch':
                case 'branchtableseeder':
                    $this->runBranchSeederInteractive();
                    break;
                case 'warehouse':
                case 'warehousetableseeder':
                    $this->runWarehouseSeederInteractive();
                    break;
                case 'productcategory':
                case 'productcategorytableseeder':
                    $this->runProductCategorySeederInteractive();
                    break;
                case 'brand':
                case 'brandtableseeder':
                    $this->runBrandSeederInteractive();
                    break;
                case 'unit':
                case 'unittableseeder':
                    $this->runUnitSeederInteractive();
                    break;
                case 'product':
                case 'producttableseeder':
                    $this->runProductSeederInteractive();
                    break;
                case 'supplier':
                case 'suppliertableseeder':
                    $this->runSupplierSeederInteractive();
                    break;
                case 'customergroup':
                case 'customergrouptableseeder':
                    $this->runCustomerSeederInteractive();
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

        $this->runUserSeeder(truncate: false, count: 5);
        $progressBar->advance();

        $this->runRoleSeeder(randomPermission: true, count: 5);
        $progressBar->advance();

        $this->runCompanySeeder(companiesPerUsers: 5, userId: 0);
        $progressBar->advance();

        $this->runBranchSeeder(branchPerCompanies: 5, onlyThisCompanyId: 0);
        $progressBar->advance();

        $this->runWarehouseSeeder(warehousePerCompanies: 5, onlyThisCompanyId: 0);
        $progressBar->advance();

        $this->runProductCategorySeeder(productCategoryPerCompanies: 5, onlyThisCompanyId: 0);
        $progressBar->advance();

        $this->runBrandSeeder(brandPerCompanies: 5, onlyThisCompanyId: 0);
        $progressBar->advance();

        $this->runUnitSeeder(unitPerCompanies: 5, onlyThisCompanyId: 0);
        $progressBar->advance();

        $this->runProductSeeder(productPerCompanies: 5, onlyThisCompanyId: 0);
        $progressBar->advance();

        $this->runCustomerGroupSeeder(customerGroupPerCompanies: 5, onlyThisCompanyId: 0);
        $progressBar->advance();

        // $this->runSupplierSeeder(5, 0);
        // $progressBar->advance();

        $progressBar->finish();
        $this->info('');
        $this->info('');
    }

    private function runUserSeederInteractive()
    {
        $this->info('Starting UserSeeder');
        $truncate = $this->confirm('Do you want to truncate the users table first?', false);
        $count = $this->ask('How many data:', 5);

        $this->info('Seeding...');

        $this->runUserSeeder($truncate, $count);

        $this->info('UserSeeder Finish.');
    }

    private function runUserSeeder($truncate, $count)
    {
        $seeder = new UserSeeder();
        $seeder->callWith(UserSeeder::class, [$truncate, $count]);
    }

    private function runRoleSeederInteractive()
    {
        $this->info('Starting RoleSeeder');
        $randomPermission = true;
        $count = $this->ask('How many data:', 5);

        $this->info('Seeding...');

        $this->runRoleSeeder($randomPermission, $count);

        $this->info('RoleSeeder Finish.');
    }

    private function runRoleSeeder($randomPermission, $count)
    {
        $seeder = new RoleSeeder();
        $seeder->callWith(RoleSeeder::class, [$randomPermission, $count]);
    }

    private function runCompanySeederInteractive()
    {
        $this->info('Starting CompanySeeder');
        $companiesPerUsers = $this->ask('How many companies for each users:', 3);
        $userId = $this->ask('Only to this userId (0 to all):', 0);

        $this->info('Seeding...');

        $this->runCompanySeeder($companiesPerUsers, $userId);

        $this->info('CompanySeeder Finish.');
    }

    private function runCompanySeeder($companiesPerUsers, $userId)
    {
        $seeder = new CompanySeeder();
        $seeder->callWith(CompanySeeder::class, [$companiesPerUsers, $userId]);
    }

    private function runBranchSeederInteractive()
    {
        $this->info('Starting BranchSeeder');
        $branchPerCompanies = $this->ask('How many branches per company (0 to skip) :', 3);
        $onlyThisCompanyId = $this->ask('Only for this companyId (0 to all):', 0);

        $this->info('Seeding...');

        $this->runBranchSeeder($branchPerCompanies, $onlyThisCompanyId);

        $this->info('BranchSeeder Finish.');
    }

    private function runBranchSeeder($branchPerCompanies, $onlyThisCompanyId)
    {
        $seeder = new BranchSeeder();
        $seeder->callWith(BranchSeeder::class, [$branchPerCompanies, $onlyThisCompanyId]);
    }

    private function runWarehouseSeederInteractive()
    {
        $this->info('Starting WarehouseSeeder');
        $warehousePerCompanies = $this->ask('How many warehouse per company (0 to skip) :', 3);
        $onlyThisCompanyId = $this->ask('Only for this companyId (0 to all):', 0);

        $this->info('Seeding...');

        $this->runWarehouseSeeder($warehousePerCompanies, $onlyThisCompanyId);

        $this->info('WarehouseSeeder Finish.');
    }

    private function runWarehouseSeeder($warehousePerCompanies, $onlyThisCompanyId)
    {
        $seeder = new WarehouseSeeder();
        $seeder->callWith(WarehouseSeeder::class, [$warehousePerCompanies, $onlyThisCompanyId]);
    }

    private function runProductCategorySeeder($productCategoryPerCompanies, $onlyThisCompanyId)
    {
        $seeder = new ProductCategorySeeder();
        $seeder->callWith(ProductCategorySeeder::class, [$productCategoryPerCompanies, $onlyThisCompanyId]);
    }

    private function runBrandSeeder($brandPerCompanies, $onlyThisCompanyId)
    {
        $seeder = new BrandSeeder();
        $seeder->callWith(BrandSeeder::class, [$brandPerCompanies, $onlyThisCompanyId]);
    }

    private function runUnitSeeder($unitPerCompanies, $onlyThisCompanyId)
    {
        $seeder = new UnitSeeder();
        $seeder->callWith(UnitSeeder::class, [$unitPerCompanies, $onlyThisCompanyId]);
    }

    private function runProductSeeder($productPerCompanies, $onlyThisCompanyId)
    {
        $seeder = new ProductSeeder();
        $seeder->callWith(ProductSeeder::class, [$productPerCompanies, $onlyThisCompanyId]);
    }

    private function runSupplierSeeder($supplierPerCompanies, $onlyThisCompanyId)
    {
        $seeder = new SupplierSeeder();
        $seeder->callWith(SupplierSeeder::class, [$supplierPerCompanies, $onlyThisCompanyId]);
    }

    private function runCustomerGroupSeeder($customerGroupPerCompanies, $onlyThisCompanyId)
    {
        $seeder = new CustomerGroupSeeder();
        $seeder->callWith(CustomerGroupSeeder::class, [$customerGroupPerCompanies, $onlyThisCompanyId]);
    }
}
