<?php

/**
 * staff-management plugin for Craft CMS 3.x
 *
 * Craft Staff Management provides an HR solution for payroll and benefits
 *
 * @link      http://percipio.london
 * @copyright Copyright (c) 2021 Percipio
 */

namespace percipiolondon\staff\services;

use Craft;
use craft\base\Component;
use craft\queue\QueueInterface;
use percipiolondon\staff\elements\Employer;
use percipiolondon\staff\elements\PayRun;
use percipiolondon\staff\helpers\Csv as CsvHelper;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\helpers\Security as SecurityHelper;
use percipiolondon\staff\jobs\FetchPayRunJob;
use percipiolondon\staff\jobs\FetchPayRunsJob;
use percipiolondon\staff\records\Employee as EmployeeRecord;
use percipiolondon\staff\records\Employer as EmployerRecord;
use percipiolondon\staff\records\EmploymentDetails;
use percipiolondon\staff\records\PayCode;
use percipiolondon\staff\records\PayCode as PayCodeRecord;
use percipiolondon\staff\records\PayLine as PayLineRecord;
use percipiolondon\staff\records\PayOption;
use percipiolondon\staff\records\PayRun as PayRunRecord;
use percipiolondon\staff\records\PayRunEntry as PayRunEntryRecord;
use percipiolondon\staff\records\PayRunTotals;
use percipiolondon\staff\records\PersonalDetails;
use percipiolondon\staff\Staff;
use yii\queue\redis\Queue as RedisQueue;

/**
 * PayRun Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Percipio
 * @package   Staff
 * @since     1.0.0-alpha.1
 */
class PayRuns extends Component
{
    // Public Methods
    // =========================================================================

    /* GETTERS */
    /**
     * @param int $employerId
     * @return PayRun|null
     */
    public function getLastPayRunByEmployer(int $employerId): ?PayRun
    {
        return PayRun::find()
            ->employerId($employerId)
            ->orderBy([
                'taxYear' => SORT_DESC,
                'taxMonth' => SORT_DESC,
            ])
            ->one();

        //return $payrun ? $payrun->toArray() : [];
    }

    /**
     * @param int $payRunId
     */
    public function getCsvTemplate(int $payRunId): void
    {
        // fetch pay run
        $payRunQuery = PayRunRecord::findOne($payRunId);
        $payRunQuery = $payRunQuery ? $payRunQuery->toArray() : [];

        // fetch employer
        $employer = EmployerRecord::findOne($payRunQuery['employerId'] ?? null);
        $employer = $employer ? $employer->toArray() : [];
        $employer = Staff::$plugin->employers->parseEmployer($employer);

        $csvEntries = $this->getCsvData($payRunId);

        CsvHelper::arrayToCsv($csvEntries, 'pay-' . ($employer['slug'] ?? 'x') . '-' . ($payRunQuery['taxMonth'] ?? 'x') . '-' . strtolower($payRunQuery['taxYear']) ?? 'x');
    }

