<?php
/**
 * staff-management plugin for Craft CMS 3.x
 *
 * Craft Staff Management provides an HR solution for payroll and benefits
 *
 * @link      http://percipio.london
 * @copyright Copyright (c) 2021 Percipio
 */

namespace percipiolondon\staff\migrations;

use Craft;

use craft\db\Migration;
use craft\db\Query;
use percipiolondon\staff\db\Table;

/**
 * Installation Migration
 *
 *
 * @author    Percipio Global Ltd. <support@percipio.london>
 * @since     1.0.0
 */
class Install extends Migration
{
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->createIndexes();
            $this->addForeignKeys();
            Craft::$app->db->schema->refresh();
            $this->insertDefaultData();
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {

//        $this->dropForeignKeys();
//        $this->dropTables();

        return true;
    }

    /**
     * Creates the tables for Staff Management
     */

    public function createTables()
    {
        $tableCreated = false;

        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::EMPLOYERS);
        if ($tableSchema === null) {
            // BASE
            $this->createTable(Table::EMPLOYEES, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employerId' => $this->integer()->notNull(), // create FK to Employer [id]
                'userId' => $this->integer(), // create FK to Craft User [id]
                //fields
                'staffologyId' => $this->string(255)->notNull(),
                'isDirector' => $this->boolean(),
                'status' => $this->enum('status', ['Current', 'Former', 'Upcoming'])->notNull(),
                'aeNotEnroledWarning' => $this->boolean()->defaultValue(0),
                'niNumber' => $this->string(255),
                'sourceSystemId' => $this->string(255),
            ]);

            $this->createTable(Table::EMPLOYERS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //fields
                'staffologyId' => $this->string(255)->notNull(),
                'name' => $this->string(255)->notNull(),
                'crn' => $this->string(),
                'logoUrl' => $this->string(),
                'alternativeId' => $this->string(),
                'bankPaymentsCsvFormat' => $this->enum('status', ['StandardCsv', 'Telleroo', 'BarclaysBacs', 'SantanderBacs', 'Sif', 'Revolut', 'Standard18FasterPayments', 'Standard18Bacs', 'Bankline', 'BanklineBulk', 'StandardCsvBacs', 'LloydsMultipleStandardCsvBacs', 'LloydsV11CsvBacs', 'CoOpBulkCsvBacs', 'CoOpFasterPaymentsCsv']),
                'bacsServiceUserNumber' => $this->string(),
                'bacsBureauNumber' => $this->string(),
                'rejectInvalidBankDetails' => $this->boolean(),
                'bankPaymentsReferenceFormat' => $this->string(),
                'useTenantRtiSubmissionSettings' => $this->boolean(),
                'employeeCount' => $this->integer(),
                'subcontractorCount' => $this->integer(),
                'startYear' => $this->string(255)->notNull(),
                'currentYear' => $this->string(255)->notNull(),
                'supportAccessEnabled' => $this->boolean(),
                'archived' => $this->boolean(),
                'canUseBureauFeatures' => $this->string(),
                'sourceSystemId' => $this->string(),
                //custom
                'slug' => $this->string(255)->notNull(),
            ]);

            $this->createTable(Table::PAYRUN_IMPORTS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'payRunId' => $this->integer()->notNull(), // create FK to PayRun [id]
                'uploadedBy' => $this->integer()->notNull(), // create FK to User [id]
                'approvedBy' => $this->integer(), // create FK to User [id]
                //fields
                'filepath' => $this->string(255)->notNull()->defaultValue(''),
                'filename' => $this->string(255)->notNull()->defaultValue(''),
                'rowCount' => $this->integer()->defaultValue(0),
                'status' => $this->enum('status', ['Succeeded', 'Failed']),
                'dateApproved' => $this->dateTime(),
            ]);

            $this->createTable(Table::PAYRUN, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employerId' => $this->integer()->notNull()->defaultValue(null), // create FK to Employer [id]
                //fields
                'taxYear' => $this->string(255)->notNull()->defaultValue(''),
                'taxMonth' => $this->integer()->notNull()->defaultValue(0),
                'payPeriod' => $this->string(255)->notNull()->defaultValue(''),
                'ordinal' => $this->integer()->notNull()->defaultValue(1),
                'period' => $this->integer()->notNull()->defaultValue(1),
                'startDate' => $this->dateTime()->notNull(),
                'endDate' => $this->dateTime()->notNull(),
                'paymentDate' => $this->dateTime()->notNull(),
                'employeeCount' => $this->integer()->notNull()->defaultValue(0),
                'subContractorCount' => $this->integer()->notNull()->defaultValue(0),
                'state' => $this->string(255)->notNull()->defaultValue(''),
                'isClosed' => $this->boolean()->notNull(),
                'dateClosed' => $this->dateTime(),
                'autoPilotCloseDate' => $this->string(),
                'url' => $this->string()->defaultValue(''),
            ]);

            $this->createTable(Table::PAYRUN_ENTRIES, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                // FK
                //intern
                'employeeId' => $this->integer()->notNull(),
                'employerId' => $this->integer()->notNull()->defaultValue(null),
                'payRunId' => $this->integer()->notNull()->defaultValue(0),
                // fields
                "staffologyId" => $this->string(255)->notNull(), // staffology: id
                'taxYear' => $this->string(255)->defaultValue(''),
                'startDate' => $this->dateTime(),
                'endDate' => $this->dateTime(),
                'note' => $this->mediumText(),
                'bacsSubReference' => $this->string(255)->defaultValue(''),
                'bacsHashcode' => $this->string(255)->defaultValue(''),
                'percentageOfWorkingDaysPaidAsNormal' => $this->double()->defaultValue(0),
                'workingDaysNotPaidAsNormal' => $this->double()->defaultValue(0),
                'payPeriod' => $this->string(255)->defaultValue(''),
                'ordinal' => $this->integer()->defaultValue(1),
                'period' => $this->integer()->defaultValue(1),
                'isNewStarter' => $this->boolean(),
                'unpaidAbsence' => $this->boolean(),
                'hasAttachmentOrders' => $this->boolean(),
                'paymentDate' => $this->dateTime(),
                'forcedCisVatAmount' => $this->double()->defaultValue(0),
                'holidayAccrued' => $this->double()->defaultValue(0),
                'state' => $this->string(255)->defaultValue('Open'),
                'isClosed' => $this->boolean(),
                'manualNi' => $this->boolean(),
                'payrollCodeChanged' => $this->boolean(),
                'aeNotEnroledWarning' => $this->boolean(),
                'receivingOffsetPay' => $this->boolean(),
                'paymentAfterLearning' => $this->boolean(),
                'pdf' => $this->mediumText(),
            ]);

            $this->createTable(Table::PAYRUN_LOG, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                // FK
                //intern
                'employerId' => $this->integer()->notNull()->defaultValue(0),
                'payRunId' => $this->integer()->notNull()->defaultValue(0),
                // fields
                'employeeCount' => $this->integer()->notNull()->defaultValue(0),
                'taxYear' => $this->string(255)->notNull()->defaultValue(''),
                'lastPeriodNumber' => $this->integer()->notNull()->defaultValue(0),
                'url' => $this->string(255)->notNull()->defaultValue(0),
            ]);

            $this->createTable(Table::PENSIONS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern (also present in staffology call, but needs to be refactored)
                'employeeId' => $this->integer(), //create FK to Employees [id]
                //fields
                "staffologyId" => $this->string(), // staffology: id
                'contributionLevelType' => $this->enum('contributionLevelType', ['UserDefined', 'StatutoryMinimum', 'Nhs2015', 'Tp2020']),
                'startDate' => $this->string(),
                'memberReferenceNumber' => $this->string(),
                'overrideContributions' => $this->boolean(),
                'employeeContribution' => $this->double(),
                'employeeContributionIsPercentage' => $this->boolean(),
                'employerContribution' => $this->double(),
                'employerContributionIsPercentage' => $this->boolean(),
                'employerContributionTopUpPercentage' => $this->double(),
                'isAeQualifyingScheme' => $this->double(),
                'isTeachersPension' => $this->double(),
                'aeStatusAtJoining' => $this->enum('contributionLevelType', ['Eligible', 'NonEligible', 'Entitled', 'NoDuties']),
                'additionalVoluntaryContribution' => $this->double(),
                'avcIsPercentage' => $this->boolean(),
                'exitViaProvider' => $this->boolean(),
                'forceEnrolment' => $this->boolean(),
                'autoEnrolled' => $this->boolean(),
            ]);

            $this->createTable(Table::PERMISSIONS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //fields
                'name' => $this->string(255)->notNull()->defaultValue(''),
            ]);

            $this->createTable(Table::PERMISSIONS_USERS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //internal
                'permissionId' => $this->integer()->notNull()->defaultValue(0), //create FK to Permissions [id]
                'userId' => $this->integer()->defaultValue(null), //create FK to User [id]
                'employeeId' => $this->integer()->notNull()->defaultValue(0), //create FK to Employees [id]
            ]);

            $this->createTable(Table::REQUESTS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                'employerId' => $this->integer()->notNull(), //create FK to Employers [id]
                'employeeId' => $this->integer()->notNull(), //create FK to Employees [id]
                'administerId' => $this->integer()->notNull(), //create FK to User [id]
                //fields
                'dateAdministered' => $this->dateTime()->notNull(),
                'data' => $this->longText(),
                'section' => $this->string()->notNull(),
                'element' => $this->string()->notNull(),
                'status' => $this->string()->notNull(),
                'note' => $this->mediumText(),
            ]);

            $this->createTable(Table::HISTORY, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //internal
                'employerId' => $this->integer()->notNull(), //create FK to Employer [id]
                'employeeId' => $this->integer()->notNull(), //create FK to Employee [id]
                'administerId' => $this->integer(), //create FK to Craft User [id] // This can be null
                //fields
                'message' => $this->string(255)->notNull(),
                'type' => $this->string()->notNull(),
            ]);


