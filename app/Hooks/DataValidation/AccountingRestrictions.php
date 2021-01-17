<?php

namespace Alpha\App\Hooks\DataValidation;

use Alpha\Framework\Support\Arr;
use Alpha\App\Modules\Reporting\ReportingHelper;

class AccountingRestrictions
{
    public function register()
    {
        add_action('ninja_erp/before_add_payment', array($this, 'validateNewPayment'), 10, 1);
    }

    public function validateNewPayment($payment)
    {
        $accountId = Arr::get($payment, 'account_id');
        $paymentItems = Arr::get($payment, 'input_items');
        $paymentTotal = 0;
        foreach ($paymentItems as $paymentItem) {
            $paymentTotal += $paymentItem['quantity'] * $paymentItem['unit_price'];
        }

        $reportingHelper = new ReportingHelper();

        $accountBalance = $reportingHelper->getAccountBalance($accountId, 'assets', 'balance');

        if($accountBalance < $paymentTotal) {
            $this->handleError(__('Sorry, You do not have enough fund on your selected account. Current available amount: ', 'ninja-erp'). $accountBalance);
        }
    }

    private function handleError($message)
    {
        wp_send_json_error(array(
            'message' => $message
        ), 400);
    }
}
