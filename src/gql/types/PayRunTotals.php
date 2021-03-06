<?php

namespace percipiolondon\staff\gql\types;

use craft\gql\base\GqlTypeTrait;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use percipiolondon\staff\helpers\Security as SecurityHelper;

/**
 * Class PayRunTotals
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class PayRunTotals
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'payRunTotals';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            'basicPay' => [
                'name' => 'basicPay',
                'type' => Type::float(),
                'description' => 'The amount to be paid to this Employee as a result of the PayOptions that have been set',
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
            'gross' => [
                'name' => 'gross',
                'type' => Type::float(),
                'description' => 'Gross pay',
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
            'grossForNi' => [
                'name' => 'grossForNi',
                'type' => Type::float(),
                'description' => 'The amount of the Gross that is subject to NI',
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
            'grossNotSubjectToEmployersNi' => [
                'name' => 'grossNotSubjectToEmployersNi',
                'type' => Type::float(),
                'description' => 'The amount of the Gross that is subject to NI',
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
            'grossForTax' => [
                'name' => 'grossForTax',
                'type' => Type::float(),
                'description' => 'The amount of the Gross that is subject to PAYE',
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
            'employerNi' => [
                'name' => 'employerNi',
                'type' => Type::float(),
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
            'employeeNi' => [
                'name' => 'employeeNi',
                'type' => Type::float(),
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
            'employerNiOffPayroll' => [
                'name' => 'employerNiOffPayroll',
                'type' => Type::float(),
                'description' => 'The amount included in EmployerNi that is in relation to Off-Payroll Workers',
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
            'realTimeClass1ANi' => [
                'name' => 'realTimeClass1ANi',
                'type' => Type::float(),
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
            'tax' => [
                'name' => 'tax',
                'type' => Type::float(),
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
            'netPay' => [
                'name' => 'netPay',
                'type' => Type::float(),
                'description' => 'The Net Pay for this Employee.',
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
            'adjustments' => [
                'name' => 'adjustments',
                'type' => Type::float(),
                'description' => 'The value of adjustments made to the Net Pay (Non taxable/NIable additions/deductions)',
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
            'additions' => [
                'name' => 'additions',
                'type' => Type::float(),
                'description' => 'The value of all additions. This minus Deductions should equal TakeHomePay',
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
            'deductions' => [
                'name' => 'deductions',
                'type' => Type::float(),
                'description' => 'The value of all deductions. Additions minus This value should equal TakeHomePay',
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
            'takeHomePay' => [
                'name' => 'takeHomePay',
                'type' => Type::float(),
                'description' => 'The amount this Employee takes home',
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
            'nonTaxOrNICPmt' => [
                'name' => 'nonTaxOrNICPmt',
                'type' => Type::float(),
                'description' => 'The value of any payments being made to this Employee that aren\'t being subjected to PAYE or NI',
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
            'itemsSubjectToClass1NIC' => [
                'name' => 'itemsSubjectToClass1NIC',
                'type' => Type::float(),
                'description' => 'Items subject to Class 1 NIC but not taxed under PAYE regulations excluding pension contributions',
            ],
            'dednsFromNetPay' => [
                'name' => 'dednsFromNetPay',
                'type' => Type::float(),
                'description' => 'The value of any deductions being made to the Net Pay for this Employee',
            ],
            'tcp_Tcls' => [
                'name' => 'tcp_Tcls',
                'type' => Type::float(),
                'description' => 'Value of payments marked as Trivial Commutation Payment (A - TCLS)',
            ],
            'tcp_Pp' => [
                'name' => 'tcp_Pp',
                'type' => Type::float(),
                'description' => 'Value of payments marked as Trivial Commutation Payment (B - Personal Pension)',
            ],
            'tcp_Op' => [
                'name' => 'tcp_Op',
                'type' => Type::float(),
                'description' => 'Value of payments marked as Trivial Commutation Payment (C - Occupational Pension)',
            ],
            'flexiDd_Death' => [
                'name' => 'flexiDd_Death',
                'type' => Type::float(),
                'description' => 'Value of payments marked as flexibly accessing death benefit (taxable)',
            ],
            'flexiDd_Death_NonTax' => [
                'name' => 'flexiDd_Death_NonTax',
                'type' => Type::float(),
                'description' => 'Value of payments marked as flexibly accessing death benefit (non taxable)',
            ],
            'flexiDd_Pension' => [
                'name' => 'flexiDd_Pension',
                'type' => Type::float(),
                'description' => 'Value of payments marked as flexibly accessing pension (taxable)',
            ],
            'flexiDd_Pension_NonTax' => [
                'name' => 'flexiDd_Pension_NonTax',
                'type' => Type::float(),
                'description' => 'Value of payments marked as flexibly accessing pension (non taxable)',
            ],
            'smp' => [
                'name' => 'smp',
                'type' => Type::float(),
                'description' => 'Statutory Maternity Pay',
            ],
            'spp' => [
                'name' => 'spp',
                'type' => Type::float(),
                'description' => 'Statutory Paternity Pay',
            ],
            'sap' => [
                'name' => 'sap',
                'type' => Type::float(),
                'description' => 'Statutory Adoption Pay',
            ],
            'shpp' => [
                'name' => 'shpp',
                'type' => Type::float(),
                'description' => 'Statutory Shared Parental Pay',
            ],
            'spbp' => [
                'name' => 'spbp',
                'type' => Type::float(),
                'description' => 'Statutory Parental Bereavement Pay',
            ],
            'ssp' => [
                'name' => 'ssp',
                'type' => Type::float(),
                'description' => 'Statutory Sick Pay',
            ],
            'studentLoanRecovered' => [
                'name' => 'studentLoanRecovered',
                'type' => Type::float(),
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
            'postgradLoanRecovered' => [
                'name' => 'postgradLoanRecovered',
                'type' => Type::float(),
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
            'pensionableEarnings' => [
                'name' => 'pensionableEarnings',
                'type' => Type::float(),
                'description' => 'The amount of the Gross that is subject to Pension Deductions. If the Pension Scheme uses Qualifying Earnings (upper and lower limits) then this value is before those are applied',
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
            'pensionablePay' => [
                'name' => 'pensionablePay',
                'type' => Type::float(),
                'description' => 'The amount of the Gross that pension calculations are based on after taking into account Upper and Lower Limits for the WorkerGroup.',
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
            'nonTierablePay' => [
                'name' => 'nonTierablePay',
                'type' => Type::float(),
                'description' => 'The value of any pay that shouldn\'t count towards determining a pension tier.',
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
            'employeePensionContribution' => [
                'name' => 'employeePensionContribution',
                'type' => Type::float(),
                'description' => 'The value of the Pension Contribution being made by this Employee, excluding any Additional Voluntary Contributions',
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
            'employeePensionContributionAvc' => [
                'name' => 'employeePensionContributionAvc',
                'type' => Type::float(),
                'description' => 'The value of the Pension Contribution being made by this Employee as an Additional Voluntary Contribution',
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
            'employerPensionContribution' => [
                'name' => 'employerPensionContribution',
                'type' => Type::float(),
                'description' => 'The value of the Pension Contribution being made by the Employer for this Employee',
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
            'empeePenContribnsNotPaid' => [
                'name' => 'empeePenContribnsNotPaid',
                'type' => Type::float(),
                'description' => 'Value of employee pension contributions that are not paid under \'net pay arrangements\', including any AVC',
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
            'empeePenContribnsPaid' => [
                'name' => 'empeePenContribnsPaid',
                'type' => Type::float(),
                'description' => 'Value of employee pension contributions paid under \'net pay arrangements\', including any AVC',
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
            'attachmentOrderDeductions' => [
                'name' => 'attachmentOrderDeductions',
                'type' => Type::float(),
                'description' => 'Value of deductions made due to AttachmentOrders',
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
            'cisDeduction' => [
                'name' => 'cisDeduction',
                'type' => Type::float(),
                'description' => 'Value of any CIS Deduction made',
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
            'cisVat' => [
                'name' => 'cisVat',
                'type' => Type::float(),
                'description' => 'Value of any VAT paid to CIS Subcontractor',
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
            'cisUmbrellaFee' => [
                'name' => 'cisUmbrellaFee',
                'type' => Type::float(),
                'description' => 'Value of any pre-tax fee charged to the CIS Subcontractor for processing the payment',
            ],
            'cisUmbrellaFeePostTax' => [
                'name' => 'cisUmbrellaFeePostTax',
                'type' => Type::float(),
                'description' => 'Value of any post-tax fee charged to the CIS Subcontractor for processing the payment',
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
            'pbik' => [
                'name' => 'pbik',
                'type' => Type::float(),
                'description' => 'Value of any Payrolled BenefitProvider In Kind',
            ],
            'mapsMiles' => [
                'name' => 'mapsMiles',
                'type' => Type::int(),
                'description' => 'The number of miles paid for Mileage Allowance Payments',
            ],
            'umbrellaFee' => [
                'name' => 'umbrellaFee',
                'type' => Type::float(),
                'description' => 'Value of any Umbrella fee charged for processing the payment',
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
            'appLevyDeduction' => [
                'name' => 'appLevyDeduction',
                'type' => Type::float(),
                'description' => 'Value of any Apprenticeship Levy fee deducted for processing the umbrella payment',
            ],
            'paymentAfterLeaving' => [
                'name' => 'paymentAfterLeaving',
                'type' => Type::float(),
                'description' => 'Payment After Leaving',
            ],
            'taxOnPaymentAfterLeaving' => [
                'name' => 'taxOnPaymentAfterLeaving',
                'type' => Type::float(),
                'description' => 'Tax On Payment After Leaving',
            ],
            'nilPaid' => [
                'name' => 'nilPaid',
                'type' => Type::int(),
            ],
            'leavers' => [
                'name' => 'leavers',
                'type' => Type::int(),
            ],
            'starters' => [
                'name' => 'starters',
                'type' => Type::int(),
            ],
            'totalCost' => [
                'name' => 'totalCost',
                'type' => Type::float(),
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo, 'float');
                },
            ],
        ];
    }
}
