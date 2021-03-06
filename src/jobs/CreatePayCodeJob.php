<?php

namespace percipiolondon\staff\jobs;

use Craft;
use craft\helpers\App;
use craft\helpers\Json;
use craft\queue\BaseJob;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\Staff;

class CreatePayCodeJob extends BaseJob
{
    public $criteria;

    public function execute($queue): void
    {
        $logger = new Logger();

        // connection props
        $api = App::parseEnv(Staff::$plugin->getSettings()->apiKeyStaffology);
        $credentials = base64_encode('staff:' . $api);
        $headers = [
            'headers' => [
                'Authorization' => 'Basic ' . $credentials,
            ],
        ];

        $client = new \GuzzleHttp\Client();

        $currentPayCode = 0;
        $totalPayCodes = count($this->criteria['payCodes']);

        $payCodes = [];

        foreach ($this->criteria['payCodes'] as $payCode) {
            $currentPayCode++;
            $progress = "[" . $currentPayCode . "/" . $totalPayCodes . "] ";

            $logger->stdout($progress . "↧ Fetch pay code info from " . $payCode['name'], $logger::RESET);

            try {
                $response = $client->get($payCode['url'], $headers);
                $result = Json::decodeIfJson($response->getBody()->getContents(), true);

                $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);

                $payCodes[] = $result;

            } catch (\Exception $e) {
                $logger->stdout(PHP_EOL, $logger::RESET);
                $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
                Craft::error($e->getMessage(), __METHOD__);
            }

            $this->setProgress($queue, $currentPayCode / $totalPayCodes);
        }

        //Delete existing if they don't exist on Staffology anymore
        Staff::$plugin->payRuns->syncPayCode($this->criteria['employer'], $payCodes);

        //Save pay codes
        foreach($payCodes as $payCode) {
            Staff::$plugin->payRuns->savePayCode($payCode, $this->criteria['employer']);
        }
    }
}
