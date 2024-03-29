<?php

namespace percipiolondon\staff\gql\types;

use craft\gql\base\GqlTypeTrait;
use craft\gql\types\DateTime;
use GraphQL\Type\Definition\Type;

/**
 * Class Request
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class Request
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'request';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            'dateAdministered' => [
                'name' => 'dateAdministered',
                'type' => DateTime::getType(),
                'description' => 'Date administered',
            ],
            'employerId' => [
                'name' => 'employerId',
                'type' => Type::int(),
                'description' => 'Employer id',
            ],
            'employeeId' => [
                'name' => 'employeeId',
                'type' => Type::int(),
                'description' => 'Employee id',
            ],
            'administerId' => [
                'name' => 'administerId',
                'type' => Type::int(),
                'description' => 'Administer id',
            ],
            'data' => [
                'name' => 'data',
                'type' => Type::string(),
                'description' => 'Data',
            ],
            'type' => [
                'name' => 'type',
                'type' => Type::string(),
                'description' => 'Type',
            ],
            'status' => [
                'name' => 'status',
                'type' => Type::string(),
                'description' => 'Status',
            ],
        ];
    }
}