//            // LINKAGE
            $this->createTable(Table::ADDRESSES, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                'employeeId' => $this->integer(), // create FK to Employers [id]
                'employerId' => $this->integer(), // create FK to Employers [id]
                'countryId' => $this->integer(), // create FK to Countries [id]
                //fields
                'address1' => $this->string(),
                'address2' => $this->string(),
                'address3' => $this->string(),
                'address4' => $this->string(),
                'address5' => $this->string(),
                'zipCode' => $this->string(),
            ]);

            $this->createTable(Table::AUTO_ENROLMENT, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employeeId' => $this->integer(), // create FK to Employee [id]
                //fields
                'state' => $this->enum('state', ['Automatic', 'OptOut', 'OptIn', 'VoluntaryJoiner', 'ContractualPension', 'CeasedMembership', 'Leaver', 'Excluded', 'Enrol']),
                'stateDate' => $this->dateTime(),
                'ukWorker' => $this->enum('status', ['No', 'Yes', 'Ordinarily']),
                'daysToDeferAssessment' => $this->integer(),
                'postponementData' => $this->dateTime(),
                'deferByMonthsNotDays' => $this->boolean(),
                'exempt' => $this->boolean(),
                'aeExclusionCode' => $this->enum('code', ['NotKnown', 'NotAWorker', 'NotWorkingInUk', 'NoOrdinarilyWorkingInUk', 'OutsideOfAgeRange', 'SingleEmployee', 'CeasedActiveMembershipInPast12Mo', 'CeasedActiveMembership', 'ReceivedWulsInPast12Mo', 'ReceivedWuls', 'Leaving', 'TaxProtection', 'CisSubContractor']),
                'aePostponementLetterSent' => $this->boolean(),
            ]);

            $this->createTable(Table::AUTO_ENROLMENT_ASSESSMENT, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //staffology
                'autoEnrolmentId' => $this->integer(), // create FK AutoEnrolment [id]
                //fields
                "staffologyId" => $this->integer(),
                'assessmentDate' => $this->dateTime(),
                'employeeState' => $this->enum('state', ['Automatic', 'OptOut', 'OptIn', 'VoluntaryJoiner', 'ContractualPension', 'CeasedMembership', 'Leaver', 'Excluded', 'Enrol']),
                'age' => $this->integer(),
                'ukWorker' => $this->enum('status', ['No', 'Yes', 'Ordinarily']),
                'payPeriod' => $this->enum('period', ['Custom', 'Monthly', 'FourWeekly', 'Fortnightly', 'Weekly', 'Daily']),
                'ordinal' => $this->integer(),
                'earningsInPeriod' => $this->double(),
                'qualifyingEarningsInPeriod' => $this->double(),
                'aeExclusionCode' => $this->enum('code', ['NotKnown', 'NotAWorker', 'NotWorkingInUk', 'NoOrdinarilyWorkingInUk', 'OutsideOfAgeRange', 'SingleEmployee', 'CeasedActiveMembershipInPast12Mo', 'CeasedActiveMembership', 'ReceivedWulsInPast12Mo', 'ReceivedWuls', 'Leaving', 'TaxProtection', 'CisSubContractor']),
                'status' => $this->enum('status', ['Eligible', 'NonEligible', 'Entitled', 'NoDuties']),
                'reason' => $this->string(),
                'assessmentId' => $this->uid(),
            ]);

            $this->createTable(Table::AUTO_ENROLMENT_ASSESSMENT_ACTION, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'autoEnrolmentAssessmentId' => $this->integer(), //create FK to AutoEnrolmentAssessement [id]
                //fields
                'action' => $this->enum('status', ['NoChange', 'Enrol', 'Exit', 'Inconclusive', 'Postpone']),
                'employeeState' => $this->enum('state', ['Automatic', 'OptOut', 'OptIn', 'VoluntaryJoiner', 'ContractualPension', 'CeasedMembership', 'Leaver', 'Excluded', 'Enrol']),
                'actionCompleted' => $this->boolean(),
                'actionCompletedMessage' => $this->string(),
                'requiredLetter' => $this->enum('status', ['B1', 'B2', 'B3']),
                'pensionSchemeId' => $this->string(),
                'workerGroupId' => $this->string(),
                'letterNotYetSent' => $this->boolean(),
            ]);

            $this->createTable(Table::AUTO_ENROLMENT_SETTINGS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employerId' => $this->integer(), //create FK to Employer [id]
                //fields
                "staffologyId" => $this->string(),
                'stagingDate' => $this->string(),
                'cyclicalReenrolmentDate' => $this->string(),
                'previousCyclicalReenrolmentDate' => $this->string(),
                'pensionSameAsDefault' => $this->boolean(),
                'daysToDeferAssessment' => $this->integer(),
                'deferByMonthsNotDays' => $this->boolean(),
                'deferEnrolmentBy' => $this->integer(),
                'deferEnrolmentByPeriodType' => $this->string(),
                'includeNonPensionedEmployeesInSubmission' => $this->boolean(),
            ]);

            $this->createTable(Table::BANK_DETAILS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employerId' => $this->integer(), //create FK to Employer [id]
                'employeeId' => $this->integer(), //create FK to Employee [id]
                //fields
                'bankName' => $this->string(),
                'bankBranch' => $this->string(),
                'bankReference' => $this->string(),
                'accountName' => $this->string(),
                'accountNumber' => $this->string(),
                'sortCode' => $this->string(),
                'note' => $this->mediumText(),
            ]);

            $this->createTable(Table::CIS_DETAILS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employmentDetailsId' => $this->integer(), //create FK EmploymentDetails [id]
                //staffology
//                'verificationId' => $this->integer(), // create FK CisVerificationDetails [id]
                //fields
                'type' => $this->enum('type', ['SoleTrader', 'Partnership', 'Company', 'Trust']),
                'utr' => $this->string(),
                'tradingName' => $this->string(),
                'companyUtr' => $this->string(),
                'companyNumber' => $this->string(),
                'vatRegistered' => $this->boolean(),
                'vatNumber' => $this->string(),
                'vatRate' => $this->double(),
                'reverseChargeVAT' => $this->boolean(),
            ]);

