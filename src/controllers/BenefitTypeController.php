<?php

namespace percipiolondon\staff\controllers;

use Craft;
use craft\helpers\DateTimeHelper;
use craft\web\Controller;
use percipiolondon\staff\elements\BenefitProvider;
use percipiolondon\staff\elements\BenefitType;
use percipiolondon\staff\elements\Employer;
use percipiolondon\staff\helpers\BenefitTypes;
use percipiolondon\staff\Staff;
use yii\web\Response;

class BenefitTypeController extends Controller
{
    /**
     * Benefit Types display
     *
     * @return Response The rendered result
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionIndex(): Response
    {
        $this->requireLogin();

        $variables = [];

        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Benefit Types');

//        Employer::findAll();

        $types = [];

        $benefitTypesHelper = new BenefitTypes();

        foreach(array_keys($benefitTypesHelper->benefitTypes) as $type){
            $types[] = BenefitType::findAll(['benefitType' => $type]);
        }

        $types = array_merge(...$types);

        $variables['controllerHandle'] = 'group-benefit-types';
        $variables['pluginName'] = $pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'benefits';
        $variables['types'] = $types;
        $variables['benefitTypes'] = $benefitTypesHelper->benefitTypes;

        // Render the template
        return $this->renderTemplate('staff-management/benefits/types', $variables);
    }

    /**
     * Benefit Type Edit display
     *
     * @return Response The rendered result
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionEdit(?int $typeId = null, ?string $benefitType = null): Response
    {
        $this->requireLogin();

        $type = null;

        if($typeId) {
            $type = BenefitType::findOne(['id' => $typeId, 'benefitType' => $benefitType]);
        }

        $benefitTypesHelper = new BenefitTypes();

        $variables = [];

        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Benefit Types');

        $variables['controllerHandle'] = 'benefit-type';
        $variables['pluginName'] = $pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'benefits';
        $variables['type'] = $type;
        $variables['types'] = $this->_getTypes();
        $variables['benefitTypes'] = $benefitTypesHelper->benefitTypes;
        $variables['providers'] = $this->_getProviders();
        $variables['errors'] = null;

        // Render the template
        return $this->renderTemplate('staff-management/benefits/types/form', $variables);
    }

    /**
     * @return Response
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionSave(): Response
    {
        $this->requireLogin();
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $elementsService = Craft::$app->getElements();
        $success = false;

        $typeId = $request->getBodyParam('typeId');
        $type = new BenefitType();

        if($typeId) {
            $type = BenefitType::findOne($typeId);
        }

        $type->providerId = $request->getBodyParam('providerId') ? (int)$request->getBodyParam('providerId') : null;
        $type->internalCode = $request->getBodyParam('internalCode');
        $type->status = $request->getBodyParam('status') == '1' ? 'active' : 'inactive';
        $type->policyName = $request->getBodyParam('policyName');
        $type->policyNumber = $request->getBodyParam('policyNumber');
        $type->policyHolder = $request->getBodyParam('policyHolder');
        $type->content = $request->getBodyParam('content');
        $type->policyStartDate = DateTimeHelper::toDateTime($request->getBodyParam('policyStartDate'));
        $type->policyRenewalDate = DateTimeHelper::toDateTime($request->getBodyParam('policyRenewalDate'));
        $type->paymentFrequency = $request->getBodyParam('paymentFrequency');
        $type->commissionRate = $request->getBodyParam('commissionRate') ? (float)$request->getBodyParam('commissionRate') : null;
        $type->benefitType = $request->getBodyParam('benefitType');

        //set the fields by the selected benefitType
        if($type->benefitType) {
            $benefitTypesHelper = new BenefitTypes();
            switch($type->benefitType) {
                case 'group-critical-illness-cover':
                    $fields = array_merge($type->toArray(), $request->getBodyParam('arrBenefitTypeGroupCriticalIllnessCover'));
                    $type->arrBenefitTypeGroupCriticalIllnessCover = $benefitTypesHelper->setGroupCriticalIllnessCover($fields, false);
                    break;
                case 'group-death-in-service':
                    $fields = array_merge($type->toArray(), $request->getBodyParam('arrBenefitTypeGroupDeathInService'));
                    $type->arrBenefitTypeGroupDeathInService = $benefitTypesHelper->setGroupDeathInService($fields, false);
                    break;
            }
        }

        if($type->validate()) {
            $success = $elementsService->saveElement($type);
        }

        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Benefit Types');

        $variables['controllerHandle'] = 'group-benefit-types';
        $variables['pluginName'] = $pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'benefits';
        $variables['types'] = BenefitType::findAll();
        $variables['type'] = $type;
        $variables['types'] = $this->_getTypes();
        $variables['providers'] = $this->_getProviders();
        $variables['errors'] = $type->getErrors();

        // Render the template
        if($success) {
            return $this->redirect('staff-management/benefits/types');
        }

        return $this->renderTemplate('staff-management/benefits/types/form', $variables);
    }

    private function _getTypes(): array {
        $types = [[
            'label' => Craft::t('staff-management', 'Choose a Benefit Type'),
            'value' => null,
        ]];

        $benefitTypes = new BenefitTypes();
        foreach($benefitTypes->benefitTypes as $key => $value){
            $types[] = [
                'value' => $key,
                'label' => $value,
            ];
        }

        return $types;
    }

    private function _getProviders(): array {
        $providers = [[
            'label' => Craft::t('staff-management', 'Choose a Benefit Provider'),
            'value' => null,
        ]];

        foreach(BenefitProvider::findAll() as $provider){
            $providers[] = [
                'value' => $provider->id,
                'label' => $provider->name,
            ];
        }

        return $providers;
    }
}