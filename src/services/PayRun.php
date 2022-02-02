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

use craft\helpers\Queue;
use percipiolondon\staff\Staff;

use Craft;
use craft\base\Component;
use percipiolondon\staff\jobs\CreatePayRunJob;
use percipiolondon\staff\jobs\FetchPaySlip;
use percipiolondon\staff\records\Employer;
use percipiolondon\staff\records\PayRunLog as PayRunLogRecord;
use percipiolondon\staff\records\PayRun as PayRunRecord;
use percipiolondon\staff\records\PayRunEntry as PayRunEntryRecord;
use yii\base\BaseObject;

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
class PayRun extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     Staff::$plugin->payRun->exampleService()
     *
     * @return mixed
     */
    public function fetch()
    {
        $api = Craft::parseEnv(Staff::$plugin->getSettings()->staffologyApiKey);
        $credentials = base64_encode('staff:'.$api);
        $headers = [
            'headers' => [
                'Authorization' => 'Basic ' . $credentials,
            ],
        ];
        $client = new \GuzzleHttp\Client();

        if ($api) {
            // GET EMPLOYERS
            $employers = Employer::find()->all();

            foreach($employers as $employer) {
                $base_url = "https://api.staffology.co.uk/employers/{$employer->staffologyId}/schedules/{$employer->currentYear}";

                //GET LIST OF PAYSCHEDULES
                try {

                    $response = $client->get($base_url, $headers);
                    $paySchedules = json_decode($response->getBody()->getContents(), true);

                    Queue::push(new CreatePayRunJob([
                        'headers' => $headers,
                        'paySchedules' => $paySchedules,
                        'employerId' => $employer->id,
                    ]));

                } catch (\Throwable $e) {
                    echo "---- error -----\n";
                    var_dump($e->getMessage());
                    Craft::error($e->getMessage(), __METHOD__);
//                    Craft::dd($e);
                    echo "\n---- end error ----";
                }
            }
        }

        return "success";
    }

    public function fetchPayslips()
    {
        $api = Craft::parseEnv(Staff::$plugin->getSettings()->staffologyApiKey);
        $credentials = base64_encode('staff:'.$api);
        $headers = [
            'headers' => [
                'Authorization' => 'Basic ' . $credentials,
                'Accept' => 'application/pdf'
            ],
        ];

        if ($api) {
            // GET EMPLOYERS
            $payRunEntries = PayRunEntryRecord::find()->all();

            foreach($payRunEntries as $payRunEntry) {

                try {

                    Queue::push(new FetchPaySlip([
                        'headers' => $headers,
                        'employerId' => $payRunEntry->employerId ?? null,
                        'payPeriod' => $payRunEntry->payPeriod ?? null,
                        'periodNumber' => $payRunEntry->period ?? null,
                        'taxYear' => $payRunEntry->taxYear ?? null,
                        'payRunEntry' => $payRunEntry ?? null
                    ]));

                } catch (\Throwable $e) {
                    echo "---- error -----\n";
                    var_dump($e->getMessage());
                    Craft::error($e->getMessage(), __METHOD__);
//                    Craft::dd($e);
                    echo "\n---- end error ----";
                }
            }
        }

        return "success";
    }
}
