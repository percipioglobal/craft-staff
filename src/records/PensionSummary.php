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
 * @property int payRunEntryId;
 * @property int workerGroupId;
 *
 * @property string name;
 * @property string startDate;
 * @property string pensionRule;
 * @property string employeePensionContributionMultiplier;
 * @property string additionalVoluntaryContribution;
 * @property bool avcIsPercentage;
 * @property bool autoEnrolled;
 * @property int papdisPensionProviderId;
 * @property int papdisEmployerId;
 */

class PensionSummary extends ActiveRecord
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
        return Table::PENSION_SUMMARY;
    }
}
