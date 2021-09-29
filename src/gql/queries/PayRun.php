<?php

namespace percipiolondon\craftstaff\gql\queries;

use craft\gql\base\Query;

use GraphQL\Type\Definition\Type;

use percipiolondon\craftstaff\gql\arguments\elements\PayRun as PayRunArguments;
use percipiolondon\craftstaff\gql\interfaces\elements\PayRun as PayRunInterface;
use percipiolondon\craftstaff\gql\resolvers\elements\PayRun as PayRunResolver;
use percipiolondon\craftstaff\helpers\Gql as GqlHelper;

class PayRun extends Query
{

        public static function getQueries($checkToken = true): array
        {
            return [
                'payruns' => [
                    'type' => Type::listOf(PayRunInterface::getType()),
                    'args' => PayRunArguments::getArguments(),
                    'resolve' => PayRunResolver::class . '::resolve',
                    'description' => 'This query is used to query for all the payruns.',
                    'complexity' => GqlHelper::relatedArgumentComplexity(),
                ],
                'payrun' => [
                    'type' => PayRunInterface::getType(),
                    'args' => PayRunArguments::getArguments(),
                    'resolve' => PayRunResolver::class . '::resolveOne',
                    'description' => 'This query is used to query for a single payrun.',
                    'complexity' => GqlHelper::relatedArgumentComplexity(),
                ]
            ];
        }

}