//            $this->createTable(Table::CIS_PARTNERSHIP, [
//                'id' => $this->primaryKey(),
//                'dateCreated' => $this->dateTime()->notNull(),
//                'dateUpdated' => $this->dateTime()->notNull(),
//                'uid' => $this->uid(),
//                //fields
//                'name' => $this->string(),
//                'utr' => $this->string(),
//            ]);
//
//            $this->createTable(Table::CIS_SUBCONTRACTOR, [
//                'id' => $this->primaryKey(),
//                'dateCreated' => $this->dateTime()->notNull(),
//                'dateUpdated' => $this->dateTime()->notNull(),
//                'uid' => $this->uid(),
//                //FK
//                //staffology
//                'itemId' => $this->integer(), // create FK Items [id]
//                'nameId' => $this->integer(), // create FK RtiEmployeeName [id]
//                'partnershipId' => $this->integer(), // create FK CisPartnership [id]
//                'addressId' => $this->integer(), // create FK RtiEmployeeAddress [id]
//                //fields
//                'employeeUniqueId' => $this->string(),
//                'emailStatementTo' => $this->string(),
//                'numberOfPayments' => $this->integer(),
//                'displayName' => $this->string(),
//                'action' => $this->string(),
//                'type' => $this->string(),
//                'tradingName' => $this->string(),
//                'worksRef' => $this->string(),
//                'unmatchedRate' => $this->string(),
//                'utr' => $this->string(),
//                'crn' => $this->string(),
//                'nino' => $this->string(),
//                'telephone' => $this->string(),
//                'totalPaymentsUnrounded' => $this->string(),
//                'costOfMaterialsUnrounded' => $this->string(),
//                'umbrellaFee' => $this->string(),
//                'validationMsg' => $this->string(),
//                'verificationNumber' => $this->string(),
//                'totalPayments' => $this->string(),
//                'costOfMaterials' => $this->string(),
//                'totalDeducted' => $this->string(),
//                'matched' => $this->string(),
//                'taxTreatment' => $this->string(),
//            ]);
//
//            $this->createTable(Table::CIS_VERIFICATION_DETAILS, [
//                'id' => $this->primaryKey(),
//                'dateCreated' => $this->dateTime()->notNull(),
//                'dateUpdated' => $this->dateTime()->notNull(),
//                'uid' => $this->uid(),
//                //FK
//                'cisSubcontractorId' => $this->integer(), // create FK CisSubcontractor [id] // verificationResponse to the CisSubContractor Table, as a Verification Response return a CisSubContractor Object
//                //fields
//                'manuallyEntered' => $this->boolean(),
//                'matchInsteadOfVerify' => $this->boolean(),
//                'number' => $this->string(),
//                'date' => $this->dateTime(),
//                'taxStatus' => $this->enum('status', ['Gross', 'NetOfStandardDeduction', 'NotOfHigherDeduction']),
//                'verificationRequest' => $this->string(),
//            ]);

            $this->createTable(Table::COUNTRIES, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //fields
                'name' => $this->string()->notNull(), //index
                'iso' => $this->string(3)->notNull(),
                'sortOrder' => $this->integer(),
            ]);

            $this->createTable(Table::CUSTOM_PAY_CODES, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employerId' => $this->integer(), //create FK to Employer [id]
                'payRunEntryId' => $this->integer(), //create FK to PayRunEntry [id]
                //fields
                'title' => $this->string()->notNull(),
                'code' => $this->string()->notNull(),
                'defaultValue' => $this->string(),
                'isDeduction' => $this->boolean(),
                'isNiable' => $this->boolean(),
                'isTaxable' => $this->boolean(),
                'isPensionable' => $this->boolean(),
                'isAttachable' => $this->boolean(),
                'isRealTimeClass1aNiable' => $this->boolean(),
                'isNotContributingToHolidayPay' => $this->boolean(),
                'isQualifyingEarningsForAe' => $this->boolean(),
                'isNotTierable' => $this->boolean(),
                'isTcp_Tcls' => $this->boolean(),
                'isTcp_Pp' => $this->boolean(),
                'isTcp_Op' => $this->boolean(),
                'isFlexiDd_DeathBenefit' => $this->boolean(),
                'isFlexiDd_Pension' => $this->boolean(),
                'calculationType' => $this->enum('calculationType', ['FixedAmount', 'PercentageOfGross', 'PercentageOfNet', 'MultipleOfHourlyRate']),
                'multiplierType' => $this->enum('multiplierType', ['None', 'Hours', 'Days']),
                'hourlyRateMultiplier' => $this->double(),
                'isSystemCode' => $this->boolean(),
                'isControlCode' => $this->boolean(),
            ]);

            $this->createTable(Table::DEPARTMENT, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employmentDetailsId' => $this->integer(), // Create FK to EmploymentDetails [id]
                //fields
                'code' => $this->string(),
                'title' => $this->string(),
                // @DISCUSS - needed? Could use better things
                'color' => $this->string(),
                // @DISCUSS - needed? Can query this ourselves
                'employeeCount' => $this->integer(),
            ]);

            $this->createTable(Table::DIRECTORSHIP_DETAILS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employmentDetailsId' => $this->integer(), // Create FK to EmploymentDetails [id]
                //fields
                'isDirector' => $this->boolean(),
                'startDate' => $this->dateTime(),
                'leaveDate' => $this->dateTime(),
                'niAlternativeMethod' => $this->boolean(),
            ]);

            $this->createTable(Table::EMPLOYER_SETTINGS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employerId' => $this->integer()->notNull(), // create FK to Employer table [id]
                //fields
                'allowNegativePay' => $this->boolean(),
            ]);

            $this->createTable(Table::EMPLOYMENT_DETAILS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                'employeeId' => $this->integer(), // create FK to Employee [id]
                //fields
                'cisSubContractor' => $this->boolean(),
                'payrollCode' => $this->string(),
                'jobTitle' => $this->string(),
                'onHold' => $this->boolean(),
                'onFurlough' => $this->boolean(),
                'furloughStart' => $this->dateTime(),
                'furloughEnd' => $this->dateTime(),
                'furloughCalculationBasis' => $this->enum('furloughCalculationBasis', ['ActualPaidAmount', 'DailyReferenceAmount', 'MonthlyReferenceAmount']),
                'furloughCalculationBasisAmount' => $this->string(),
                'partialFurlough' => $this->boolean(),
                'furloughHoursNormallyWorked' => $this->double(),
                'furloughHoursOnFurlough' => $this->double(),
                'isApprentice' => $this->boolean(),
                'apprenticeshipStartDate' => $this->dateTime(),
                'apprenticeshipEndDate' => $this->dateTime(),
                'workingPattern' => $this->string(),
                'forcePreviousPayrollCode' => $this->string(),
                //            'posts' => $this->integer(), included in the ItemRelations with this id
            ]);

