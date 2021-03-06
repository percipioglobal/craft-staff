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
 * @property string employerId;
 *
 * @property string title;
 * @property int code;
 * @property string defaultValue;
 * @property boolean isDeduction;
 * @property boolean isNiable;
 * @property boolean isTaxable;
 * @property boolean isPensionable;
 * @property boolean isAttachable;
 * @property boolean isRealTimeClass1aNiable;
 * @property boolean isNotContributingToHolidayPay;
 * @property boolean isQualifyingEarningsForAe;
 * @property boolean isNotTierable;
 * @property boolean isTcp_Tcls;
 * @property boolean isTcp_Pp;
 * @property boolean isTcp_Op;
 * @property boolean isFlexiDd_DeathBenefit;
 * @property boolean isFlexiDd_Pension;
 * @property string calculationType;
 * @property double hourlyRateMultiplier;
 * @property boolean isSystemCode;
 * @property boolean isControlCode;
 */

class PayCode extends ActiveRecord
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
        return Table::PAY_CODES;
    }
}
