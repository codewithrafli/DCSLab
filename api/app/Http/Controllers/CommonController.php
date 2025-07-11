<?php

namespace App\Http\Controllers;

use App\Enums\PaymentTermTypeEnum;
use App\Enums\RecordStatusEnum;
use App\Enums\RoundOnEnum;

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

    public function getPaymentTermTypes()
    {
        return [
            ['name' => 'components.dropdown.values.paymentTermTypeDDL.pia', 'code' => PaymentTermTypeEnum::PAYMENT_IN_ADVANCE->name],
            ['name' => 'components.dropdown.values.paymentTermTypeDDL.net', 'code' => PaymentTermTypeEnum::X_DAYS_AFTER_INVOICE->name],
            ['name' => 'components.dropdown.values.paymentTermTypeDDL.eom', 'code' => PaymentTermTypeEnum::END_OF_MONTH->name],
            ['name' => 'components.dropdown.values.paymentTermTypeDDL.cod', 'code' => PaymentTermTypeEnum::CASH_ON_DELIVERY->name],
            ['name' => 'components.dropdown.values.paymentTermTypeDDL.cnd', 'code' => PaymentTermTypeEnum::CASH_ON_NEXT_DELIVERY->name],
            ['name' => 'components.dropdown.values.paymentTermTypeDDL.cbs', 'code' => PaymentTermTypeEnum::CASH_BEFORE_SHIPMENT->name],
        ];
    }

    public function getRoundingTypes()
    {
        return [
            ['name' => 'components.dropdown.values.roundOnDDL.up', 'code' => RoundOnEnum::UP->name],
            ['name' => 'components.dropdown.values.roundOnDDL.down', 'code' => RoundOnEnum::DOWN->name],
        ];
    }

    public function getRecordStatuses()
    {
        return [
            ['name' => 'components.dropdown.values.statusDDL.active', 'code' => RecordStatusEnum::ACTIVE->name],
            ['name' => 'components.dropdown.values.statusDDL.inactive', 'code' => RecordStatusEnum::INACTIVE->name],
            ['name' => 'components.dropdown.values.statusDDL.deleted', 'code' => RecordStatusEnum::DELETED->name],
        ];
    }
}