//            // Harding Hub table
//            $this->createTable(Table::FAMILY_DETAILS, [
//                'id' => $this->primaryKey(),
//                'dateCreated' => $this->dateTime()->notNull(),
//                'dateUpdated' => $this->dateTime()->notNull(),
//                'uid' => $this->uid(),
//                //FK
//                //intern
//                'employeeId' => $this->integer()->notNull(), // create FK to Employee [id]
//                //fields
//                'title' => $this->string(255),
//                'firstName' => $this->string(255)->notNull(),
//                'middleName' => $this->string(255),
//                'lastName' => $this->string(255)->notNull(),
//                'email' => $this->string(255),
//                'telephone' => $this->string(255),
//                'mobile' => $this->string(255),
//                'gender' => $this->enum('gender', ['Male', 'Female']),
//                'niNumber' => $this->string(255),
//                'passportNumber' => $this->string(255),
//                'relationshipStatus' => $this->enum('relationshipStatus', ['Cohabitate', 'Partner', 'Spouse', 'Child', 'Sibling', 'Parent', 'Other']),
//            ]);

            $this->createTable(Table::FPS_FIELDS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'payRunEntryId' => $this->integer(), //Create FK to PayRunEntries [id]
                //fields
                'offPayrollWorker' => $this->boolean(),
                'irregularPaymentPattern' => $this->boolean(),
                'nonIndividual' => $this->boolean(),
                'hoursNormallyWorked' => $this->enum('hours', ['LessThan16', 'MoreThan16', 'MoreThan24', 'MoreThan30', 'NotRegular']),
            ]);

            $this->createTable(Table::HMRC_DETAILS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employerId' => $this->integer()->notNull(), // create FK to employer [id]
                //fields
                'officeNumber' => $this->string(),
                'payeReference' => $this->string(),
                'accountsOfficeReference' => $this->string(),
                'econ' => $this->string(),
                'utr' => $this->string(),
                'coTax' => $this->string(),
                'employmentAllowance' => $this->boolean(),
                'employmentAllowanceMaxClaim' => $this->string(),
                'smallEmployersRelief' => $this->boolean(),
                'apprenticeshipLevy' => $this->boolean(),
                'apprenticeshipLevyAllowance' => $this->string(),
                'quarterlyPaymentSchedule' => $this->boolean(),
                'includeEmploymentAllowanceOnMonthlyJournal' => $this->boolean(),
                'carryForwardUnpaidLiabilities' => $this->boolean(),
            ]);

            $this->createTable(Table::ITEMS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'autoEnrolmentAssessmentId' => $this->integer(), //create FK AutoEnrolmentAssesment [id]
                //fields
                "staffologyId" => $this->string(),
                'name' => $this->string(),
                'metadata' => $this->longText(),
                'url' => $this->string(),
            ]);

            $this->createTable(Table::LEAVE_SETTINGS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employeeId' => $this->integer(), //create FK to Employee [id] //can be null, since leave settings are also for employer
                'employerId' => $this->integer(), //create FK to Employer [id] //can be null, since leave settings are also for employee
                //fields
                'useDefaultHolidayType' => $this->boolean(),
                'useDefaultAllowanceResetDate' => $this->boolean(),
                'useDefaultAllowance' => $this->boolean(),
                'useDefaultAccruePaymentInLieu' => $this->boolean(),
                'useDefaultAccruePaymentInLieuRate' => $this->boolean(),
                'useDefaultAccruePaymentInLieuAllGrossPay' => $this->boolean(),
                'useDefaultAccruePaymentInLieuPayAutomatically' => $this->boolean(),
                'useDefaultAccrueHoursPerDay' => $this->boolean(),
                'allowanceResetDate' => $this->dateTime(),
                'allowance' => $this->double(),
                'adjustment' => $this->double(),
                'allowanceUsed' => $this->double(),
                'allowanceUsedPreviousPeriod' => $this->double(),
                'allowanceRemaining' => $this->double(),
                'holidayType' => $this->enum('type', ['Days', 'Accrual_Money', 'Accrual_Days']),
                'accrueSetAmount' => $this->boolean(),
                'accrueHoursPerDay' => $this->double(),
                'showAllowanceOnPayslip' => $this->boolean(),
                'showAhpOnPayslip' => $this->boolean(),
                'accruePaymentInLieuRate' => $this->double(),
                'accruePaymentInLieuAllGrossPay' => $this->boolean(),
                'accruePaymentInLieuPayAutomatically' => $this->boolean(),
                'accruedPaymentLiability' => $this->double(),
                'accruedPaymentAdjustment' => $this->double(),
                'accruedPaymentPaid' => $this->double(),
                'accruedPaymentBalance' => $this->double(),
            ]);

            $this->createTable(Table::LEAVER_DETAILS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employmentDetailsId' => $this->integer(), //create FK in EmploymentDetails [id]
                //fields
                'hasLeft' => $this->boolean(),
                'leaveDate' => $this->dateTime(),
                'isDeceased' => $this->boolean(),
                'paymentAfterLeaving' => $this->boolean(),
                'p45Sent' => $this->boolean(),
            ]);

            $this->createTable(Table::NATIONAL_INSURANCE_CALCULATION, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'payRunEntryId' => $this->integer(), //create FK to PayRunEntry
                //fields
                'earningsUptoIncludingLEL' => $this->double(),
                'earningsAboveLELUptoIncludingPT' => $this->double(),
                'earningsAbovePTUptoIncludingST' => $this->double(),
                'earningsAbovePTUptoIncludingUEL' => $this->double(),
                'earningsAboveSTUptoIncludingUEL' => $this->double(),
                'earningsAboveUEL' => $this->double(),
                'employeeNiGross' => $this->double(),
                'employeeNiRebate' => $this->double(),
                'employerNiGross' => $this->double(),
                'employerNiRebate' => $this->double(),
                'employeeNi' => $this->double(),
                'employerNi' => $this->double(),
                'netNi' => $this->double(),
            ]);