    /**
     * @param int $payRunId
     * @param bool $fetchHeaders
     * @return array
     */
    public function getCsvData(int $payRunId, bool $fetchHeaders = false): array
    {
        // fetch pay run
        $payRunQuery = PayRunRecord::findOne($payRunId);
        $payRunQuery = $payRunQuery ? $payRunQuery->toArray() : [];

        // fetch pay run entries
        $payRunId = $payRunQuery['id'] ?? null;
        $payRunEntries = $fetchHeaders ? [PayRunEntryRecord::findOne(['payRunId' => $payRunId])] : PayRunEntryRecord::findAll(['payRunId' => $payRunId]);

        // fetch employer
        $employer = EmployerRecord::findOne($payRunQuery['employerId'] ?? null);
        $employer = $employer ? $employer->toArray() : [];
        $employer = Staff::$plugin->employers->parseEmployer($employer);

        // fetch pay codes
        $payRunEmployerId = $employer['id'] ?? null;
        $payCodes = PayCodeRecord::find()->where(['employerId' => $payRunEmployerId, 'isSystemCode' => 0])->all();

        //create pay codes for all entries
        $payCodeKeys = array_map(function ($code) {
            return $code['code'];
        }, $payCodes);
        sort($payCodeKeys);

        $csvEntries = [];

        //fill in data
        foreach ($payRunEntries as $entry) {
            $employee = EmployeeRecord::findOne($entry['employeeId'] ?? null);
            $employee = $employee ? $employee->toArray() : [];

            //personalDetails
            $personalDetails = PersonalDetails::findOne(['employeeId' => ($employee['id'] ?? null)]);
            $personalDetails = $personalDetails?->toArray();

            //employmentDetails
            $employmentDetails = EmploymentDetails::findOne(['employeeId' => ($employee['id'] ?? null)]);
            $employmentDetails = $employmentDetails?->toArray();

            //totals
            $totals = PayRunTotals::findOne(["payRunEntryId" => $entry['id'] ?? null]);
            $totals = $totals?->toArray();

            $payOptions = PayOption::findOne(['payRunEntryId' => $entry['id']]);

            //payLines
            $payLines = PayLineRecord::find()->where(['payOptionsId' => ($payOptions->id ?? null)])->all();

            // decrypt values
            $personalDetails = Staff::$plugin->employees->parsePersonalDetails($personalDetails);
            $employmentDetails = Staff::$plugin->employees->parseEmploymentDetails($employmentDetails);
            $totals = Staff::$plugin->totals->parseTotals($totals);

            //CSV structure
            $payRunEntry = [];
            $payRunEntry['id'] = (int)($entry['id'] ?? null);
            $payRunEntry['name'] = ($personalDetails['title'] . ' ' ?? null) . ($personalDetails['firstName'] ?? null) . ' ' . ($personalDetails['lastName'] ?? null);
            $payRunEntry['niNumber'] = $personalDetails['niNumber'] ?? null;
            $payRunEntry['payrollCode'] = $employmentDetails['payrollCode'] ?? null;
            $payRunEntry['gross'] = (float)($totals['gross'] ?? 0);
            $payRunEntry['netPay'] = (float)($totals['netPay'] ?? 0);
            $payRunEntry['totalCost'] = (float)($totals['totalCost'] ?? 0);

            //set all the pay run codes dynamic to payRunEntry with default value
            foreach ($payCodeKeys as $payCodeKey) {
                $payRunEntry[$payCodeKey] = '';
                $payRunEntry['description_' . $payCodeKey] = '';
            }

            //overwrite custom pay lines
            foreach ($payLines as $payLine) {
                $payLine = $payLine->toArray();
                $payLine = Staff::$plugin->payOptions->parsePayLines($payLine);

                if ($payLine && in_array($payLine['code'], $payCodeKeys, true)) {
                    $payRunEntry[$payLine['code']] = (float)($payLine['value'] ?? '');
                    $payRunEntry['description_' . $payLine['code']] = $payLine['description'] ?? '';
                }
            }

            $csvEntries[] = $payRunEntry;
        }

        usort($csvEntries, function ($a, $b) {
            return $a['payrollCode'] > $b['payrollCode'];
        });

        return $csvEntries;
    }


    /* FETCHES */
    /**
     * @param array $payRuns
     * @param array $employer
     */
    public function fetchPayRuns(int|string $employer = '*', string $taxYear = '*', bool $startQueue = false): void
    {
        $queue = Craft::$app->getQueue();
        $queue->push(new FetchPayRunsJob([
            'criteria' => [
                'employers' => $employer === '*' ? Employer::findAll() : [Employer::findOne($employer)],
                'taxYear' => $taxYear === '*' ? null : $taxYear,
                'fetchEntries' => true
            ],
            'description' => 'Fetching pay run',
        ]));

        if ($startQueue) {
            $queue = Craft::$app->getQueue();
            if ($queue instanceof QueueInterface) {
                $queue->run();
            } elseif ($queue instanceof RedisQueue) {
                $queue->run(false);
            }
        }
    }

