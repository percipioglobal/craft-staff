<?php

namespace percipiolondon\craftstaff\jobs;

use Craft;
use craft\queue\BaseJob;
use percipiolondon\craftstaff\records\Employee;
use percipiolondon\craftstaff\records\PayRunEntry as PayRunEntryRecord;
use percipiolondon\craftstaff\elements\PayRunEntry;
use craft\helpers\Queue;

class CreatePayRunEntryJob extends Basejob
{
    public $headers;
    public $payRunEntries;
    public $payRunId;
    public $employerId;

    public function execute($queue): void
    {
        try {
            $client = new \GuzzleHttp\Client();

            foreach($this->payRunEntries as $payRunEntryData) {

                $payRunEntry = PayRunEntryRecord::findOne(['staffologyId' => $payRunEntryData['id']]);

                // SET PAY RUN ENTRY IF IT DOESN'T EXIST
                if (!$payRunEntry) {

                    $base_url = "https://api.staffology.co.uk/" . $payRunEntryData['url'];
                    $response = $client->get($base_url, $this->headers);
                    $payRunEntryData = json_decode($response->getBody()->getContents(), true);

                    $this->_savePayRunEntry($payRunEntryData);
                }
            }
        } catch (\Exception $e) {
            Craft::error("Something went wrong: {$e->getMessage()}", __METHOD__);
        } catch (\Throwable $e) {
            Craft::error("Something went wrong: {$e->getMessage()}", __METHOD__);
        }
    }

    private function _savePayRunEntry($payRunEntryData)
    {
        $payRunEntry = new PayRunEntry();

        $employee = Employee::findOne(['staffologyId' => $payRunEntryData['employee']['id']]);

        $payRunEntry->siteId = Craft::$app->getSites()->currentSite->id;
        $payRunEntry->employerId = $this->employerId;
        $payRunEntry->employeeId = $employee->id ?? null;
        $payRunEntry->payRunId = $this->payRunId;
        $payRunEntry->staffologyId = $payRunEntryData['id'] ?? null;
        $payRunEntry->taxYear = $payRunEntryData['taxYear'] ?? null;
        $payRunEntry->startDate = $payRunEntryData['startDate'] ?? null;
        $payRunEntry->endDate = $payRunEntryData['endDate'] ?? null;
        $payRunEntry->note = $payRunEntryData['note'] ?? '';
        $payRunEntry->bacsSubReference = $payRunEntryData['bacsSubReference'] ?? '';
        $payRunEntry->bacsHashcode = $payRunEntryData['bacsHashcode'] ?? '';
        $payRunEntry->percentageOfWorkingDaysPaidAsNormal = $payRunEntryData['percentageOfWorkingDaysPaidAsNormal'] ?? null;
        $payRunEntry->workingDaysNotPaidAsNormal = $payRunEntryData['workingDaysNotPaidAsNormal'] ?? null;
        $payRunEntry->payPeriod = $payRunEntryData['payPeriod'] ?? null;
        $payRunEntry->ordinal = $payRunEntryData['ordinal'] ?? null;
        $payRunEntry->period = $payRunEntryData['period'] ?? null;
        $payRunEntry->isNewStarter = $payRunEntryData['isNewStarter'] ?? null;
        $payRunEntry->unpaidAbsence = $payRunEntryData['unpaidAbsence'] ?? null;
        $payRunEntry->hasAttachmentOrders = $payRunEntryData['hasAttachmentOrders'];
        $payRunEntry->paymentDate = $payRunEntryData['paymentDate'] ?? null;
        $payRunEntry->priorPayrollCode = $payRunEntryData['priorPayrollCode'] ?? '';
        $payRunEntry->payOptions = $payRunEntryData['payOptions'] ?? '';
        $payRunEntry->pensionSummary = $payRunEntryData['pensionSummary'] ?? '';
        $payRunEntry->totals = $payRunEntryData['totals'] ?? '';
        $payRunEntry->periodOverrides = $payRunEntryData['periodOverrides'] ?? '';
        $payRunEntry->totalsYtd = $payRunEntryData['totalsYtd'] ?? '';
        $payRunEntry->totalsYtdOverrides = $payRunEntryData['totalsYtdOverrides'] ?? '';
        $payRunEntry->forcedCisVatAmount = $payRunEntryData['forcedCisVatAmount'] ?? null;
        $payRunEntry->holidayAccured = $payRunEntryData['holidayAccured'] ?? null;
        $payRunEntry->state = $payRunEntryData['state'] ?? '';
        $payRunEntry->isClosed = $payRunEntryData['isClosed'] ?? null;
        $payRunEntry->manualNi = $payRunEntryData['manualNi'] ?? null;
        $payRunEntry->nationalInsuranceCalculation = $payRunEntryData['nationalInsuranceCalculation'] ?? '';
        $payRunEntry->aeNotEnroledWarning = $payRunEntryData['aeNotEnroledWarning'] ?? null;
        $payRunEntry->fps = $payRunEntryData['fps'] ?? '';
        $payRunEntry->receivingOffsetPay = $payRunEntryData['receivingOffsetPay'] ?? null;
        $payRunEntry->paymentAfterLearning = $payRunEntryData['paymentAfterLearning'] ?? null;
        $payRunEntry->umbrellaPayment = $payRunEntryData['umbrellaPayment'] ?? '';
        $payRunEntry->employee = $payRunEntryData['employee'] ?? '';
//        $payRunEntry->pdf = $payRunEntryData['pdf'];

        $elementsService = Craft::$app->getElements();
        $success = $elementsService->saveElement($payRunEntry);

        if(!$success){
            Craft::error($payRunEntry->errors);
        }
    }

    protected function defaultDescription(): string
    {
        return sprintf(
            'Fetching Pay Run Entries from "%s"',
            $this->employerId
        );
    }
}
