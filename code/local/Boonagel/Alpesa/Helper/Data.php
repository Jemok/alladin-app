<?php

class Boonagel_Alpesa_Helper_Data extends Mage_Core_Helper_Abstract {
    /*     * payments allowed* */

    function allowPayments() {
        $configdata = $this->getConfigData();
        if (count($configdata) != 1) {
            return false;
        }
        
        if ($configdata->allow_payment == 1) {
            
            return true;
        } else {
            return false;
        }
    }

    /*     * get the sum of specific column field* */

    function sumColVals($modelType, $conditionArray, $sumField) {
        //$conditionArray = array('points'=>'neq,100')
        //$modelType = 'alpesa/alpesawallet'
        //$sumField='points'

        $summed = 0;

        //get the values.
        $dataSelected = Mage::getModel($modelType)->getCollection();
        if (count($conditionArray) > 0) {
            foreach ($conditionArray as $field => $value) {
                $operatorval = explode(",", $value);
                $dataSelected->addFieldToFilter($field, array($operatorval[0] => $operatorval[1]));
            }
        }

        //loop computing the values.
        if ($dataSelected->count() > 0) {
            foreach ($dataSelected as $singleData) {
                $summed += $singleData->$sumField;
            }
        }

        return $summed;
    }

    /*     * trigger an event to indicate points have been updated and to confirm target as defined by the administrator* */

    function triggerEventPointsUpdated($userId) {
        $eventData = array('customer_id' => $userId);
        Mage::dispatchEvent('altarget_point', $eventData);
    }

    /*     * trigger an event to indicate actual points have been updated* */

    function triggerUpdateActualPoints($userId, $pointId) {
        $eventData = array('customer_id' => $userId, 'point_id' => $pointId);
        Mage::dispatchEvent('actual_point_success', $eventData);
    }

    /*     * get the static configuration data* */

    function getConfigData() {

        $alpesaconfig = Mage::getModel('alpesa/alpesaconfig');
        $alpesaconfig->load(1, 'config_priority');
        return $alpesaconfig;
    }

    /*     * determine whether a certain rule is a yes or a no by returning true or false* */

    function processFlag($configEntireData, $flagType) {

        $canProcess = false;
        //$flagType=login,signup,newsletter,referral,

        if (!in_array($flagType, array('login', 'signup', 'newsletter', 'referral'))) {
            return $canProcess;
        }


        $flagData = trim($configEntireData[$flagType . '_points']);
        if (strlen($flagData) > 0) {
            $flagDataArray = explode(",", $flagData);
            $canProcess = count($flagDataArray) > 1 && $flagDataArray[0] == 'yes' ? true : false;
        }

        return $canProcess;
    }

    /*     * determine the amount of poits for a specific flag* */

    function processFlagPoints($configEntireData, $flagType) {

        $flagPoints = 0;
        if (!in_array($flagType, array('login', 'signup', 'newsletter', 'referral'))) {
            return $flagPoints;
        }
        //$flagType=login,signup,newsletter,referral,

        $flagData = trim($configEntireData[$flagType . '_points']);
        if (strlen($flagData) > 1) {
            $flagDataArray = explode(",", $flagData);
            $flagPoints = count($flagDataArray) > 1 ? (int) $flagDataArray[1] : 0;
        }
        return $flagPoints;
    }

    /*     * convert points-to-currency or currency-to-points* */

    function pointPriceConversion($configData, $points, $currency, $opType) {
        //opType = pnt-curr,curr-pnt

        if ($opType == 'pnt-curr') {
            //convert points to currency
            $currency = 0;
            if (strlen(trim($configData['point_currency'])) > 1) {
                $pointCurr = explode(",", trim($configData['point_currency']));
                $currency = $points * $pointCurr[1];
            }
            return $currency;
        }

        if ($opType == 'curr-pnt') {
            //convert currency to points
            $points = 0;
            if (strlen(trim($configData['currency_point'])) > 1) {
                $currPoint = explode(",", trim($configData['currency_point']));
                $points = floor($currency / $currPoint[0]);
            }
            return $points;
        }
    }