    /**
     * @param int $payRunId
     * @param bool $startQueue
     */
    public function fetchPayRunByPayRunId(int $payRunId, bool $startQueue = false): void
    {
        $payRun = PayRunRecord::findOne($payRunId);

        if ($payRun) {
            $employerId = $payRun['employerId'] ?? null;
            $employer = Employer::findOne($employerId);

            $queue = Craft::$app->getQueue();
            $queue->push(new FetchPayRunJob([
                'criteria' => [
                    'employer' => $employer,
                    'payRun' => $payRun,
                    'fetchEntries' => true
                ],
                'description' => 'Fetching pay run',
            ]));

            if ($startQueue) {
                $queue = Craft::$app->getQueue();
                if ($queue instanceof QueueInterface) {
                    $queue->run();
                } elseif ($queue instanceof RedisQueue) {
                    $queue->run(false);
                }
            }
        }
    }


    /* SYNCS */
    /**
     * Checks if our database has employers that are deleted on staffology, if so, delete them on our system
     *
     * @param array $employers
     */
    public function syncPayCode(array|Employer $employer, array $payCodes)
    {
        $logger = new Logger();
        $logger->stdout('↧ Sync pay codes of ' . $employer['name'] . PHP_EOL, $logger::RESET);

        $hubEmployer = is_array($employer) ? Employer::findOne(['staffologyId' => $employer['id']]) : $employer;
        $hubPayCodes = PayCode::find()->where(['employerId' => $hubEmployer['id']])->all();

        foreach ($hubPayCodes as $hubPayCode) {

            $exists = false;

            // loop through our employees and check if the employee is still on staffology
            foreach ($payCodes as $payCode) {
                if ($payCode['code'] === $hubPayCode['code']) {
                    $exists = true;
                }
            }

            // remove the employee if it doesn't exists anymore
            if (!$exists) {
                $logger->stdout('✓ Delete pay code ' . $hubPayCode['code'] . ' from ' . $employer['name'] . PHP_EOL, $logger::FG_YELLOW);

                $payCode = PayCode::findOne(['code' => $hubPayCode['code']]);
                if ($payCode) {
                    $payCode->delete();
                }
            }
        }
    }


    /**
     * Checks if our database has employers that are deleted on staffology, if so, delete them on our system
     *
     * @param array $employer
     * @param array $payruns
     */
    public function syncPayRuns(array|Employer $employer, array $payRuns)
    {
        $logger = new Logger();
        $logger->stdout('↧ Sync pay run of ' . $employer['name'] . PHP_EOL, $logger::RESET);

        $taxYear = $payRuns[0]['metadata']['taxYear'] ?? '';

        // only check to delete pay runs if we have a tax year
        if ($taxYear) {
            $hubEmployer = is_array($employer) ? Employer::findOne(['staffologyId' => $employer['id']]) : $employer;
            $hubPayRuns = PayRun::findAll(['employerId' => $hubEmployer['id'], 'taxYear' => $taxYear]);
            foreach ($hubPayRuns as $hubPayRun) {

                $exists = false;

                // loop through our pay runs and check if the pay run is still on staffology
                foreach ($payRuns as $payRun) {
                    // if ($payRun['url'] === 'https://api.staffology.co.uk' . $hubPayRun['url']) {
                    if ($payRun['url'] === $hubPayRun['url']) {
                        $exists = true;
                    }
                }

                // remove the pay run if it doesn't exists anymore
                if (!$exists) {
                    $logger->stdout('✓ Delete pay run ' . $hubPayRun['taxYear'] . '/' . $hubPayRun['taxMonth'] . ' from ' . $employer['name'] . PHP_EOL, $logger::FG_YELLOW);
                    Craft::$app->getElements()->deleteElementById($hubPayRun['id']);
                }
            }
        }
    }



