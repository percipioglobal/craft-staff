<?php

namespace percipiolondon\staff\elements;

use Craft;
use craft\elements\Asset;
use craft\base\Element;
use craft\elements\db\ElementQueryInterface;
use craft\web\Request;
use percipiolondon\staff\elements\db\BenefitVariantQuery;
use percipiolondon\staff\helpers\variants\VariantGcic;
use percipiolondon\staff\helpers\variants\VariantGdis;
use percipiolondon\staff\helpers\variants\VariantGla;
use percipiolondon\staff\helpers\variants\VariantPmi;
use percipiolondon\staff\records\BenefitEmployeeVariant;
use percipiolondon\staff\records\BenefitPolicy;
use percipiolondon\staff\records\BenefitType;
use percipiolondon\staff\records\BenefitVariantGcic;
use percipiolondon\staff\records\BenefitVariantGdis;
use percipiolondon\staff\records\BenefitVariantGla;
use percipiolondon\staff\records\BenefitVariantPmi;
use percipiolondon\staff\records\TotalRewardsStatement;
use percipiolondon\staff\records\BenefitVariant as BenefitVariantRecord;
use yii\web\NotFoundHttpException;

/**
 *
 * @property-read string $gqlTypeName
 * @property-read null|array $provider
 * @property-read null|array $employees
 * @property-read null|\percipiolondon\staff\records\BenefitPolicy $policy
 * @property-read \percipiolondon\staff\records\TotalRewardsStatement|null $totalRewardsStatement
 */
class BenefitVariant extends Element
{
    // Public Properties
    // =========================================================================
    /**
     * @var TotalRewardsStatement|null
     */
    public ?TotalRewardsStatement $trs = null;
    /**
     * @var int|null
     */
    public ?int $policyId = null;
    /**
     * @var Request|null
     */
    public ?Request $request = null;
    /**
     * @var string|null
     */
    public ?string $name = null;

    // Private Properties
    // =========================================================================
    /**
     * @var TotalRewardsStatement|null
     */
    private ?TotalRewardsStatement $_totalRewardsStatement = null;
    /**
     * @var BenefitPolicy|null
     */
    private ?BenefitPolicy $_policy = null;
    /**
     * @var array|null
     */
    private ?array $_provider = null;
    /**
     * @var array|null
     */
    private ?array $_employees = null;
    /**
     * @var string|null
     */
    private ?string $_type = null;
    /**
     * @var BenefitVariantGcic|null
     */
    private ?BenefitVariantGcic $_gcic = null;
    /**
     * @var BenefitVariantGdis|null
     */
    private ?BenefitVariantGdis $_gdis = null;
    /**
     * @var BenefitVariantGla|null
     */
    private ?BenefitVariantGla $_gla = null;
    /**
     * @var BenefitVariantPmi|null
     */
    private ?BenefitVariantPmi $_pmi = null;



    // Static Methods
    // =========================================================================
    /**
     * @return string
     */
    public static function displayName(): string
    {
        return Craft::t('staff-management', 'Benefit Variant');
    }

    /**
     * @return string
     */
    public static function lowerDisplayName(): string
    {
        return Craft::t('staff-management', 'benefit variant');
    }

    /**
     * @return string
     */
    public static function pluralDisplayName(): string
    {
        return Craft::t('staff-management', 'Benefit Variants');
    }

    /**
     * @return string
     */
    public static function pluralLowerDisplayName(): string
    {
        return Craft::t('staff-management', 'benefit variants');
    }

    /**
     * @return ElementQueryInterface
     */
    public static function find(): ElementQueryInterface
    {
        return new BenefitVariantQuery(static::class);
    }

    /**
     * @param mixed $context
     * @return string
     */
    public static function gqlTypeNameByContext($context): string
    {
        return 'BenefitVariant';
    }



    // Public Methods
    // =========================================================================
    /**
     * @return array
     */
    public function defineRules(): array
    {
        return parent::defineRules();
    }

    // Getters
    // -------------------------------------------------------------------------
    /**
     * @inheritdoc
     */
    public function getGqlTypeName(): string
    {
        return static::gqlTypeNameByContext($this);
    }

    /**
     * @return TotalRewardsStatement|null
     */
    public function getTotalRewardsStatement(): ?TotalRewardsStatement
    {
        if ($this->_totalRewardsStatement === null) {
            if ($this->id === null) {
                return null;
            }

            $this->_totalRewardsStatement = TotalRewardsStatement::findOne(['variantId' => $this->id]);

            return $this->_totalRewardsStatement;
        }
    }