    /*     * get data in relation to customer id or not id at all * */

    function dynoData($modelType, $arrayConditions = null, $limit = null, $orderArray = null) {

        //$arrayConditions = array('user_id,neq,32');
        //$orderArray = array('id','DESC');
        //$limit = 1;

        $dynamicDatad = Mage::getModel($modelType)->getCollection();

        if (count($arrayConditions) > 0) {
            foreach ($arrayConditions as $arrayCondition) {
                $arrayedVals = explode(",", $arrayCondition);
                $dynamicDatad->addFieldToFilter($arrayedVals[0], array($arrayedVals[1] => $arrayedVals[2]));
            }
        }

        if ($orderArray != null) {
            $dynamicDatad->setOrder($orderArray[0], $orderArray[1]);
        }

        if ($limit != null) {
            $dynamicDatad->setPageSize((int) $limit);
        }

        return $dynamicDatad;
    }

    function formatNumber($value, $decimalPlaces = 0) {
        if (!is_int($decimalPlaces)) {
            $decimalPlaces = 0;
        }
        return number_format($value, $decimalPlaces);
    }

    /*     * calculates whether the gift date has arrived or not* */

    function giftFlag($cardId) {

        $today = new DateTime(now("Y-m-d H:i:s"));

        $passedArray = array();
        $passed = false;
        $provisional = false;
        $voucherLogging = true;
        $alpesacard = Mage::getModel('alpesa/alpesacard')->load($cardId);

        if (count($alpesacard) == 1) {
            //get the current time ranges from 0000hrs-2359hrs
            $dateArray = $this->calculateDateObjectStringNotation($alpesacard->card_gift_date);

            $midnightGiftDay = new DateTime(date("Y-m-d H:i:s", strtotime($dateArray[0])));
            $endTimeGiftDay = new DateTime(date("Y-m-d", strtotime($dateArray[0])) . ' 23:59:59');
            if ($today >= $midnightGiftDay && $today <= $endTimeGiftDay) {
                $provisional = true;
            } else {
                $provisional = false;
            }


            //check the logs
            $logStartDay = $midnightGiftDay->format('Y-m-d H:i:s');
            $logEndDay = $endTimeGiftDay->format('Y-m-d H:i:s');

            $alpesavoucherCollection = self::dynoData('alpesa/alpesavoucher', array('voucher_amount,eq,' . $alpesacard->card_voucher_amount,
                        'voucher_date,gteq,' . $logStartDay, 'voucher_date,lteq,' . $logEndDay));


            if ($alpesavoucherCollection->count() > 0) {
                $voucherTotal = 0;
                //loop through them and summing the amounts to determine if voucher total has already been utilized.
                foreach ($alpesavoucherCollection as $voucherCollection) {
                    $voucherTotal += $voucherCollection->voucher_used_amount;
                }

                if ($voucherTotal >= $alpesacard->card_voucher_amount) {
                    $voucherLogging = false;
                }
            }
            $passed = $provisional && $voucherLogging;
        }

        $passedArray[0] = $passed;
        return $passedArray;
    }

    /*     * calculates the date object and the string notation.* */

    function calculateDateObjectStringNotation($giftDateString) {

        $dateArray = array();

        $breakDate = explode(",", $giftDateString);
        $stringNotation = '';
        $giftDate = now();

        //yearly
        if (count($breakDate) == 4) {
            $stringNotation = 'Annualy on month ' . $breakDate[1] . ',week ' . $breakDate[2] . ',day ' . $breakDate[3];
            $giftDate = $this->detailedDateComputation('annualy', $breakDate);
        }
        //monthly
        if (count($breakDate) == 3) {
            $stringNotation = 'Monthly,week ' . $breakDate[1] . ',day ' . $breakDate[2];
            $giftDate = $this->detailedDateComputation('monthly', $breakDate);
        }
        //weekly
        if (count($breakDate) == 2) {
            $stringNotation = 'Weekly on day ' . $breakDate[1];
            $giftDate = $this->detailedDateComputation('weekly', $breakDate);
        }

        $dateArray[0] = $giftDate;
        $dateArray[1] = $stringNotation;
        return $dateArray;
    }

