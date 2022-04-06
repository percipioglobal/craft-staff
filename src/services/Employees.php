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

use percipiolondon\staff\records\Address;
use percipiolondon\staff\records\Countries;
use percipiolondon\staff\records\LeaverDetails;
use percipiolondon\staff\records\LeaveSettings;
use percipiolondon\staff\records\StarterDetails;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\helpers\Security as SecurityHelper;
use percipiolondon\staff\elements\Employee;
use percipiolondon\staff\jobs\CreateEmployeeJob;

use percipiolondon\staff\records\DirectorshipDetails;
use percipiolondon\staff\records\EmploymentDetails;
use percipiolondon\staff\records\PersonalDetails;
use percipiolondon\staff\records\Employer as EmployerRecord;

use percipiolondon\staff\Staff;
use percipiolondon\staff\jobs\FetchEmployeesListJob;

use yii\db\Exception;

/**
 * Employees Service
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
class Employees extends Component
{
    // Public Methods
    // =========================================================================


    /* GETTERS */
    public function getEmployeeById(int $employeeId): array
    {
        $employee = Employee::findOne($employeeId);

        if(!$employee){
            return [];
        }

        $employee = $employee->toArray();

        $personalDetails = $this->getPersonalDetailssById($employee['personalDetailsId']);
        if ($personalDetails){
            $employee['personalDetails'] = $personalDetails;
        }

        $leaveSettings = $this->getLeaveSettingsById($employee['leaveSettingsId']);
        if ($leaveSettings){
            $employee['leaveSettings'] = $leaveSettings;
        }

        $employmentDetails = $this->getEmploymentDetailsById($employee['employmentDetailsId']);
        if ($employmentDetails){
            $employee['employmentDetails'] = $employmentDetails;
        }

        return $employee;
    }

    public function getEmploymentDetailsById(int $employmentDetailsId): array
    {
        $employmentDetails = EmploymentDetails::findOne($employmentDetailsId);

        if(!$employmentDetails){
            return [];
        }

        $employmentDetails = $employmentDetails->toArray();

        // directorship details
        $directorshipDetails = DirectorshipDetails::findOne($employmentDetails['directorshipDetailsId']);
        if ($directorshipDetails) {
            $employmentDetails['directorshipDetails'] = $directorshipDetails->toArray();
        }

        // starter details
        $starterDetails = StarterDetails::findOne($employmentDetails['starterDetailsId']);

        if ($starterDetails) {
            $employmentDetails['starterDetails'] = $starterDetails->toArray();
        }

        // leaver details
        $leaverDetails = LeaverDetails::findOne($employmentDetails['leaverDetailsId']);
        if ($leaverDetails) {
            $employmentDetails['leaverDetails'] = $leaverDetails->toArray();
        }

        return $employmentDetails;
    }

    public function getLeaveSettingsById(int $leaveSettingsId): array
    {
        $leaveSettings = LeaveSettings::findOne($leaveSettingsId);

        if(!$leaveSettings){
            return [];
        }

        return $leaveSettings->toArray();
    }

    public function getPersonalDetailssById(int $personalDetailsId): array
    {
        $personalDetails = PersonalDetails::findOne($personalDetailsId);

        if(!$personalDetails){
            return [];
        }

        $personalDetails = $personalDetails->toArray();

        // address
        $address = Address::findOne($personalDetails['addressId']);
        if ($address) {
            $address = $address->toArray();

            //country
            $country = Countries::findOne($address['countryId']);
            $address['country'] = $country['name'] ?? '';

            $personalDetails['address'] = $address;
        }

        return $personalDetails;
    }



    /* FETCHES */
    public function fetchEmployeesByEmployer(array $employer)
    {
        $queue = Craft::$app->getQueue();
        $queue->push(new FetchEmployeesListJob([
            'description' => 'Fetch the employees',
            'criteria' => [
                'employer' => $employer
            ]
        ]));

    }

    public function fetchEmployee(array $employee, array $employer)
    {
        $queue = Craft::$app->getQueue();
        $queue->push(new CreateEmployeeJob([
            'description' => 'Save employees',
            'criteria' => [
                'employee' => $employee,
                'employer' => $employer
            ]
        ]));
    }






    /* SAVES */
    public function saveEmployee(array $employee, string $employeeName, array $employer)
    {
        $logger = new Logger();
        $logger->stdout("✓ Save employee " .$employeeName . '...', $logger::RESET);

        $employeeRecord = Employee::findOne(['staffologyId' => $employee['id']]);

        try {

            if (!$employeeRecord) {
                $employeeRecord = new Employee();
            }

            //foreign keys
            $personalDetailsId = $employeeRecord->personalDetailsId ?? null;
            $employmentDetailsId = $employeeRecord->employmentDetailsId ?? null;
            $leaveSettingsId = $employeeRecord->leaveSettingsId ?? null;

            $personalDetails = $this->savePersonalDetails($employee['personalDetails'], $personalDetailsId);
            $employmentDetails = $this->saveEmploymentDetails($employee['employmentDetails'], $employmentDetailsId);
            $leaveSettings = $this->saveLeaveSettings($employee['leaveSettings'], $leaveSettingsId);
            $employerRecord = EmployerRecord::findOne(['staffologyId' => $employer['id']]);

            $employeeRecord->employerId = $employerRecord['id'] ?? null;
            $employeeRecord->staffologyId = $employee['id'];
            $employeeRecord->siteId = Craft::$app->getSites()->currentSite->id;
            $employeeRecord->personalDetailsId = $personalDetails->id ?? null;
            $employeeRecord->employmentDetailsId = $employmentDetails->id ?? null;
            $employeeRecord->leaveSettingsId = $leaveSettings->id ?? null;
            $employeeRecord->status = $employee['status'] ?? '';
            $employeeRecord->niNumber = $employee['personalDetails']['niNumber'] ?? null;
            $employeeRecord->userId = null;
            $employeeRecord->isDirector = $this->isDirector ?? false;

            // save new employee
            $elementsService = Craft::$app->getElements();
            $success = $elementsService->saveElement($employeeRecord);

            if($success){
                $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);
            }else{
                $logger->stdout(" failed" . PHP_EOL, $logger::FG_RED);

                $errors = "";

                foreach($employeeRecord->errors as $err) {
                    $errors .= implode(',', $err);
                }

                $logger->stdout($errors . PHP_EOL, $logger::FG_RED);
                Craft::error($employeeRecord->errors, __METHOD__);
            }

        } catch (\Exception $e) {

            $logger = new Logger();
            $logger->stdout(PHP_EOL, $logger::RESET);
            $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
            Craft::error($e->getMessage(), __METHOD__);
        }

    }

    public function saveEmploymentDetails(array $employmentDetails, int $employmentDetailsId = null): EmploymentDetails
    {
        if($employmentDetailsId) {
            $record = EmploymentDetails::findOne($employmentDetailsId);

            if (!$record) {
                throw new Exception('Invalid personal details ID: ' . $employmentDetailsId);
            }

        } else {
            $record = new EmploymentDetails();
        }

        $starterDetailsId = $record->starterDetailsId ?? null;

        $starterDetails = $this->saveStarterDetails($employmentDetails['starterDetails'], $starterDetailsId);

        $record->starterDetailsId = $starterDetails->id ?? null;
        $record->cisSubContractor = $employmentDetails['cisSubCopntractor'] ?? null;
        $record->payrollCode = SecurityHelper::encrypt($employmentDetails['payrollCode'] ?? '');
        $record->jobTitle = SecurityHelper::encrypt($employmentDetails['jobTitle'] ?? '');
        $record->onHold = $employmentDetails['onHold'] ?? null;
        $record->onFurlough = $employmentDetails['onFurlough'] ?? null;
        $record->furloughStart = $employmentDetails['furloughStart'] ?? null;
        $record->furloughEnd = $employmentDetails['furloughEnd'] ?? null;
        $record->furloughCalculationBasis = $employmentDetails['furloughCalculationBasis'] ?? null;
        $record->furloughCalculationBasisAmount = SecurityHelper::encrypt($employmentDetails['furloughCalculationBasisAmount'] ?? '');
        $record->partialFurlough = $employmentDetails['partialFurlough'] ?? null;
        $record->furloughHoursNormallyWorked = $employmentDetails['furloughHoursNormallyWorked'] ?? null;
        $record->furloughHoursOnFurlough = $employmentDetails['furloughHoursOnFurlough'] ?? null;
        $record->isApprentice = $employmentDetails['isApprentice'] ?? null;
        $record->apprenticeshipStartDate = $employmentDetails['apprenticeshipStartDate'] ?? null;
        $record->apprenticeshipEndDate = $employmentDetails['apprenticeshipEndDate'] ?? null;
        $record->workingPattern = $employmentDetails['workingPattern'] ?? null;
        $record->forcePreviousPayrollCode = SecurityHelper::encrypt($employmentDetails['forcePreviousPayrollCode'] ?? '');

        $record->save();

        return $record;
    }

    public function saveLeaveSettings(array $leaveSettings, int $leaveSettingsId = null): LeaveSettings
    {
        if($leaveSettingsId) {
            $record = LeaveSettings::findOne($leaveSettingsId);

            if (!$record) {
                throw new Exception('Invalid leave settings ID: ' . $leaveSettingsId);
            }

        } else {
            $record = new LeaveSettings();
        }

        $record->useDefaultHolidayType = $leaveSettings['useDefaultHolidayType'] ?? null;
        $record->useDefaultAllowanceResetDate = $leaveSettings['useDefaultAllowanceResetDate'] ?? null;
        $record->useDefaultAllowance = $leaveSettings['useDefaultAllowance'] ?? null;
        $record->useDefaultAccruePaymentInLieu = $leaveSettings['useDefaultAccruePaymentInLieu'] ?? null;
        $record->useDefaultAccruePaymentInLieuRate = $leaveSettings['useDefaultAccruePaymentInLieuRate'] ?? null;
        $record->useDefaultAccruePaymentInLieuAllGrossPay = $leaveSettings['useDefaultAccruePaymentInLieuAllGrossPay'] ?? null;
        $record->useDefaultAccruePaymentInLieuPayAutomatically = $leaveSettings['useDefaultAccruePaymentInLieuPayAutomatically'] ?? null;
        $record->useDefaultAccrueHoursPerDay = $leaveSettings['useDefaultAccrueHoursPerDay'] ?? null;
        $record->allowanceResetDate = $leaveSettings['allowanceResetDate'] ?? null;
        $record->allowance = $leaveSettings['allowance'] ?? null;
        $record->adjustment = $leaveSettings['adjustment'] ?? null;
        $record->allowanceUsed = $leaveSettings['allowanceUsed'] ?? null;
        $record->allowanceUsedPreviousPeriod = $leaveSettings['allowanceUsedPreviousPeriod'] ?? null;
        $record->allowanceRemaining = $leaveSettings['allowanceRemaining'] ?? null;
        $record->holidayType = $leaveSettings['holidayType'] ?? null;
        $record->accrueSetAmount = $leaveSettings['accrueSetAmount'] ?? null;
        $record->accrueHoursPerDay = $leaveSettings['accrueHoursPerDay'] ?? null;
        $record->showAllowanceOnPayslip = $leaveSettings['showAllowanceOnPayslip'] ?? null;
        $record->showAhpOnPayslip = $leaveSettings['showAhpOnPayslip'] ?? null;
        $record->accruePaymentInLieuRate = $leaveSettings['accruePaymentInLieuRate'] ?? null;
        $record->accruePaymentInLieuAllGrossPay = $leaveSettings['accruePaymentInLieuAllGrossPay'] ?? null;
        $record->accruePaymentInLieuPayAutomatically = $leaveSettings['accruePaymentInLieuPayAutomatically'] ?? null;
        $record->accruedPaymentLiability = $leaveSettings['accruedPaymentLiability'] ?? null;
        $record->accruedPaymentAdjustment = $leaveSettings['accruedPaymentAdjustment'] ?? null;
        $record->accruedPaymentPaid = $leaveSettings['accruedPaymentPaid'] ?? null;
        $record->accruedPaymentBalance = $leaveSettings['accruedPaymentBalance'] ?? null;

        $record->save();

        return $record;
    }

    public function saveStarterDetails(array $starterDetails, int $staterDetailsId = null): StarterDetails
    {
        if($staterDetailsId) {
            $record = StarterDetails::findOne($staterDetailsId);

            if (!$record) {
                throw new Exception('Invalid starter details ID: ' . $staterDetailsId);
            }

        } else {
            $record = new StarterDetails();
        }

        $record->startDate = $starterDetails['startDate'] ?? null;
        $record->starterDeclaration = $starterDetails['starterDeclaration'] ?? null;
        // overseasEmployerDetailsId
        // pensionerPayrollId

        $record->save();

        return $record;
    }

    public function savePersonalDetails(array $personalDetails, int $personalDetailsId = null): PersonalDetails
    {
        if($personalDetailsId) {
            $record = PersonalDetails::findOne($personalDetailsId);

            if (!$record) {
                throw new Exception('Invalid personal details ID: ' . $personalDetailsId);
            }

            //foreign keys
            $addressId = $record->addressId;

        } else {
            $record = new PersonalDetails();

            //foreign keys
            $addressId = null;
        }

        //foreign keys
        $address = Staff::$plugin->addresses->saveAddress($personalDetails['address'] ?? [], $addressId);

        $record->addressId = $address->id;

        $record->maritalStatus = $personalDetails['maritalStatus'] ?? 'Unknown';
        $record->title = SecurityHelper::encrypt($personalDetails['title'] ?? '');
        $record->firstName = SecurityHelper::encrypt($personalDetails['firstName'] ?? '');
        $record->middleName = SecurityHelper::encrypt($personalDetails['middleName'] ?? '');
        $record->lastName = SecurityHelper::encrypt($personalDetails['lastName'] ?? '');
        $record->email = SecurityHelper::encrypt($personalDetails['email'] ?? '');
        $record->emailPayslip = $personalDetails['emailPayslip'] ?? null;
        $record->passwordProtectPayslip = $personalDetails['passwordProtectPayslip'] ?? null;
        $record->pdfPassword = SecurityHelper::encrypt($personalDetails['pdfPassword'] ?? '');
        $record->telephone = SecurityHelper::encrypt($personalDetails['telephone'] ?? '');
        $record->mobile = SecurityHelper::encrypt($personalDetails['mobile'] ?? '');
        $record->dateOfBirth = $personalDetails['dateOfBirth'] ?? null;
        $record->statePensionAge = $personalDetails['statePensionAge'] ?? null;
        $record->gender = $personalDetails['gender'] ?? null;
        $record->niNumber = SecurityHelper::encrypt($personalDetails['niNumber'] ?? '');
        $record->passportNumber = SecurityHelper::encrypt($personalDetails['passportNumber'] ?? '');

        $record->save();

        return $record;
    }




    /* PARSE SECURITY VALUES */
    public function parsePersonalDetails(array $personalDetails): array
    {
        $personalDetails['title'] = SecurityHelper::decrypt($personalDetails['title'] ?? '');
        $personalDetails['firstName'] = SecurityHelper::decrypt($personalDetails['firstName'] ?? '');
        $personalDetails['middleName'] = SecurityHelper::decrypt($personalDetails['middleName'] ?? '');
        $personalDetails['lastName'] = SecurityHelper::decrypt($personalDetails['lastName'] ?? '');
        $personalDetails['email'] = SecurityHelper::decrypt($personalDetails['email'] ?? '');
        $personalDetails['pdfPassword'] = SecurityHelper::decrypt($personalDetails['pdfPassword'] ?? '');
        $personalDetails['telephone'] = SecurityHelper::decrypt($personalDetails['telephone'] ?? '');
        $personalDetails['mobile'] = SecurityHelper::decrypt($personalDetails['mobile'] ?? '');
        $personalDetails['niNumber'] = SecurityHelper::decrypt($personalDetails['niNumber'] ?? '');
        $personalDetails['passportNumber'] = SecurityHelper::decrypt($personalDetails['passportNumber'] ?? '');

        return $personalDetails;
    }

    public function parseEmploymentDetails(array $employmentDetails): array
    {
        $employmentDetails['payrollCode'] = SecurityHelper::decrypt($employmentDetails['payrollCode'] ?? '');
        $employmentDetails['jobTitle'] = SecurityHelper::decrypt($employmentDetails['jobTitle'] ?? '');
        $employmentDetails['furloughCalculationBasisAmount'] = SecurityHelper::decrypt($employmentDetails['furloughCalculationBasisAmount'] ?? '');
        $employmentDetails['forcePreviousPayrollCode'] = SecurityHelper::decrypt($employmentDetails['forcePreviousPayrollCode'] ?? '');

        return $employmentDetails;
    }
}