    /**
     * @return BenefitPolicy|null
     */
    public function getPolicy(): ?BenefitPolicy
    {
        if ($this->_policy === null) {
            if ($this->policyId === null) {
                return null;
            }

            $this->_policy = BenefitPolicy::findOne($this->policyId);
        }

        return $this->_policy;
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function getProvider(): ?array
    {
        if ($this->_provider === null) {
            if ($this->policyId === null) {
                return null;
            }

            $this->getPolicy();

            if ($this->_policy === null) {
                return null;
            }

            $this->_provider = BenefitProvider::findOne($this->_policy['providerId'])->toArray();

            if ($this->_provider) {
                $this->_provider['logo'] = Asset::findOne($this->_provider['logo'])?->getUrl();
            }
        }

        return $this->_provider;
    }

    /**
     * @return array|null
     */
    public function getEmployees(): ?array
    {
        if ($this->_employees === null) {
            $variantEmployees = BenefitEmployeeVariant::findAll(['variantId' => $this->id]);

            $this->_employees = [];
            foreach($variantEmployees as $employee) {
                $this->_employees[] = Employee::findOne($employee);
            }

            return $this->_employees;
        }
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        $benefitType = BenefitType::findOne($this->getPolicy()->benefitTypeId ?? null);

        if ($benefitType) {
            $this->_type = $benefitType->slug;
        }

        return $this->_type;
    }

    // Getters for specific benefit type
    // -------------------------------------------------------------------------
    /**
     * @return BenefitVariantGcic|null
     */
    public function getGcic(): ?BenefitVariantGcic
    {
        if ($this->_gcic === null) {
            $this->_gcic = BenefitVariantGcic::findOne($this->id);
        }

        return $this->_gcic;
    }

    /**
     * @return BenefitVariantGdis|null
     */
    public function getGdis(): ?BenefitVariantGdis
    {
        if ($this->_gdis === null) {
            $this->_gdis = BenefitVariantGdis::findOne($this->id);
        }

        return $this->_gdis;
    }

    /**
     * @return BenefitVariantGla|null
     */
    public function getGla(): ?BenefitVariantGla
    {
        if ($this->_gla === null) {
            $this->_gla = BenefitVariantGla::findOne($this->id);
        }

        return $this->_gla;
    }

    /**
     * @return BenefitVariantPmi|null
     */
    public function getPmi(): ?BenefitVariantPmi
    {
        if ($this->_pmi === null) {
            $this->_pmi = BenefitVariantPmi::findOne($this->id);
        }

        return $this->_pmi;
    }

    // Get fields and their values for specific benefit types
    // -------------------------------------------------------------------------
    /**
     * @param string $benefitTypeSlug
     * @return array|null
     */
    public function getFields(string $benefitTypeSlug): ?array
    {
        $variant = match ($benefitTypeSlug ?? '') {
            'gcic' => VariantGcic::get($this->id, $this->request),
            'gdis' => VariantGdis::get($this->id, $this->request),
            'gla' => VariantGla::get($this->id, $this->request),
            'pmi' => VariantPmi::get($this->id, $this->request),
            default => null
        };

        if ($variant) {
            return $variant;
        }

        return null;
    }

    /**
     * @param string $benefitTypeSlug
     * @return array|null
     */
    public function getValues(string $benefitTypeSlug): ?array
    {
        return match ($benefitTypeSlug ?? '') {
            'gcic' => is_null($this->getGcic()) ? [] : $this->getGcic()->toArray(),
            'gdis' => is_null($this->getGdis()) ? [] : $this->getGdis()->toArray(),
            'gla' => is_null($this->getGla()) ? [] : $this->getGla()->toArray(),
            'pmi' => is_null($this->getPmi()) ? [] : $this->getPmi()->toArray(),
            default => []
        };
    }

    /**
     * @param string $benefitSlug
     * @return mixed
     */
    public function getFilledVariant(string $benefitSlug): mixed
    {
        return match ($benefitSlug ?? '') {
            'gcic' => VariantGcic::fill($this->id ?? null, $this->request),
            'gdis' => VariantGdis::fill($this->id ?? null, $this->request),
            'gla' => VariantGla::fill($this->id ?? null, $this->request),
            'pmi' => VariantPmi::fill($this->id ?? null, $this->request),
            default => null
        };
    }

    // Events
    // -------------------------------------------------------------------------
    /**
     * @param bool $isNew
     */
    public function afterSave(bool $isNew): void
    {
        if (!$this->propagating) {
            $this->_saveRecord();
        }

        parent::afterSave($isNew);
    }



    // Private Methods
    // =========================================================================
    /**
     * Save the Benefit Variant record
     */
    private function _saveRecord(): void
    {
        try {
            $policyId = $this->policyId;

            $policy = BenefitPolicy::findOne($policyId);

            if (is_null($policy)) {
                throw new NotFoundHttpException(Craft::t('staff-management', 'Policy does not exist'));
            }

            //save benefit variant generic
            $benefit = BenefitVariantRecord::findOne($this->id);

            if (is_null($benefit)) {
                $benefit = new BenefitVariantRecord();
                $benefit->id = $this->id;
            }

            $benefit->name = $this->name;
            $benefit->policyId = $this->policyId;
            $successBenefit = $benefit->save();

            if(!$successBenefit) {
                Craft::error(Craft::t('staff-management','The save of the Benefit Variant wasn\'t successfull'));
            }

            // save benefit type variants
            $benefitType = BenefitType::findOne($policy->benefitTypeId);
            $variant = $this->getFilledVariant($benefitType->slug ?? '');

            if ($variant) {
                $successVariant = $variant->save();

                if(!$successVariant) {
                    Craft::error(Craft::t('staff-management','The save of the Benefit Type specific Variant wasn\'t successfull'));
                }
            }

        } catch (\Exception $e) {
            Craft::error($e->getMessage(), __METHOD__);
        }
    }

}