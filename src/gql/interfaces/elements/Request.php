<?php

namespace percipiolondon\staff\gql\interfaces\elements;

use craft\gql\GqlEntityRegistry;
use craft\gql\interfaces\Element;
use craft\gql\TypeManager;
use craft\gql\types\DateTime;
use craft\helpers\DateTimeHelper;
use craft\helpers\Gql;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use percipiolondon\staff\elements\Request as RequestElement;
use percipiolondon\staff\gql\types\Employee;
use percipiolondon\staff\gql\types\generators\RequestGenerator;

class Request extends Element
{
    public static function getTypeGenerator(): string
    {
        return RequestGenerator::class;
    }

    /**
     * @inheritdoc
     */
    public static function getType($fields = null): Type
    {
        if ($type = GqlEntityRegistry::getEntity(self::getName())) {
            return $type;
        }

        $type = GqlEntityRegistry::createEntity(self::getName(), new InterfaceType([
            'name' => static::getName(),
            'fields' => self::class . '::getFieldDefinitions',
            'description' => 'This is the interface implemented by all requests.',
            'resolveType' => function(RequestElement $value) {
                return $value->getGqlTypeName();
            },
        ]));

        RequestGenerator::generateTypes();

        return $type;
    }

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'RequestInterface';
    }

    /**
     * @inheritdoc
     */
    public static function getFieldDefinitions(): array
    {
        $parentFields = parent::getFieldDefinitions();

        $fields = [
            'dateAdministered' => [
                'name' => 'dateAdministered',
                'type' => DateTime::getType(),
                'description' => 'Date administered',
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Gql::applyDirectives($source, $resolveInfo, DateTimeHelper::toDateTime($source->dateAdministered));
                }
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
            'employee' => [
                'name' => 'employee',
                'type' => Employee::getType(),
                'description' => 'The employee of where the request belongs to'
            ],
            'employer' => [
                'name' => 'employer',
                'type' => Type::string(),
                'description' => 'The company name of where the pay run belongs to',
            ],
            'admin' => [
                'name' => 'admin',
                'type' => Type::string(),
                'description' => 'Get the admin name that handled the request',
            ],
            'request' => [
                'name' => 'request',
                'type' => Type::string(),
                'description' => 'The requested data'
            ],
            'current' => [
                'name' => 'current',
                'type' => Type::string(),
                'description' => 'The current data'
            ]
        ];

        return TypeManager::prepareFieldDefinitions(array_merge($parentFields, $fields), self::getName());
    }
}