    /*     * detailed computation* */

    function detailedDateComputation($interval, $dateData) {
        //$interval = 'annualy,monthly,weekly'
        if ($interval == 'annualy') {
            //use 3 digits
            $firstDayMonthOfJanuary = date("Y-m-d", strtotime('first day of January ' . date('Y')));
            $dateTime = new DateTime($firstDayMonthOfJanuary);
            $dateTime->modify('+' . ($dateData[1] - 1) . ' month');
            $strDate = $dateTime->format('Y-m-d');

            $finalDate = $this->dateDayWeek($strDate, $dateData[2], $dateData[3]);
            return $finalDate;
        }
        if ($interval == 'monthly') {
            //use 2 digits
            $firstCurrentDayMonth = now("Y-m-d");
            $strDate = date("Y-m-d", strtotime('first day of' . $firstCurrentDayMonth));

            $finalDate = $this->dateDayWeek($strDate, $dateData[1], $dateData[2]);
            return $finalDate;
        }
        if ($interval == 'weekly') {
            //use 1 digit
            $firstCurrentDayMonth = now("Y-m-d");
            $strDate = date("Y-m-d", strtotime('first day of' . $firstCurrentDayMonth));
            $currentWeek = ceil((date("d") - date("w") - 1) / 7) + 1;

            $finalDate = $this->dateDayWeek($strDate, $currentWeek, $dateData[1]);
            return $finalDate;
        }
    }

    /*     * get the specific day* */

    function dateDayWeek($datestring, $weeknumber, $dateDay) {
        //$weeknumber=1,2,3, etc if 0 then all weeks
        //
        $date_timstamp = strtotime($datestring);
        $day_in_month = date('t', $date_timstamp);
        $arr_day_in_week = array();
        $j = 0;
        $weekCounter = 0;
        for ($i = 0; $i < $day_in_month; $i++) {
            $day = date('D', $date_timstamp);
            if ($day == 'Sat') {
                $j++;
                $weekCounter++;
                $arr_day_in_week[$weekCounter] = $j;
                $j = 0;
            } else {
                $j++;
            }
            $date_timstamp += 24 * 60 * 60;
        }

        if ($j > 0) {
            $arr_day_in_week[] = $j;
        }

        $dateNumber = 0;
        $dayCount = 0;
        $dayExists = false;
        foreach ($arr_day_in_week as $weekNum => $weekDays) {
            if ($weekNum == $weeknumber) {
                //finish execution and return date string
                if ($dateDay <= $weekDays) {
                    $dateNumber = $dayCount + $dateDay;
                    $dayExists = true;
                } else {
                    //return the first date of the month if the date does not exist
                    $dayExists = false;
                }
            }
            if ($weekNum != $weeknumber && $dayExists == false) {
                //increment the date
                $dayCount += $weekDays;
            }
        }

        $meDateString = $datestring;
        if ($dayExists == true) {
            $dateTime = new DateTime($datestring);
            $dateTime->modify('+' . ($dateNumber - 1) . ' day');
            $meDateString = $dateTime->format('Y-m-d');
        }

        return $meDateString;
    }

    public function canReedem() {
        //get the minimum points allowed and compare with user's actual points
        $flag = FALSE;
        $configData = self::getConfigData();
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $customerPoints = self::dynoData('alpesa/alpesawallet', array('user_id,eq,' . $customerId), 1);
        $customerData = $customerPoints->getFirstItem();

        if (count($configData) == 1 && count($customerPoints) == 1) {
            if ($customerData->actual_points > $configData->minimum_points) {
                $flag = true;
            }
        }

        return $flag;
    }