//            $this->createTable(Table::NOTE, [
//                'id' => $this->primaryKey(),
//                'dateCreated' => $this->dateTime()->notNull(),
//                'dateUpdated' => $this->dateTime()->notNull(),
//                'uid' => $this->uid(),
//                //FK
//                //intern
//                'employeeId' => $this->integer(), // Create FK to Items [id]
//                //fields
//                'noteDate' => $this->string(),
//                'noteText' => $this->string(),
//                'createdBy' => $this->string(),
//                'updatedBy' => $this->string(),
//                'type' => $this->enum('type', ['General', 'RtwProof', 'P45']),
//                'documentCount' => $this->integer(),
//                //            'documents' => $this->integer(), // linked in ItemRelations table with noteId
//            ]);
//
//
//            $this->createTable(Table::OVERSEAS_EMPLOYER_DETAILS, [
//                'id' => $this->primaryKey(),
//                'dateCreated' => $this->dateTime()->notNull(),
//                'dateUpdated' => $this->dateTime()->notNull(),
//                'uid' => $this->uid(),
//                //FK
//                //fields
//                'overseasEmployer' => $this->boolean(),
//                'overseasSecondmentStatus' => $this->enum('status', ['MoreThan183Days', 'LessThan183Days', 'BothInAndOutOfUK']),
//                'eeaCitizen' => $this->boolean(),
//                'epm6Scheme' => $this->boolean(),
//            ]);
//
//            $this->createTable(Table::PAY_CODES, [
//                'id' => $this->primaryKey(),
//                'dateCreated' => $this->dateTime()->notNull(),
//                'dateUpdated' => $this->dateTime()->notNull(),
//                'uid' => $this->uid(),
//                //FK
//                'employerId' => $this->integer(), // Create FK to Employer [id]
//                //fields
//                'title' => $this->string()->notNull(),
//                'code' => $this->string()->notNull(),
//                'defaultValue' => $this->double(),
//                'isDeduction' => $this->boolean(),
//                'isNiable' => $this->boolean(),
//                'isTaxable' => $this->boolean(),
//                'isPensionable' => $this->boolean(),
//                'isAttachable' => $this->boolean(),
//                'isRealTimeClass1aNiable' => $this->boolean(),
//                'isNotContributingToHolidayPay' => $this->boolean(),
//                'isQualifyingEarningsForAe' => $this->boolean(),
//                'isNotTierable' => $this->boolean(),
//                'isTcp_Tcls' => $this->boolean(),
//                'isTcp_Pp' => $this->boolean(),
//                'isTcp_Op' => $this->boolean(),
//                'isFlexiDd_DeathBenefit' => $this->boolean(),
//                'isFlexiDd_Pension' => $this->boolean(),
//                'calculationType' => $this->enum('type', ['FixedAmount', 'PercentageOfGross', 'PercentageOfNet', 'MultipleOfHourlyRate']),
//                'hourlyRateMultiplier' => $this->double(),
//                'isSystemCode' => $this->boolean(),
//                'isControlCode' => $this->boolean(),
//            ]);
//
//            $this->createTable(Table::PAY_LINES, [
//                'id' => $this->primaryKey(),
//                'dateCreated' => $this->dateTime()->notNull(),
//                'dateUpdated' => $this->dateTime()->notNull(),
//                'uid' => $this->uid(),
//                //FK
//                //intern
//                'payOptionsId' => $this->integer(), //create FK to PayOptions [id]
//                //fields
//                'value' => $this->string(),
//                'rate' => $this->string(),
//                'multiplier' => $this->string(),
//                'description' => $this->string(),
//                'attachmentOrderId' => $this->string(),
//                'pensionId' => $this->string(),
//                'code' => $this->string(),
//            ]);

            $this->createTable(Table::PAY_OPTIONS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employerId' => $this->integer(), // create FK to Employers [id]
                'payRunEntryId' => $this->integer(), // create FK to PayRunEntries [id]
                //staffology
//                'taxAndNiId' => $this->integer(), // create FK to TaxAndNi [id]
//                'fpsFieldsId' => $this->integer(), // create FK to fpsFields [id]
                //fields
                'period' => $this->enum('period', ['Custom', 'Monthly', 'FourWeekly', 'Fortnightly', 'Weekly', 'Daily']),
                'ordinal' => $this->integer(),
                'payAmount' => $this->string(),
                'basis' => $this->enum('basis', ['Hourly', 'Daily', 'Monthly']),
                'nationalMinimumWage' => $this->boolean(),
                'payAmountMultiplier' => $this->string(),
                'baseHourlyRate' => $this->string(),
                'autoAdjustForLeave' => $this->boolean(),
                'method' => $this->enum('method', ['Cash', 'Cheque', 'Credit', 'DirectDebit']),
                'payCode' => $this->string(),
                'withholdTaxRefundIfPayIsZero' => $this->boolean(),
                'mileageVehicleType' => $this->enum('type', ['Car', 'Motorcycle', 'Cycle']),
                'mapsMiles' => $this->integer(),
            ]);

            $this->createTable(Table::PAYRUN_TOTALS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                // FK
                //internal
                'payRunId' => $this->integer(), // Create FK to PayRun [id]
                'payRunEntryId' => $this->integer(), // Create FK to PayRunEntries [id]
                // fields
                'basicPay' => $this->string(),
                'gross' => $this->string(),
                'grossForNi' => $this->string(),
                'grossNotSubjectToEmployersNi' => $this->string(),
                'grossForTax' => $this->string(),
                'employerNi' => $this->string(),
                'employeeNi' => $this->string(),
                'employerNiOffPayroll' => $this->double(),
                'realTimeClass1ANi' => $this->double(),
                'tax' => $this->string(),
                'netPay' => $this->string(),
                'adjustments' => $this->string(),
                'additions' => $this->string(),
                'deductions' => $this->string(),
                'takeHomePay' => $this->string(),
                'nonTaxOrNICPmt' => $this->string(),
                'itemsSubjectToClass1NIC' => $this->double(),
                'dednsFromNetPay' => $this->double(),
                'tcp_Tcls' => $this->double(),
                'tcp_Pp' => $this->double(),
                'tcp_Op' => $this->double(),
                'flexiDd_Death' => $this->double(),
                'flexiDd_Death_NonTax' => $this->double(),
                'flexiDd_Pension' => $this->double(),
                'flexiDd_Pension_NonTax' => $this->double(),
                'smp' => $this->double(),
                'spp' => $this->double(),
                'sap' => $this->double(),
                'shpp' => $this->double(),
                'spbp' => $this->double(),
                'ssp' => $this->double(),
                'studentLoanRecovered' => $this->string(),
                'postgradLoanRecovered' => $this->string(),
                'pensionableEarnings' => $this->string(),
                'pensionablePay' => $this->string(),
                'nonTierablePay' => $this->string(),
                'employeePensionContribution' => $this->string(),
                'employeePensionContributionAvc' => $this->string(),
                'employerPensionContribution' => $this->string(),
                'empeePenContribnsNotPaid' => $this->string(),
                'empeePenContribnsPaid' => $this->string(),
                'attachmentOrderDeductions' => $this->string(),
                'cisDeduction' => $this->string(),
                'cisVat' => $this->string(),
                'cisUmbrellaFee' => $this->string(),
                'cisUmbrellaFeePostTax' => $this->string(),
                'pbik' => $this->double(),
                'mapsMiles' => $this->integer(),
                'umbrellaFee' => $this->string(),
                'appLevyDeduction' => $this->double(),
                'paymentAfterLeaving' => $this->double(),
                'taxOnPaymentAfterLeaving' => $this->double(),
                'nilPaid' => $this->integer(),
                'leavers' => $this->integer(),
                'starters' => $this->integer(),
                'totalCost' => $this->string(),
                'isYtd' => $this->boolean(),
            ]);

