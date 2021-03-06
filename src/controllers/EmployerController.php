<?php

namespace percipiolondon\staff\controllers;

use craft\web\Controller;
use percipiolondon\staff\Staff;

class EmployerController extends Controller
{
    public function actionFetchOne(string $employer): void
    {
        // Fetch defaults
        Staff::$plugin->payRuns->fetchPayCodesList([$employer]);
        Staff::$plugin->pensions->fetchPensionSchemes([$employer]);

        Staff::$plugin->employers->fetchEmployers([$employer]);
    }
    public function actionFetch(array $employers): void
    {
        // Fetch defaults
        Staff::$plugin->payRuns->fetchPayCodesList($employers);
        Staff::$plugin->pensions->fetchPensionSchemes($employers);

        Staff::$plugin->employers->fetchEmployers($employers);
    }
}
