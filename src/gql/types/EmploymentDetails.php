<?php

namespace percipiolondon\staff\gql\types;

use craft\gql\base\GqlTypeTrait;
use craft\gql\types\DateTime;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use percipiolondon\staff\helpers\Security;

/**
 * Class EmploymentDetails
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class EmploymentDetails
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'employmentDetails';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            'cisSubContractor' => [
                'name' => 'cisSubContractor',
                'type' => Type::boolean(),
                'description' => 'Set to True if this Employee is a CIS Subcontractor.',
            ],
            'payrollCode' => [
                'name' => 'payrollCode',
                'type' => Type::string(),
                'description' => 'The Employees Payroll Code. Must be unique within the Employer.',
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Security::resolve($source, $resolveInfo, 'string');
                },
            ],
            'jobTitle' => [
                'name' => 'jobTitle',
                'type' => Type::string(),
                'description' => 'Job Title of Primary post of the Employee.',
            ],
            'onHold' => [
                'name' => 'onHold',
                'type' => Type::boolean(),
                'description' => 'Set to true to temporarily exclude the employee from payruns',
            ],
            'onFurlough' => [
                'name' => 'onFurlough',
                'type' => Type::boolean(),
                'description' => 'Set to true if the employee is on furlough.',
            ],
            'furloughStart' => [
                'name' => 'furloughStart',
                'type' => DateTime::getType(),
                'description' => 'Furlough start date.',
            ],
            'furloughEnd' => [
                'name' => 'furloughEnd',
                'type' => DateTime::getType(),
                'description' => 'Furlough end date.',
            ],
            // TODO CREATE ENUM
            'furloughCalculationBasis' => [
                'name' => 'furloughCalculationBasis',
                'type' => Type::string(),
                'description' => 'Furlough calculation basis',
            ],
            'furloughCalculationBasisAmount' => [
                'name' => 'furloughCalculationBasisAmount',
                'type' => Type::float(),
                'description' => 'Furlough calculation basis amount.',
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Security::resolve($source, $resolveInfo, 'float');
                },
            ],
            'partialFurlough' => [
                'name' => 'partialFurlough',
                'type' => Type::boolean(),
                'description' => 'Partial furlough?',
            ],
            'furloughHoursNormallyWorked' => [
                'name' => 'furloughHoursNormallyWorked',
                'type' => Type::float(),
                'description' => 'Furlough hours normally worked.',
            ],
            'furloughHoursOnFurlough' => [
                'name' => 'furloughHoursOnFurlough',
                'type' => Type::float(),
                'description' => 'Furlough hours on furlough.',
            ],
            'isApprentice' => [
                'name' => 'isApprentice',
                'type' => Type::boolean(),
                'description' => 'Set to True if this Employee is an apprentice. This affects the calculations for National Minimum Wage',
            ],
            'workingPattern' => [
                'name' => 'workingPattern',
                'type' => Type::string(),
                'description' => 'Used when calculating payments for Leave. If null then the default Working Pattern is used.',
            ],
            'forcePreviousPayrollCode' => [
                'name' => 'forcePreviousPayrollCode',
                'type' => Type::string(),
                'description' => 'If this property has a non-empty value then a change of Payroll code will be declared on the next FPS.',
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Security::resolve($source, $resolveInfo, 'string');
                },
            ],
            'starterDetails' => [
                'name' => 'starterDetails',
                'type' => StarterDetails::getType(),
            ],
            'directorshipDetails' => [
                'name' => 'directorshipDetails',
                'type' => DirectorshipDetails::getType(),
            ],
            'leaverDetails' => [
                'name' => 'leaverDetails',
                'type' => LeaverDetails::getType(),
            ],
            'cis' => [
                'name' => 'cisDetails',
                'type' => CisDetails::getType(),
            ],
            'department' => [
                'name' => 'department',
                'type' => Department::getType(),
            ],
        ];
    }
}