//            $this->createTable(Table::PENSION_ADMINISTRATOR, [
//                'id' => $this->primaryKey(),
//                'dateCreated' => $this->dateTime()->notNull(),
//                'dateUpdated' => $this->dateTime()->notNull(),
//                'uid' => $this->uid(),
//                //FK
//                //staffology
//                'addressId' => $this->integer(), //create FK to Address [id]
//                //fields
//                "staffologyId" => $this->string(),
//                'name' => $this->string(),
//                'email' => $this->string(),
//                'telephone' => $this->string(),
//            ]);
//
//            $this->createTable(Table::PENSION_PROVIDER, [
//                'id' => $this->primaryKey(),
//                'dateCreated' => $this->dateTime()->notNull(),
//                'dateUpdated' => $this->dateTime()->notNull(),
//                'uid' => $this->uid(),
//                //FK
//                //staffology
//                'addressId' => $this->integer(), // create FK to Address [id]
//                //fields
//                "staffologyId" => $this->string(),
//                'name' => $this->string()->notNull(),
//                'accountNo' => $this->string(),
//                'portal' => $this->string(),
//                'website' => $this->string(),
//                'telephone' => $this->string(),
//                'papdisVersion' => $this->enum('papdisVersion', ['PAP10', 'PAP11']),
//                'papdisProviderId' => $this->string(),
//                'papdisEmployerId' => $this->string(),
//                'csvFormat' => $this->enum('csvFormat', ['Papdis', 'Nest', 'NowPensions', 'TeachersPensionMdc', 'TeachersPensionMcr']),
//                'excludeNilPaidFromContributions' => $this->boolean(),
//                'payPeriodDateAdjustment' => $this->integer(),
//                'miscBoolean1' => $this->boolean(),
//                'miscBoolean2' => $this->boolean(),
//                'miscString1' => $this->string(),
//                'miscString2' => $this->string(),
//                'optOutWindow' => $this->integer(),
//                'optOutWindowIsMonths' => $this->boolean(),
//            ]);

            $this->createTable(Table::PENSION_SCHEME, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
//                'employerId' => $this->integer(), // create FK to Employer [id]
//                'pensionId' => $this->integer(), // create FK to Pension [id]
                //staffology
//                'providerId' => $this->integer(), // create FK to PensionProvider [id]
//                'administratorId' => $this->integer(), // create FK to PensionAdministrator [id]
//                'bankDetailsId' => $this->integer(), // create FK to BankDetailsId [id]
                //            'customPayCodes' => $this->integer(),  // Added an internal relation table CustomPayCodes to store this [id] into
                //fields
                "staffologyId" => $this->string(),
                'name' => $this->string()->notNull(),
                'pensionRule' => $this->enum('period', ['ReliefAtSource', 'SalarySacrifice', 'NetPayArrangement']),
                'qualifyingScheme' => $this->boolean(),
                'disableAeLetters' => $this->boolean(),
                'subtractBasicRateTax' => $this->boolean(),
                'payMethod' => $this->enum('period', ['Cash', 'Cheque', 'Credit', 'DirectDebit']),
                'useCustomPayCodes' => $this->boolean(),
            ]);

            $this->createTable(Table::PENSION_SELECTION, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'autoEnrollmentSettingsId' => $this->integer(), // create FK to AutoEnrollmentSettings [id]
                'employerId' => $this->integer(), // create FK to Employer [id]
                //staffology
                'pensionSchemeId' => $this->integer(), // create FK to PensionSchema [id] //refactored from Staffology to link
                'workerGroupId' => $this->string(), // create FK to WorkerGroup [id]
                //fields
                "staffologyId" => $this->integer()
            ]);

            $this->createTable(Table::PENSION_SUMMARY, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'payRunEntryId' => $this->integer(), // create FK to PensionSummary [id]
                //staffology
//                'pensionId' => $this->integer(), // create FK to Pension [id]
//                'pensionSchemeId' => $this->integer(), // create FK to PensionSchema [id]
//                'workerGroupId' => $this->integer(), // create FK to WorkerGroup [id]
                //fields
                'name' => $this->string(),
                'startDate' => $this->string(),
                'pensionRule' => $this->enum('pensionRule', ['ReliefAtSource', 'SalarySacrifice', 'NetPayArrangement'])->notNull(),
                'employeePensionContributionMultiplier' => $this->double(),
                'additionalVoluntaryContribution' => $this->double(),
                'avcIsPercentage' => $this->boolean(),
                'autoEnrolled' => $this->boolean(),
                'papdisPensionProviderId' => $this->integer(),
                'papdisEmployerId' => $this->integer()
            ]);

//            $this->createTable(Table::PENSIONER_PAYROLL, [
//                'id' => $this->primaryKey(),
//                'dateCreated' => $this->dateTime()->notNull(),
//                'dateUpdated' => $this->dateTime()->notNull(),
//                'uid' => $this->uid(),
//                //fields
//                'inReceiptOfPension' => $this->boolean(),
//                'bereaved' => $this->boolean(),
//                'amount' => $this->double(),
//            ]);

            $this->createTable(Table::PERSONAL_DETAILS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employeesId' => $this->integer(), // create FK to Employees [id]
                //fields
                'maritalStatus' => $this->enum('maritalStatus', ['Single', 'Married', 'Divorced', 'Widowed', 'CivilPartnership', 'Unknown'])->notNull(),
                'title' => $this->string(255),
                'firstName' => $this->string(255)->notNull(),
                'middleName' => $this->string(255),
                'lastName' => $this->string(255)->notNull(),
                'email' => $this->string(255)->notNull(),
                'emailPayslip' => $this->boolean()->notNull(),
                'passwordProtectPayslip' => $this->boolean()->notNull(),
                'pdfPassword' => $this->string(255),
                'telephone' => $this->string(255),
                'mobile' => $this->string(255),
                'dateOfBirth' => $this->dateTime()->notNull(),
                'statePensionAge' => $this->integer()->notNull(),
                'gender' => $this->enum('gender', ['Male', 'Female'])->notNull(),
                'niNumber' => $this->string(255)->notNull(),
                'passportNumber' => $this->string(255)->notNull(),
            ]);

            $this->createTable(Table::RIGHT_TO_WORK, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                'employeeId' => $this->integer(), //create FK to Employee table [id]
                //fields
                'checked' => $this->boolean(),
                'documentType' => $this->enum('type', ['Other', 'Visa', 'Passport', 'BirthCertificate', 'IdentityCard', 'Sharecode']),
                'documentRef' => $this->string(),
                'documentExpiry' => $this->dateTime(),
                'note' => $this->mediumText(),
            ]);

//            $this->createTable(Table::RTI_AGENT, [
//                'id' => $this->primaryKey(),
//                'dateCreated' => $this->dateTime()->notNull(),
//                'dateUpdated' => $this->dateTime()->notNull(),
//                'uid' => $this->uid(),
//                //FK
//                //staffology
//                'addressId' => $this->integer(), // create FK to Address table [id]
//                //fields
//                'agentId' => $this->string(),
//                'company' => $this->string(),
//                'email' => $this->string(),
//                'telephone' => $this->string(),
//            ]);
//
//            $this->createTable(Table::RTI_CONTACT, [
//                'id' => $this->primaryKey(),
//                'dateCreated' => $this->dateTime()->notNull(),
//                'dateUpdated' => $this->dateTime()->notNull(),
//                'uid' => $this->uid(),
//                //fields
//                'firstName' => $this->string(),
//                'lastName' => $this->string(),
//                'email' => $this->string(),
//                'telephone' => $this->string(),
//            ]);
//
//            $this->createTable(Table::RTI_EMPLOYEE_ADDRESS, [
//                'id' => $this->primaryKey(),
//                'dateCreated' => $this->dateTime()->notNull(),
//                'dateUpdated' => $this->dateTime()->notNull(),
//                'uid' => $this->uid(),
//                //FK
//                //intern
//                'employeeId' => $this->integer(), // create FK to Employee [id]
//                'addressId' => $this->integer(), // create FK to Address [id]
//                //fields
            ////                'line' => $this->longText(),
//                'postcode_v1' => $this->string(), //staffology api call --> postcode
//                'postcode_v2' => $this->string(), //staffology api call --> postCode
//                'ukPostcode' => $this->string(),
            ////                'country' => $this->string(),
//            ]);
//
//            $this->createTable(Table::RTI_EMPLOYEE_NAME, [
//                'id' => $this->primaryKey(),
//                'dateCreated' => $this->dateTime()->notNull(),
//                'dateUpdated' => $this->dateTime()->notNull(),
//                'uid' => $this->uid(),
//                //fields
//                'ttl' => $this->string(),
//                'fore' => $this->longText(),
//                'initials' => $this->string(),
//                'sur' => $this->string(),
//            ]);

            $this->createTable(Table::RTI_SUBMISSION_SETTINGS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employerId' => $this->integer(), // create FK to Employer [id]
                //extern
                'contactId' => $this->integer(), // create FK to RtiContact [id]
                'agentId' => $this->integer(), // create FK to RtiAgent [id]
                //fields
                'senderType' => $this->enum('declaration', ['ActingInCapacity', 'Agent', 'Bureau', 'Company', 'Employer', 'Government', 'Individual', 'Other', 'Partnership', 'Trust']),
                'senderId' => $this->string(),
                'password' => $this->string(),
                'excludeNilPaid' => $this->boolean(),
                'includeHashCrossRef' => $this->boolean(),
                'autoSubmitFps' => $this->boolean(),
                'testInLive' => $this->boolean(),
                'useTestGateway' => $this->boolean(),
                'overrideTimestampValue' => $this->string(),
            ]);

            $this->createTable(Table::STARTER_DETAILS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employmentDetailsId' => $this->integer(), // create FK to employmentDetails [id]
//                'overseasEmployerDetailsId' => $this->integer(), // create FK to OverseasEmployerDetails [id]
//                'pensionerPayrollId' => $this->integer(), // create FK to pensionerPayrollId [id]
                //fields
                'startDate' => $this->dateTime()->notNull(),
                'starterDeclaration' => $this->enum('declaration', ['A', 'B', 'C', 'Unknown'])->notNull(),
            ]);

