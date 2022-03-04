<?php

namespace percipiolondon\staff\jobs;

use craft\helpers\App;
use craft\helpers\Json;
use craft\queue\BaseJob;
use percipiolondon\staff\Staff;
use percipiolondon\staff\helpers\Logger;
use Craft;

class FetchPayCodesListJob extends BaseJob
{
    public $criteria;

    public function execute($queue): void
    {
        $logger = new Logger();

        // connection props
        $api = App::parseEnv(Staff::$plugin->getSettings()->staffologyApiKey);
        $base_url = 'https://api.staffology.co.uk/';
        $credentials = base64_encode('staff:'.$api);
        $headers = [
            'headers' => [
                'Authorization' => 'Basic ' . $credentials,
            ],
        ];

        $client = new \GuzzleHttp\Client();

        foreach($this->criteria['employers'] as $employer) {

            $logger->stdout("↧ Fetching pay codes of " . $employer['name'] . '...', $logger::RESET);

            try {
                $response = $client->get($base_url.'employers/'.$employer['id'].'/paycodes', $headers);
                $payCodes = Json::decodeIfJson($response->getBody()->getContents(), true);

                $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);

                Staff::$plugin->payRun->fetchPayCodes($payCodes);
            } catch (\Exception $e) {

                $logger->stdout(PHP_EOL, $logger::RESET);
                $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
                Craft::error($e->getMessage(), __METHOD__);

            }

        }
    }
}
