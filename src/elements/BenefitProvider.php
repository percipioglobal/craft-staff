<?php
/**
 * staff-management plugin for Craft CMS 3.x
 *
 * Craft Staff Management provides an HR solution for payroll and benefits
 *
 * @link      http://percipio.london
 * @copyright Copyright (c) 2021 Percipio
 */

namespace percipiolondon\staff\elements;

use Craft;
use craft\base\Element;
use craft\db\Query;
use craft\elements\db\ElementQueryInterface;

use percipiolondon\staff\db\Table;
use percipiolondon\staff\elements\db\BenefitProviderQuery as ProviderQuery;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\records\BenefitProvider as ProviderRecord;

/**
 * Benefit Provder Element
 *
 * @property-read string $gqlTypeName
 */
class BenefitProvider extends Element
{
    // Public Properties
    // =========================================================================
    /**
     * @var string
     */
    public string $name = '';
    /**
     * @var string|null
     */
    public ?string $url = null;
    /**
     * @var int|null|string
     */
    public int|string|null $logo = null;
    /**
     * @var string|null
     */
    public ?string $content = null;


    // Static Methods
    // =========================================================================
    /**
     * Returns the display name of this class.
     *
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return Craft::t('staff-management', 'Benefit Provider');
    }

    /**
     * @inheritdoc
     */
    public static function lowerDisplayName(): string
    {
        return Craft::t('staff-management', 'benefit provider');
    }

    /**
     * @inheritdoc
     */
    public static function pluralDisplayName(): string
    {
        return Craft::t('staff-management', 'Benefit Providers');
    }

    /**
     * @inheritdoc
     */
    public static function pluralLowerDisplayName(): string
    {
        return Craft::t('staff-management', 'benefit providers');
    }

    /**
     * Creates an [[ElementQueryInterface]] instance for query purpose.
     *
     * The returned [[ElementQueryInterface]] instance can be further customized by calling
     * methods defined in [[ElementQueryInterface]] before `one()` or `all()` is called to return
     * populated [[ElementInterface]] instances. For example,
     *
     * @return ElementQueryInterface The newly created [[ElementQueryInterface]] instance.
     */
    public static function find(): ElementQueryInterface
    {
        return new ProviderQuery(static::class);
    }

    /**
     * @param mixed $context
     * @return string
     */
    public static function gqlTypeNameByContext($context): string
    {
        return 'BenefitProvider';
    }



    // Protected Methods
    // =========================================================================
    /**
     * Defines the sources that elements of this type may belong to.
     *
     * @param string|null $context The context ('index' or 'modal').
     *
     * @return array The sources.
     * @see sources()
     */
    protected static function defineSources(string $context = null): array
    {
        $ids = self::_getProviderIds();

        return [
            [
                'key' => '*',
                'label' => 'All Providers',
                'defaultSort' => ['id', 'desc'],
                'criteria' => ['id' => $ids],
            ],
        ];
    }

    /**
     * @return array
     */
    protected static function defineTableAttributes(): array
    {
        return [
            'name' => ['label' => Craft::t('staff-management', 'Name')],
            'url' => ['label' => Craft::t('staff-management', 'Url')],
        ];
    }


    /**
     * @param string $source
     * @return array
     */
    protected static function defineDefaultTableAttributes(string $source): array
    {
        $attributes = [];
        $attributes[] = 'name';
        $attributes[] = 'dateCreated';
        $attributes[] = 'dateUpdated';

        return $attributes;
    }

    /**
     * @inheritDoc
     */
    protected static function defineSortOptions(): array
    {
        return [
            'name' => Craft::t('staff-management', 'Name'),
            'url' => Craft::t('staff-management', 'Url'),

        ];
    }

    // Public Methods
    // =========================================================================

    /**
     * @param string $attribute
     * @return string
     * @throws InvalidConfigException
     */
    protected function tableAttributeHtml(string $attribute): string
    {
        switch ($attribute) {
            case 'name':
                return $this->name;
            case 'url':
                return $this->url;
        }

        return parent::tableAttributeHtml($attribute);
    }


    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function defineRules(): array
    {
        $rules = parent::defineRules();

        $rules[] = [['name', 'content'], 'required'];

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function getGqlTypeName(): string
    {
        return static::gqlTypeNameByContext($this);
    }

    // Events
    // -------------------------------------------------------------------------

    /**
     * Performs actions after an element is saved.
     *
     * @param bool $isNew Whether the element is brand new
     *
     */
    public function afterSave(bool $isNew): void
    {
        $this->_saveRecord();

        parent::afterSave($isNew);
    }

    /**
     * Save benefit provider record
     */
    private function _saveRecord(): void
    {
        try{
            $record = ProviderRecord::findOne($this->id);

            if(!$record) {
                $record = new ProviderRecord();
                $record->id = $this->id;
            }

            $record->name = $this->name;
            $record->logo = $this->logo;
            $record->url = $this->url;
            $record->content = $this->content;

            $record->save();

        } catch (\Exception $e) {
            $logger = new Logger();
            $logger->stdout(PHP_EOL, $logger::RESET);
            $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
            Craft::error($e->getMessage(), __METHOD__);
        }
    }


    // Private Methods
    // =========================================================================
    /**
     * Returns all employee ID's
     *
     * @return array
     */
    private static function _getProviderIds(): array
    {
        $providerIds = [];

        $providers = (new Query())
            ->from(Table::BENEFIT_PROVIDERS)
            ->select('*')
            ->all();

        foreach ($providers as $provider) {
            $providerIds[] = $provider['id'];
        }

        return $providerIds;
    }
}