//            $this->createTable(Table::TAX_AND_NI, [
//                'id' => $this->primaryKey(),
//                'dateCreated' => $this->dateTime()->notNull(),
//                'dateUpdated' => $this->dateTime()->notNull(),
//                'uid' => $this->uid(),
//                //fields
//                'niTable' => $this->string()->notNull(),
//                'secondaryClass1NotPayable' => $this->boolean(),
//                'postgradLoan' => $this->boolean(),
//                'postgraduateLoanStartDate' => $this->dateTime(),
//                'postgraduateLoanEndDate' => $this->dateTime(),
//                'studentLoan' => $this->boolean(),
//                'studentLoanStartDate' => $this->dateTime(),
//                'studentLoanEndDate' => $this->dateTime(),
//                'taxCode' => $this->string(),
//                'week1Month1' => $this->boolean(),
//            ]);

            $this->createTable(Table::TEACHER_PENSION_DETAILS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'pensionId' => $this->integer(), //create FK to Pensions [id]
                //fields
                'employmentType' => $this->enum('type', ['FullTime', 'PartTimeRegular', 'IrregularPartTime', 'IrregularPartTime_In']),
                'fullTimeSalary' => $this->integer(),
                'partTimeSalaryPaid' => $this->integer(),
            ]);

            $this->createTable(Table::TIER_PENSION_RATE, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'pensionId' => $this->integer(), //create FK to Pensions [id]
                //fields
                'name' => $this->string(),
                'description' => $this->string(),
                'rangeStart' => $this->double(),
                'rate' => $this->double(),
            ]);

            $this->createTable(Table::UMBRELLA_PAYMENT, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'payRunEntryId' => $this->integer(), //create FK to PayRunEntry [id]
                // fields
                'payrollCode' => $this->string(),
                'chargePerTimesheet' => $this->double(),
                'invoiceValue' => $this->double(),
                'mapsMiles' => $this->integer(),
                'otherExpenses' => $this->double(),
                'numberOfTimesheets' => $this->integer(),
                'hoursWorked' => $this->double(),
                'grossDeduction' => $this->double(),
                'grossAddition' => $this->double(),
            ]);

            $this->createTable(Table::UMBRELLA_SETTINGS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employerId' => $this->integer(), // create FK to Employer [id]
                // fields
                'enabled' => $this->boolean(),
                'chargePerTimesheet' => $this->double(),
                'apprenticeshipLevyDednRate' => $this->double(),
                'holidayRate' => $this->double(),
                'dpsbCode' => $this->string(),
                'expensesCode' => $this->string(),
                'grossDeductionCode' => $this->string(),
                'holidayCode' => $this->string(),
                'cisFeeCode' => $this->string(),
                'detailFeeInComment' => $this->boolean(),
            ]);

