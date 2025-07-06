<?php

namespace App\Http\Controllers;

use App\Enums\RecordStatusEnum;

class CommonController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getCountries()
    {
        return [
            ['name' => 'Indonesia', 'code' => 'ID'],
            ['name' => 'Singapore', 'code' => 'SG'],
        ];
    }

    public function getStatus()
    {
        return [
            ['name' => 'components.dropdown.values.statusDDL.active', 'code' => RecordStatusEnum::ACTIVE->name],
            ['name' => 'components.dropdown.values.statusDDL.inactive', 'code' => RecordStatusEnum::INACTIVE->name],
        ];
    }
}
