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

use percipiolondon\staff\Staff;

use Craft;
use craft\db\ActiveRecord;
use percipiolondon\staff\db\Table;

/**
 * Employer Record
 *
 * ActiveRecord is the base class for classes representing relational data in terms of objects.
 *
 * Active Record implements the [Active Record design pattern](http://en.wikipedia.org/wiki/Active_record).
 * The premise behind Active Record is that an individual [[ActiveRecord]] object is associated with a specific
 * row in a database table. The object's attributes are mapped to the columns of the corresponding table.
 * Referencing an Active Record attribute is equivalent to accessing the corresponding table column for that record.
 *
 * http://www.yiiframework.com/doc-2.0/guide-db-active-record.html
 *
 * @author    Percipio
 * @package   Staff
 * @since     1.0.0-alpha.1
 */

/**
 * Employer record
 * @property integer $employeeId;
 * @property integer $addressId;
 * @property string $maritalStatus;
 * @property string $title;
 * @property string $firstName;
 * @property string $middleName;
 * @property string $lastName;
 * @property string $email;
 * @property string $emailPayslip;
 * @property boolean $passwordProtectPayslip;
 * @property string $pdfPassword;
 * @property string $telephone;
 * @property string $mobile;
 * @property \DateTime $dob;
 * @property integer $statePensionAge;
 * @property string $gender;
 * @property string $niNumber;
 * @property string $passportNumber;
 */

class PersonalDetails extends ActiveRecord
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
    public static function tableName()
    {
        return Table::PERSONAL_DETAILS;
    }
}
