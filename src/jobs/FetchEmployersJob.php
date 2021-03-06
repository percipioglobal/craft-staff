<?php

namespace percipiolondon\staff\jobs;

use Craft;
use craft\helpers\App;
use craft\helpers\Json;
use craft\queue\BaseJob;
use percipiolondon\staff\elements\Employer;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\Staff;

class FetchEmployersJob extends BaseJob
{
    public $criteria;

    public function execute($queue): void
    {
        $logger = new Logger();

        $currentEmployer = 0;
        $totalEmployers = count($this->criteria['employers']);

        // connection props
        $api = App::parseEnv(Staff::$plugin->getSettings()->apiKeyStaffology);
        $credentials = base64_encode('staff:' . $api);
        $headers = [
            'headers' => [
                'Authorization' => 'Basic ' . $credentials,
            ],
        ];

        $client = new \GuzzleHttp\Client();

        //Delete existing if they don't exist on Staffology anymore
        Staff::$plugin->employers->syncEmployers($this->criteria['employers']);

        //Kickstart the sync process for each employer
        foreach ($this->criteria['employers'] as $employer) {
            $currentEmployer++;
            $progress = "[" . $currentEmployer . "/" . $totalEmployers . "] ";

            $logger->stdout($progress . "↧ Fetch employer info from " . $employer['name'] . " (" . $employer['id'] . ")", $logger::RESET);

            try {
                $response = $client->get($employer['url'], $headers);
                $result = Json::decodeIfJson($response->getBody()->getContents(), true);

                $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);

                Staff::$plugin->employers->saveEmployer($result);
                Staff::$plugin->payRuns->fetchPayCodesList($employer);
                Staff::$plugin->employees->fetchEmployeesByEmployer($employer);
            } catch (\Exception $e) {
                $logger->stdout(PHP_EOL, $logger::RESET);
                $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
                Craft::error($e->getMessage(), __METHOD__);
            }

            $this->setProgress($queue, $currentEmployer / $totalEmployers);
        }
    }
}
