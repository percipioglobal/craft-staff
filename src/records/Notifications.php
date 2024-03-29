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
 * @property int employeeId;
 * @property int employerId;
 * @property string message;
 * @property string type;
 * @property boolean viewed;
 */

class Notifications extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================
    public static function tableName(): string
    {
        return Table::NOTIFICATIONS;
    }
}
