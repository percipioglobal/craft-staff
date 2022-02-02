<?php

namespace percipiolondon\staff\elements\db;

use craft\db\Query;
use craft\db\QueryAbortedException;
use craft\db\Table;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;

use yii\db\Connection;

class EmployeeQuery extends ElementQuery
{
    public $staffologyId;
    public $employerId;
    public $userId;
    public $personalDetails;
    public $employmentDetails;
    public $autoEnrolment;
    public $leaveSettings;
    public $rightToWork;
    public $bankDetails;
    public $status;
    public $aeNotEnroledWarning;
    public $niNumber;
    public $sourceSystemId;
    public $isDirector;

    /**
     * @inheritdoc
     */
    public function __construct($elementType, array $config = [])
    {
        parent::__construct($elementType, $config);
    }

    public function personalDetails($value)
    {
        $this->personalDetails = $value;
        return $this;
    }
    public function staffologyId($value)
    {
        $this->staffologyId = $value;
        return $this;
    }
    public function employerId($value)
    {
        $this->employerId = $value;
        return $this;
    }
    public function userId($value)
    {
        $this->userId = $value;
        return $this;
    }
    public function employmentDetails($value)
    {
        $this->employmentDetails = $value;
        return $this;
    }
    public function autoEnrolment($value)
    {
        $this->autoEnrolment = $value;
        return $this;
    }
    public function leaveSettings($value)
    {
        $this->leaveSettings = $value;
        return $this;
    }
    public function rightToWork($value)
    {
        $this->rightToWork = $value;
        return $this;
    }
    public function bankDetails($value)
    {
        $this->bankDetails = $value;
        return $this;
    }
    public function sourceSystemId($value)
    {
        $this->sourceSystemId = $value;
        return $this;
    }
    public function status($value)
    {
        $this->status = $value;
        return $this;
    }

    public function aeNotEnroledWarning($value)
    {
        $this->aeNotEnroledWarning = $value;
        return $this;
    }

    public function niNumber($value)
    {
        $this->niNumber = $value;
        return $this;
    }

    public function isDirector($value)
    {
        $this->isDirector = $value;
        return $this;
    }

    protected function beforePrepare(): bool
    {
        $this->joinElementTable('staff_employees');

        $this->query->select([
            'staff_employees.personalDetails',
            'staff_employees.siteId',
            'staff_employees.staffologyId',
            'staff_employees.employerId',
            'staff_employees.userId',
            'staff_employees.personalDetails',
            'staff_employees.employmentDetails',
            'staff_employees.autoEnrolment',
            'staff_employees.leaveSettings',
            'staff_employees.rightToWork',
            'staff_employees.bankDetails',
            'staff_employees.status',
            'staff_employees.aeNotEnroledWarning',
            'staff_employees.niNumber',
            'staff_employees.sourceSystemId',
            'staff_employees.isDirector',
        ]);

        if ($this->staffologyId) {
            $this->subQuery->andWhere(Db::parseParam('staff_employees.staffologyId', $this->staffologyId));
        }

        if ($this->employerId) {
            $this->subQuery->andWhere(Db::parseParam('staff_employees.employerId', $this->employerId));
        }

        if ($this->userId) {
            $this->subQuery->andWhere(Db::parseParam('staff_employees.userId', $this->userId));
        }

        if ($this->isDirector) {
            $this->subQuery->andWhere(Db::parseParam('staff_employees.isDirector', $this->isDirector, '=', false));
        }

        if ($this->status) {
            $this->subQuery->andWhere(Db::parseParam('staff_employees.status', $this->status, '=', true));
        }

        return parent::beforePrepare();
    }
}