//            //        @DISCUSS: do we need this? these are the staffology users, we don't link up to this
//            //        $this->createTable(Table::USERS, [
//            //            'id' => $this->primaryKey(),
//            //            'dateCreated' => $this->dateTime()->notNull(),
//            //            'dateUpdated' => $this->dateTime()->notNull(),
//            //            'uid' => $this->uid(),
//            //            //FK
//            //            'metadata' => $this->longText()->notNull(), // create own table
//            //            // fields
//            //            "staffologyId" => $this->string(255)->notNull(),
//            //        ]);
//
//            $this->createTable(Table::VALUE_OVERRIDE, [
//                'id' => $this->primaryKey(),
//                'dateCreated' => $this->dateTime()->notNull(),
//                'dateUpdated' => $this->dateTime()->notNull(),
//                'uid' => $this->uid(),
//                //FK
//                //intern
//                'payRunEntryId' => $this->integer(), // create FK to PayRunEntry table (id)
//                // fields
//                'type' => $this->enum('type', ['BasicPay', 'Gross', 'GrossForTax', 'GrossForNi', 'EmployerNi', 'EmployeeNi', 'EmployerNiOffPayroll', 'RealTimeClass1ANi', 'Tax', 'NetPay', 'Adjustments', 'TakeHomePay', 'NonTaxOrNICPmt', 'ItemsSubjectToClass1NIC', 'DednsFromNetPay', 'Tcp_Tcls', 'Tcp_Pp', 'Tcp_Op', 'FlexiDd_Death', 'FlexiDd_Death_NonTax', 'FlexiDd_Pension', 'FlexiDd_Pension_NonTax', 'Smp', 'Spp', 'Sap', 'Shpp', 'Spbp', 'StudentLoanRecovered', 'PostgradLoanRecovered', 'PensionablePay', 'NonTierablePay', 'EmployeePensionContribution', 'EmployerPensionContribution', 'EmpeePenContribnsNotPaid', 'EmpeePenContribnsPaid', 'AttachmentOrderDeductions', 'CisDeduction', 'CisVat', 'CisUmbrellaFee', 'CisUmbrellaFeePostTax', 'Pbik', 'MapsMiles', 'UmbrellaFee', 'AppLevyDeduction', 'PaymentAfterLeaving', 'TaxOnPaymentAfterLeaving', 'Ssp', 'AttachmentOrderAdminFee', 'EmployeePensionNetPay', 'EmployeePensionRas', 'EmployeePensionSalSac', 'EmployeePensionContributionAvc', 'Deductions', 'Additions', 'PensionableEarnings']),
//                'value' => $this->double(),
//                'originalValue' => $this->double(),
//                'note' => $this->mediumText(),
//                'attachmentOrderId' => $this->string(),
//                // custom
//                'field' => $this->enum('type', ['periodOverrides', 'totalsYtdOverrides']),
//            ]);

            $this->createTable(Table::WORKER_GROUP, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'pensionId' => $this->integer(), // create FK to Pensions [id]
                'pensionSchemeId' => $this->integer(), // create FK to PensionScheme [id]
                // fields
                "staffologyId" => $this->string(), // staffology: id
                'name' => $this->string()->notNull(),
                'contributionLevelType' => $this->enum('type', ['UserDefined', 'StatutoryMinimum', 'Nhs2015', 'Tp2020']),
                'employeeContribution' => $this->double(),
                'employeeContributionIsPercentage' => $this->boolean(),
                'employerContribution' => $this->double(),
                'employerContributionIsPercentage' => $this->boolean(),
                'employerContributionTopUpPercentage' => $this->double(),
                'customThreshold' => $this->boolean(),
                'lowerLimit' => $this->double(),
                'upperLimit' => $this->double(),
                'papdisGroup' => $this->string(),
                'papdisSubGroup' => $this->string(),
                'localAuthorityNumber' => $this->string(),
                'schoolEmployerType' => $this->string(),
                'workerGroupId' => $this->string(),
            ]);

            $tableCreated = true;
        }


        return $tableCreated;
    }


    /**
     * Creates the indexes
     */
    public function createIndexes(): void
    {
        /** LEVEL 1 TABLES **/

        /** LEVEL 1+ TABLES **/
        $this->createIndex(null, Table::ADDRESSES, 'countryId', false);

        $this->createIndex(null, Table::ADDRESSES, "employerId", true);
        $this->createIndex(null, Table::AUTO_ENROLMENT_SETTINGS, "employerId", true);
        $this->createIndex(null, Table::BANK_DETAILS, "employerId", true);
        $this->createIndex(null, Table::EMPLOYER_SETTINGS, "employerId", true);
        $this->createIndex(null, Table::HMRC_DETAILS, "employerId", true);
        $this->createIndex(null, Table::LEAVE_SETTINGS, "employerId", true);

        $this->createIndex(null, Table::PAYRUN_TOTALS, "payRunId", false);
        $this->createIndex(null, Table::PAYRUN_TOTALS, "payRunEntryId", false);
    }

    /**
     * Creates the foreign keys needed for the Records used by the plugin
     *
     * @return void
     */
    protected function addForeignKeys()
    {
        // BASE
        // Employees [id]
        $this->addForeignKey(null, Table::AUTO_ENROLMENT, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::ADDRESSES, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::BANK_DETAILS, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::HISTORY, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::LEAVE_SETTINGS, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::PAYRUN_ENTRIES, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::PENSIONS, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::PERMISSIONS_USERS, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::PERSONAL_DETAILS, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::REQUESTS, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::RIGHT_TO_WORK, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');

        //Employer [id]
        $this->addForeignKey(null, Table::ADDRESSES, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::AUTO_ENROLMENT_SETTINGS, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::BANK_DETAILS, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::CUSTOM_PAY_CODES, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::EMPLOYER_SETTINGS, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::HISTORY, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::HMRC_DETAILS, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::LEAVE_SETTINGS, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::PAYRUN, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::PAYRUN_ENTRIES, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::PAYRUN_LOG, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::PENSION_SELECTION, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::REQUESTS, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');

        //Employment [id]
        $this->addForeignKey(null, Table::CIS_DETAILS, ['employmentDetailsId'], Table::EMPLOYMENT_DETAILS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::DEPARTMENT, ['employmentDetailsId'], Table::EMPLOYMENT_DETAILS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::DIRECTORSHIP_DETAILS, ['employmentDetailsId'], Table::EMPLOYMENT_DETAILS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::LEAVER_DETAILS, ['employmentDetailsId'], Table::EMPLOYMENT_DETAILS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::STARTER_DETAILS, ['employmentDetailsId'], Table::EMPLOYMENT_DETAILS, ['id'], 'CASCADE', 'CASCADE');

        //Country [id]
        $this->addForeignKey(null, Table::ADDRESSES, ['countryId'], Table::COUNTRIES, ['id']);

        //Pay Run [id]
        $this->addForeignKey(null, Table::PAYRUN_TOTALS, ['payRunId'], Table::PAYRUN, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::PAYRUN_ENTRIES, ['payRunId'], Table::PAYRUN, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::PAYRUN_IMPORTS, ['payRunId'], Table::PAYRUN, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::PAYRUN_LOG, ['payRunId'], Table::PAYRUN, ['id'], 'CASCADE', 'CASCADE');

        //Pay Run Entries [id]
        $this->addForeignKey(null, Table::CUSTOM_PAY_CODES, ['payRunEntryId'], Table::PAYRUN_ENTRIES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::FPS_FIELDS, ['payRunEntryId'], Table::PAYRUN_ENTRIES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::PAY_OPTIONS, ['payRunEntryId'], Table::PAYRUN_ENTRIES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::NATIONAL_INSURANCE_CALCULATION, ['payRunEntryId'], Table::PAYRUN_ENTRIES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::PAYRUN_TOTALS, ['payRunEntryId'], Table::PAYRUN_ENTRIES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::PENSION_SUMMARY, ['payRunEntryId'], Table::PAYRUN_ENTRIES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::UMBRELLA_PAYMENT, ['payRunEntryId'], Table::PAYRUN_ENTRIES, ['id'], 'CASCADE', 'CASCADE');

        //Pensions [id]
        $this->addForeignKey(null, Table::PENSIONS, ['pensionId'], Table::PENSIONS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::TEACHER_PENSION_DETAILS, ['pensionId'], Table::PENSIONS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::TIER_PENSION_RATE, ['pensionId'], Table::PENSIONS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::WORKER_GROUP, ['pensionId'], Table::PENSIONS, ['id'], 'CASCADE', 'CASCADE');

        //Permissions [id]
        $this->addForeignKey(null, Table::PERMISSIONS_USERS, ['permissionId'], Table::PERMISSIONS, ['id'], 'CASCADE', 'CASCADE');

        // LINKAGE
        //Auto Enrolment [id]
        $this->addForeignKey(null, Table::AUTO_ENROLMENT_ASSESSMENT, ['autoEnrolmentId'], Table::AUTO_ENROLMENT, ['id'], 'CASCADE', 'CASCADE');

        //Auto Enrolment Assessment [id]
        $this->addForeignKey(null, Table::ITEMS, ['autoEnrolmentAssessmentId'], Table::AUTO_ENROLMENT_ASSESSMENT, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::AUTO_ENROLMENT_ASSESSMENT_ACTION, ['autoEnrolmentAssessmentId'], Table::AUTO_ENROLMENT_ASSESSMENT, ['id'], 'CASCADE', 'CASCADE');

        //Auto Enrolment Settings
        $this->addForeignKey(null, Table::PENSION_SELECTION, ['autoEnrollmentSettingsId'], Table::AUTO_ENROLMENT_SETTINGS, ['id'], 'CASCADE', 'CASCADE');

        // CRAFT
        // User [id]
        $this->addForeignKey(null, Table::EMPLOYEES, ['userId'], \craft\db\Table::USERS, ['id']);
        $this->addForeignKey(null, Table::HISTORY, ['approvedBy'], \craft\db\Table::USERS, ['id']);
        $this->addForeignKey(null, Table::PAYRUN_IMPORTS, ['approvedBy'], \craft\db\Table::USERS, ['id']);
        $this->addForeignKey(null, Table::PAYRUN_IMPORTS, ['uploadedBy'], \craft\db\Table::USERS, ['id']);
        $this->addForeignKey(null, Table::PAYRUN_IMPORTS, ['userId'], \craft\db\Table::USERS, ['id']);
        $this->addForeignKey(null, Table::REQUESTS, ['administerId'], \craft\db\Table::USERS, ['id']);
    }

    /**
     * Insert the default data.
     */
    public function insertDefaultData(): void
    {
        $this->_createPermissions();
        $this->_defaultCountries();
    }

    /**
     * Insert default countries data.
     */
    private function _defaultCountries(): void
    {
        $countries = [
            ['ENG', 'England'],
            ['NIR', 'Northern Ireland'],
            ['SCT', 'Scotland'],
            ['WLS', 'Wales'],
            ['UKM', 'United Kingdom'],
            ['OUK', 'Outside of the UK'],
        ];

        $orderNumber = 1;
        foreach ($countries as $key => $country) {
            $country[] = $orderNumber;
            $countries[$key] = $country;
            $orderNumber++;
        }

        $this->batchInsert(Table::COUNTRIES, ['iso', 'name', 'sortOrder'], $countries);
    }

    /**
     * Create the permissions for the Company Users
     */
    private function _createPermissions(): void
    {
        $rows = [];

        $rows[] = ['access:employers'];
        $rows[] = ['access:employer'];
        $rows[] = ['access:groupbenefits'];
        $rows[] = ['access:voluntarybenefits'];
        $rows[] = ['access:history'];
        $rows[] = ['access:requests'];
        $rows[] = ['manage:notifications'];
        $rows[] = ['manage:employees'];
        $rows[] = ['manage:employer'];
        $rows[] = ['manage:benefits'];
        $rows[] = ['manage:requests'];
        $rows[] = ['purchase:groupbenefits'];
        $rows[] = ['purchase:voluntarybenefits'];

        $this->batchInsert(Table::PERMISSIONS, ['name'], $rows);
    }
}
