<?php
/**
 * staff-management plugin for Craft CMS 3.x
 *
 * Craft Staff Management provides an HR solution for payroll and benefits
 *
 * @link      http://percipio.london
 * @copyright Copyright (c) 2021 Percipio
 */

namespace percipiolondon\staff\records;

use craft\db\ActiveRecord;
use percipiolondon\staff\db\Table;

/**
 * @property int $employerId
 * @property int $totalsId;
 *
 * @property string $staffologyId;
 * @property string $taxYear;
 * @property int $taxMonth;
 * @property string $payPeriod;
 * @property int $ordinal;
 * @property int $period;
 * @property \DateTime $startDate;
 * @property \DateTime $endDate;
 * @property \DateTime $paymentDate;
 * @property int $employeeCount;
 * @property int $subContractorCount;
 * @property string $state;
 * @property boolean $isClosed;
 * @property \DateTime $dateClosed;
 * @property string $pdf
 * @property string $url
 */

class PayRun extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================

    /**
    * Declares the name of the database table associated with this AR class.
    * By default this method returns the class name as the table name by calling [[Inflector::camel2id()]]
    * with prefix [[Connection::tablePrefix]]. For example if [[Connection::tablePrefix]] is `tbl_`,
    * `Customer` becomes `tbl_customer`, and `OrderItem` becomes `tbl_order_item`. You may override this method
    * if the table is not named after this convention.
    *
    * By convention, tables created by plugins should be prefixed with the plugin
    * name and an underscore.
    *
    * @return string the table name
    */
    public static function tableName(): string
    {
        return Table::PAY_RUN;
    }
}