    public function alpesaPayments() {
        //fetch all unvalidated used amt logs and group in relation to order_id and combine with
        $usedUnvalidatedAmtData = self::dynoData('alpesa/alpesainvoice', array('validated,eq,0'), null, array('created_at', 'DESC'));
        $voucherUnvalidatedData = self::dynoData('alpesa/alpesavoucher', array('validated,eq,0'), null, array('created_at', 'DESC'));

        $alPayments['unvalidated'] = self::_determineIfAlpesaCanInvoice($usedUnvalidatedAmtData, $voucherUnvalidatedData);


        $usedvalidatedAmtData = self::dynoData('alpesa/alpesainvoice', array('validated,eq,1'), null, array('created_at', 'DESC'));
        $vouchervalidatedData = self::dynoData('alpesa/alpesavoucher', array('validated,eq,1'), null, array('created_at', 'DESC'));
        $alPayments['validated'] = self::_determineIfAlpesaCanInvoice($usedvalidatedAmtData, $vouchervalidatedData);

        //all the vouchers used amounts to determine whether the product has been fully paid for
        return $alPayments;
    }

    private function _determineIfAlpesaCanInvoice($usedAmtData, $voucherData) {


        $orderUsedkeysIdHolder = array();
        $orderVoucherkeysIdHolder = array();
        //$subsequentArray = array();
        $mergedArray = array();

        if ($usedAmtData->count() > 0) {
            foreach ($usedAmtData as $usedAmtDt) {
                if (!array_key_exists($usedAmtDt->order_id, $orderUsedkeysIdHolder)) {
                    $orderUsedkeysIdHolder[$usedAmtDt->order_id] = $usedAmtDt->order_id;
                    $orderUsedkeysIdHolder[$usedAmtDt->order_id] = self::_populateOrderArray($usedAmtDt->order_id, $usedAmtData, 'points');
                }
            }
        }

        if ($voucherData->count() > 0) {
            foreach ($voucherData as $voucherDatad) {
                if (!array_key_exists($voucherDatad->order_id, $orderVoucherkeysIdHolder)) {
                    $orderVoucherkeysIdHolder[$voucherDatad->order_id] = $voucherDatad->order_id;
                    $orderVoucherkeysIdHolder[$voucherDatad->order_id] = self::_populateOrderArray($voucherDatad->order_id, $voucherData, 'voucher');
                }
            }
        }

        if (count($orderUsedkeysIdHolder) > 0) {
            foreach ($orderUsedkeysIdHolder as $key => $value) {
                if (array_key_exists($key, $orderVoucherkeysIdHolder)) {
                    $mergedArray[$key] = array_merge($value, $orderVoucherkeysIdHolder[$key]);

                    unset($orderVoucherkeysIdHolder[$key]);
                }
            }
        }
        if (count($orderVoucherkeysIdHolder) > 0) {
            foreach ($orderVoucherkeysIdHolder as $key => $value) {
                $mergedArray[$key] = $value;
            }
        }

        //prepend total due,grandtotal,boolean to show can invoice or not.
        if (count($mergedArray) > 0) {
            foreach ($mergedArray as $key => $value) {
                $thisOrder = Mage::getModel("sales/order")->load($key);
                //ensure one order is available else remove the element from the array
                if (count($thisOrder) != 1) {
                    unset($mergedArray[$key]);
                } else {
                    $alpesaCanInvoice = false;

                    if ($thisOrder->total_due == 0) {
                        $alpesaCanInvoice = true;
                    }

                    array_unshift($mergedArray[$key], $thisOrder->grand_total, $thisOrder->total_due, $thisOrder->increment_id, $alpesaCanInvoice);
                }
            }
        }

        krsort($mergedArray);
        return $mergedArray;
    }

