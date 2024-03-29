<?php

namespace percipiolondon\staff\controllers;

use Craft;
use craft\helpers\ArrayHelper;
use craft\web\Controller;
use League\Csv\AbstractCsv;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\Statement;
use percipiolondon\staff\db\Table;
use percipiolondon\staff\elements\Employer;
use percipiolondon\staff\elements\PayRun;
use percipiolondon\staff\helpers\Security as SecurityHelper;
use percipiolondon\staff\records\PayLine as PayLineRecord;
use percipiolondon\staff\records\PayRunImport;

use percipiolondon\staff\Staff;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class PayRunController extends Controller
{
    /**
     * Payrun display
     *
     * @param string|null $siteHandle
     *
     * @return Response The rendered result
     * @throws NotFoundHttpException
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionIndex(): Response
    {
        $this->requireLogin();

        $variables = [];

        $pluginName = Staff::$plugin->settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Pay Runs');

        $variables['controllerHandle'] = 'payruns';
        $variables['pluginName'] = Staff::$plugin->settings->pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'payRuns';

        $variables['csrf'] = [
            'name' => Craft::$app->getConfig()->getGeneral()->csrfTokenName,
            'value' => Craft::$app->getRequest()->getCsrfToken(),
        ];

        // Render the template
        return $this->renderTemplate('staff-management/payruns/index', $variables);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionPayRunByEmployer(int $employerId): Response
    {
        $this->requireLogin();

        $variables = [];

        $employer = Employer::findOne($employerId);

        if(is_null($employer)) {
            throw new NotFoundHttpException('Employer does not exist');
        }

//        $payRuns = PayRunRecord::findAll(['employerId' => $employer['id']]);

        $pluginName = Staff::$plugin->settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Pay Runs > ' . $employer['name']);

        $variables['controllerHandle'] = 'payruns';
        $variables['pluginName'] = Staff::$plugin->settings->pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle} - {$employer['name']}";
        $variables['selectedSubnavItem'] = 'payRuns';

        $variables['employerId'] = $employer['id'];

        $variables['csrf'] = [
            'name' => Craft::$app->getConfig()->getGeneral()->csrfTokenName,
            'value' => Craft::$app->getRequest()->getCsrfToken(),
        ];

        // Render the template
        return $this->renderTemplate('staff-management/payruns/payruns', $variables);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionDetail(int $employerId, int $payRunId): Response
    {
        $this->requireLogin();

//        Staff::$plugin->payRuns->fetchPayRunByPayRunId($payRunId, true);

        $variables = [];

        $employer = Employer::findOne($employerId);
        $payRun = PayRun::findOne($payRunId);

        if (!$employer || !$payRun) {
            throw new NotFoundHttpException();
        }

//        $payRuns = PayRunRecord::findAll(['employerId' => $employer['id']]);
        $taxYear = $payRun['taxYear'] ?? '';
        $period = $payRun['period'] ?? '';

        $pluginName = Staff::$plugin->settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Pay Runs > ' . $employer->name . ' > ' . $taxYear . ' / ' . $period);

        $variables['controllerHandle'] = 'payruns';
        $variables['pluginName'] = Staff::$plugin->settings->pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle} - {$employer->name}";
        $variables['selectedSubnavItem'] = 'payRuns';

        $variables['employerId'] = $employer['id'];

        $variables['csrf'] = [
            'name' => Craft::$app->getConfig()->getGeneral()->csrfTokenName,
            'value' => Craft::$app->getRequest()->getCsrfToken(),
        ];

        // Render the template
        return $this->renderTemplate('staff-management/payruns/detail', $variables);
    }

    public function actionDownloadTemplate(int $payRunId): void
    {
        $this->requireLogin();

        $payRun = PayRun::findOne($payRunId);

        if (!$payRun) {
            throw new NotFoundHttpException();
        }

        Staff::$plugin->payRuns->getCsvTemplate($payRunId);
    }

    public function actionFetchPayRuns(int $employerId, string $taxYear = ''): Response
    {
        $this->requireLogin();
        $this->requireAcceptsJson();

        Staff::$plugin->payRuns->fetchPayRuns($employerId, $taxYear, true);

        return $this->asJson([
            'success' => true,
        ]);
    }

    public function actionFetchPayRun(int $payRunId): Response
    {
        $this->requireLogin();
        $this->requireAcceptsJson();

        Staff::$plugin->payRuns->fetchPayRunByPayRunId($payRunId, true);

        return $this->asJson([
            'success' => true,
        ]);
    }

    public function actionGetQueue(): Response
    {
        $this->requireLogin();
        $this->requireAcceptsJson();

        $queue = Craft::$app->getQueue();

        return $this->asJson([
            'total' => $queue->getTotalJobs(),
            'jobs' => $queue->getJobInfo(),
        ]);
    }

    public function actionGetPayRunLogs(int $payRunId): Response
    {
        $this->requireLogin();
        $this->requireAcceptsJson();

        $query = new \yii\db\Query();
        $query->from(['log' => Table::PAY_RUN_IMPORTS])
            ->select(['filename', 'rowCount', 'uploadedBy', 'payRunId', 'status', 'log.dateCreated', 'user.username', 'user.firstName', 'user.lastName'])
            ->innerJoin(['user' => \craft\db\Table::USERS],'`user`.`id` = `uploadedBy`')
            ->where(['payRunId' => $payRunId])
            ->orderBy(['dateCreated' => SORT_DESC]);

        return $this->asJson([
            'logs' => $query->all(),
        ]);
    }


    /**
     * @throws \League\Csv\UnableToProcessCsv
     * @throws \craft\errors\MissingComponentException
     * @throws Exception
     * @throws NotFoundHttpException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionImport()
    {
        $this->requireLogin();
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $payRunId = $request->getBodyParam('payRunId');
        $employerId = $request->getBodyParam('employerId');

        $employer = Employer::findOne($employerId);
        $payRun = PayRun::findOne($payRunId);

        if (!$employer || !$payRun) {
            throw new NotFoundHttpException();
        }

        //SAVE FILE
        $filename = "";
        $headers = null;
        $filePath = "";
        $error = "";

        $file = UploadedFile::getInstanceByName('file');

        if ($file !== null) {
            $filename = uniqid($file->name, true);
            $filePath = Craft::$app->getPath()->getTempPath() . DIRECTORY_SEPARATOR . $filename;
            $file->saveAs($filePath, false);
            // Also save the file to the cache as a backup way to access it
            $cache = Craft::$app->getCache();
            $fileHandle = fopen($filePath, 'r');
            if ($fileHandle) {
                $fileContents = fgets($fileHandle);
                if ($fileContents) {
                    $cache->set($filePath, $fileContents);
                }
                fclose($fileHandle);
            }
            // Read in the headers
//            $csv = Reader::createFromPath($file->tempName);
//            try {
//                $csv->setDelimiter(',');
//            } catch (Exception $e) {
//                Craft::error($e, __METHOD__);
//            }
//            $headers = $csv->fetchOne(0);
        }

        //PARSE CSV
        try {
            $csv = Reader::createFromPath($filePath);
            $csv->setDelimiter(',');
            $headers = array_flip($csv->fetchOne(0));
        } catch (\Exception $e) {
            // If this throws an exception, try to read the CSV file from the data cache
            // This can happen on load balancer setups where the Craft temp directory isn't shared
            $cache = Craft::$app->getCache();
            $cachedFile = $cache->get($filename);
            if ($cachedFile !== false) {
                $csv = Reader::createFromString($cachedFile);
                try {
                    $csv->setDelimiter(',');
                } catch (Exception $e) {
                    Craft::error($e, __METHOD__);
                }
                $headers = array_flip($csv->fetchOne(0));
                $cache->delete($filename);
            } else {
                Craft::error("Could not import ${$filename} from the file system, or the cache.", __METHOD__);
            }
        }

        //save log
        $importLog = new PayRunImport();
        $importLog->payRunId = $payRunId;
        $importLog->uploadedBy = Craft::$app->getUser()->id;
        $importLog->filepath = $filePath;
        $importLog->filename = $file->name;

        // If we have headers, then we have a file, so parse it
        if ($headers !== null) {
            $entries = $this->importCsvApi9($csv, $headers, $payRunId);

            if (count($entries) > 0) {
                $importLog->rowCount = count($entries);

                $success = $this->saveEntriesToStaffology($payRunId, $entries);

                if ($success) {
                    $importLog->status = 'Succeeded';
                    Craft::$app->getSession()->setNotice(Craft::t('staff-management', 'Imports from CSV started.'));
                } else {
                    $importLog->status = 'Failed';
                    $error = 'Staffology contains different pay codes than what existed when this CSV template was downloaded. Click "Refresh Pay Run" and download the new Pay Run Template.';
                }
            } else {
                $importLog->status = 'Failed';
                $error = "There is an error in the CSV template you are trying to upload. Have you altered a column heading or added any additional columns? Download the latest pay run template to check against your file.";
            }
            @unlink($filename);
        } else {
            $importLog->status = 'Failed';
            $error = "There was a technical problem. Please contact the developers at Percipio to solve.";
        }

        $importLog->save();

        // Render the template
        $employerName = SecurityHelper::decrypt($employer['name']) ?? '';
        $taxYear = $payRun['taxYear'] ?? '';
        $period = $payRun['period'] ?? '';

        $pluginName = Staff::$plugin->settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Pay Runs > ' . $employerName . ' > ' . $taxYear . ' / ' . $period);

        $variables['controllerHandle'] = 'payruns';
        $variables['pluginName'] = Staff::$plugin->settings->pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle} - {$employerName}";
        $variables['selectedSubnavItem'] = 'payRuns';
        $variables['error'] = $error;

        $variables['employerId'] = $employer['id'];

        return $this->renderTemplate('staff-management/payruns/detail', $variables);
    }

    protected function saveEntriesToStaffology(int $payRunId, array $entries): bool
    {
        $savedEntries = Staff::$plugin->payRunEntries->setPayRunEntry($entries);

        $updatedEntries = [];
        $payPeriod = null;
        $employer = null;

        foreach ($savedEntries as $entry) {
            $entry = $entry->toArray();

            $payPeriod = $entry['payPeriod'] ?? null;
            $employer = $entry['employerId'] ?? null;

            $csvEntry = array_filter($entries, function($csv) use ($entry) {
                return $csv['id'] == $entry['id'];
            });

            $csvEntry = reset($csvEntry) ?? [];
            $payRollCode = $csvEntry['payrollCode'] ?? null;

            $payLines = PayLineRecord::find()->where(['payOptionsId' => $entry['payOptionsId'] ?? null])->all();
            $regularPayLines = [];

            unset($csvEntry['id']);
            unset($csvEntry['name']);
            unset($csvEntry['niNumber']);
            unset($csvEntry['payrollCode']);
            unset($csvEntry['gross']);
            unset($csvEntry['netPay']);
            unset($csvEntry['totalCost']);

            foreach ($payLines as $payLine) {
                $payLine = $payLine->toArray();
                $code = $payLine['code'] ?? SecurityHelper::decrypt($payLine['value'] ?? '');

                if ($payLine && array_key_exists($code, $csvEntry)) {
                    $value = $csvEntry[$payLine['code'] ?? SecurityHelper::decrypt($payLine['value'] ?? '')];

                    //overwrite values
                    $payLine['value'] = $value;
                    $payLine['rate'] = SecurityHelper::decrypt($payLine['rate'] ?? '');
                    $payLine['description'] = $csvEntry['description_' . $payLine['code'] ?? $payLine['description'] ?? ''];

                    //reset in csvEntry to check which ones are new to save later on
                    $csvEntry[$payLine['code']] = '';

                    //remove own fields
                    unset($payLine['id']);
                    unset($payLine['dateCreated']);
                    unset($payLine['dateUpdated']);
                    unset($payLine['uid']);
                    unset($payLine['payOptionsId']);

                    if ($payLine['value'] != '') {
                        //save
                        $regularPayLines[] = $payLine;
                    }
                }
            }

            foreach (array_filter($csvEntry) as $key => $defaultPayCode) {
                if (strpos($key, 'description') === false && strlen($defaultPayCode) > 0) {
                    $payLine = [];
                    $payLine['code'] = $key;
                    $payLine['value'] = $defaultPayCode;

                    if (array_key_exists('description_' . $key, $csvEntry)) {
                        $payLine['description'] = $csvEntry['description_' . $key];
                    }

                    $regularPayLines[] = $payLine;
                }
            }

            $updatedEntries[] = [
                'payrollCode' => $payRollCode,
                'lines' => $regularPayLines,
            ];
        }

        return Staff::$plugin->payRunEntries->updatePayRunEntry($payPeriod, $employer, $payRunId, $updatedEntries);
    }

    /**
     * @param AbstractCsv $csv
     * @param array $columns
     * @param array $headers
     * @throws \League\Csv\Exception
     */
    protected function importCsvApi9(AbstractCsv $csv, array $headers, string $payRunId): array
    {
        $csvEntries = Staff::$plugin->payRuns->getCsvData($payRunId, true);

        if (count($csvEntries) > 0) {
            $columns = array_keys($csvEntries[0]);

            $stmt = (new Statement())
                ->offset(1)
            ;

            $rows = $stmt->process($csv);
            $columns = ArrayHelper::filterEmptyStringsFromArray($columns);
            $totalRows = count($csv) - 1;
            $rowCount = 0;

            $entries = [];

            foreach ($rows as $row) {
                $rowCount++;

                $entry = [];
                $errors = false;

                $index = 0;
                foreach ($columns as $importField) {
                    if (isset($columns[$index], $headers[$columns[$index]])) {
                        $entry[$importField] = empty($row[$headers[$columns[$index]]])
                            ? null
                            : $row[$headers[$columns[$index]]];
                    } else {
                        $errors = true;
                    }

                    $index++;
                }

                if (!$errors) {
                    $entries[] = $entry;
                }
            }


            return $entries;
        }

        return [];
    }
}