    /* SAVES */
    /**
     * @param array $payCode
     * @param array $employer
     */
    public function savePayCode(array $payCode, array|Employer $employer): void
    {
        $logger = new Logger();
        $logger->stdout('✓ Save pay code ' . $payCode['code'] . '...', $logger::RESET);

        $employerRecord = is_int($employer['id'] ?? null) ? $employer : EmployerRecord::findOne(['staffologyId' => $employer['id'] ?? null]);
        $payCodeRecord = PayCodeRecord::findOne(['code' => $payCode['code'], 'employerId' => $employerRecord['id']]);

        if (!$payCodeRecord) {
            $payCodeRecord = new PayCodeRecord();
        }

        $payCodeRecord->title = $payCode['title'] ?? null;
        $payCodeRecord->code = $payCode['code'] ?? null;
        $payCodeRecord->employerId = $employerRecord->id ?? null;
        $payCodeRecord->defaultValue = SecurityHelper::encrypt($payCode['defaultValue'] ?? '');
        $payCodeRecord->isSystemCode = $payCode['isSystemCode'] ?? null;
        $success = $payCodeRecord->save();

        if ($success) {
            $logger->stdout(' done' . PHP_EOL, $logger::FG_GREEN);
        } else {
            $logger->stdout(' failed' . PHP_EOL, $logger::FG_RED);

            $errors = '';

            foreach ($payCodeRecord->errors as $err) {
                $errors .= implode(',', $err);
            }

            $logger->stdout($errors . PHP_EOL, $logger::FG_RED);
            Craft::error($payCodeRecord->errors, __METHOD__);
        }
    }

    /**
     * @param array $payRun
     * @param string $payRunUrl
     * @param array $employer
     * @throws \Throwable
     */
    public function savePayRun(array $payRun, string $payRunUrl, array $employer): ?PayRun
    {
        $logger = new Logger();
        $logger->stdout("✓ Save pay run of " . $employer['name'] . ' ' . $payRun['taxYear'] . ' / ' . $payRun['taxMonth'] . '...', $logger::RESET);

        $payRunRecord = PayRun::findOne(['url' => $payRunUrl]);

        try {
            if (!$payRunRecord) {
                $payRunRecord = new PayRun();
            }

            //foreign keys
            //            $totals = Staff::$plugin->totals->saveTotals($payRun['totals'] ?? [], $totalsId);
            $emp = is_int($employer['id'] ?? null) ? $employer : EmployerRecord::findOne(['staffologyId' => $employer['id'] ?? null]);

            $payRunRecord->employerId = $emp->id ?? $emp['id'] ?? null;
            $payRunRecord->taxYear = $payRun['taxYear'] ?? '';
            $payRunRecord->taxMonth = $payRun['taxMonth'] ?? null;
            $payRunRecord->payPeriod = $payRun['payPeriod'] ?? '';
            $payRunRecord->ordinal = $payRun['ordinal'] ?? null;
            $payRunRecord->period = $payRun['period'] ?? null;
            $payRunRecord->startDate = $payRun['startDate'] ?? null;
            $payRunRecord->endDate = $payRun['endDate'] ?? null;
            $payRunRecord->paymentDate = $payRun['paymentDate'] ?? null;
            $payRunRecord->employeeCount = $payRun['employeeCount'] ?? null;
            $payRunRecord->subContractorCount = $payRun['subContractorCount'] ?? null;
            $payRunRecord->state = $payRun['state'] ?? '';
            $payRunRecord->isClosed = $payRun['isClosed'] ?? '';
            $payRunRecord->dateClosed = $payRun['dateClosed'] ?? null;
            $payRunRecord->url = $payRunUrl ?? '';

            $elementsService = Craft::$app->getElements();
            $success = $elementsService->saveElement($payRunRecord);

            if ($success) {
                $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);

                if ($payRun['totals'] ?? null) {
                    Staff::$plugin->totals->savePayRunTotals($payRun['totals'], $payRunRecord->id);
                }
                //                $this->savePayRunLog($payRun, $payRunUrl, $payRunRecord->id, $employer['id']);

                return $payRunRecord;
            } else {
                $logger->stdout(" failed" . PHP_EOL, $logger::FG_RED);

                $errors = "";

                foreach ($payRunRecord->errors as $err) {
                    $errors .= implode(',', $err);
                }

                $logger->stdout($errors . PHP_EOL, $logger::FG_RED);
                Craft::error($payRunRecord->errors, __METHOD__);
            }
        } catch (\Exception $e) {
            $logger = new Logger();
            $logger->stdout(PHP_EOL, $logger::RESET);
            $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
            Craft::error($e->getMessage(), __METHOD__);
        }

        return null;
    }
}
