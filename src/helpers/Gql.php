<?php

namespace percipiolondon\staff\helpers;

use craft\helpers\Gql as CraftGql;

class Gql extends CraftGql
{
    public static function canQueryBenefitProviders(): bool
    {
        return true;
    }

    public static function canQueryEmployers(): bool
    {
        return true;
    }

    public static function canQueryEmployees(): bool
    {
        return true;
    }

    public static function canQueryHistory(): bool
    {
        return true;
    }

    public static function canQueryPayruns(): bool
    {
        return true;
    }

    public static function canQueryPayrunEntries(): bool
    {
        return true;
    }

    public static function canQueryRequests(): bool
    {
        return true;
    }

    public static function canMutateRequests(): bool
    {
        return true;
    }
}