    private function _populateOrderArray($orderId, $arrayData, $amountType) {
        $completeArray = array();
        //$amountType = 'voucher,points'

        foreach ($arrayData as $arrayedData) {
            if ($arrayedData->order_id == $orderId) {
                $arrayedData->AmountType = $amountType;
                $completeArray[] = $arrayedData;
            }
        }

        //Zend_Debug::dump($completeArray,'what is happening');
        //die();

        return $completeArray;
    }

    //create on complete payment
    public function createInvoice($incrementId) {

//set invoice as paid if it exists or automatically create one to show it has been paid

        $orders = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('increment_id', array("eq" => $incrementId))->setPageSize(1);
        $invoiceCustomId = 0;
//        $order = Mage::getModel("sales/order")->load('increment_id',$incrementId);
        if ($orders->count() == 1) {
            $order = $orders->getFirstItem();



            try {

                if ($order->canInvoice()) {
                    //before creating the invoice set the total paid to 0 ie
                    $order->setTotalPaid(0);
                    $order->save();

                    $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
                    if ($invoice->getTotalQty()) {



                        $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
                        $invoice->register();
                        $invoice->getOrder()->setCustomerNoteNotify(false);
                        $invoice->getOrder()->setIsInProcess(true);
                        $order->addStatusHistoryComment('Automatically INVOICED by Alpesa.', false);

                        $transactionSave = Mage::getModel('core/resource_transaction')
                                ->addObject($invoice)
                                ->addObject($invoice->getOrder());

                        $transactionSave->save();
                        $invoiceCustomId = $invoice->getId();
                        //Self::_setOrderState($incrementId, Mage_Sales_Model_Order::STATE_PROCESSING);

                        Mage::getSingleton('core/session')->addSuccess('Invoice Created Successfully.');
                        return $invoiceCustomId;
                    } else {
                        Mage::getSingleton('core/session')->addError('Can not create invoice without Product Quantities');
                        return true; //use this true feature to prevent setting the log as paid for already
                    }
                } else {
                    Mage::getSingleton('core/session')->addError('Failed to create invoice.');
                    return false;
                }
            } catch (Mage_Core_Exception $e) {
//                echo $e->getMessage();
                return false;
            }
        }
    }

    /*     * change the order state and status from* */

    private function _setOrderState($increment_id, $state) {
//$state = Mage_Sales_Model_Order::STATE_PROCESSING
        $orders = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('increment_id', array("eq" => $increment_id))->setPageSize(1);
        $meorders = $orders->getFirstItem();
        if ($meorders->count() == 1) {
            $final = $meorders->setState($state)->setStatus($state)->save();
        }

//         echo $final->getStatus();
//        DIE();
    }

    private function _resetTotalPaid($increment_id) {

        //set the total_paid=0
        $orders = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('increment_id', array("eq" => $increment_id))->setPageSize(1);
        $meorders = $orders->getFirstItem();
        if (count($meorders) == 1) {
            $final = $meorders->setTotalPaid(0)->save();
        }
    }

    public function savePointsToPointsModel($customerId, $amount, $points, $status, $orderNumber = null) {
        $alpesapoints = Mage::getModel('alpesa/alpesapoints');
        $alpesapoints->getData();
        $alpesapoints->setUserId($customerId);
        $alpesapoints->setAmount($amount);
        $alpesapoints->setStatus($status);
        if ($orderNumber != null) {
            $alpesapoints->setOrderNumber($orderNumber);
        }
        $alpesapoints->setPoints($points);
        $alpesapoints->setUpdatedAt(now());
        $alpesapoints->setcreatedAt(now());
        $dbpointsdata = $alpesapoints->save();

        return $dbpointsdata;
    }

    /*     * set the title of the page* */

    public function setTitle($currentContext, $title) {
        $currentContext->getLayout()->getBlock('head')->setTitle($title);
    }

}
