<?php

namespace percipiolondon\staff\records;

use craft\db\ActiveRecord;
use percipiolondon\staff\db\Table;

/**
 * @property int cisSubcontractorId;
 * @property int countryId;
 * @property integer employerId;
 * @property integer employeeId;
 * @property integer pensionAdministratorId;
 * @property integer pensionProviderId;
 * @property integer rtiAgentId;
 * @property integer rtiEmployeeAddressId;
 *
 * @property string address1;
 * @property string address2;
 * @property string address3;
 * @property string address4;
 * @property string zipCode;
 */

class Address extends ActiveRecord
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
        return Table::ADDRESSES;
    }
}
