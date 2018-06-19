<?php declare(strict_types=1);

namespace Shopware\Core\Content\Configuration\Struct;

use Shopware\Core\Content\Configuration\Aggregate\ConfigurationGroupOption\Collection\ConfigurationGroupOptionBasicCollection;
use Shopware\Core\Content\Configuration\Aggregate\ConfigurationGroupTranslation\Collection\ConfigurationGroupTranslationBasicCollection;

class ConfigurationGroupDetailStruct extends ConfigurationGroupBasicStruct
{
    /**
     * @var \Shopware\Core\Content\Configuration\Aggregate\ConfigurationGroupOption\Collection\ConfigurationGroupOptionBasicCollection
     */
    protected $options;

    /**
     * @var ConfigurationGroupTranslationBasicCollection
     */
    protected $translations;

    public function __construct()
    {
        $this->options = new ConfigurationGroupOptionBasicCollection();

        $this->translations = new ConfigurationGroupTranslationBasicCollection();
    }

    public function getOptions(): ConfigurationGroupOptionBasicCollection
    {
        return $this->options;
    }

    public function setOptions(ConfigurationGroupOptionBasicCollection $options): void
    {
        $this->options = $options;
    }

    public function getTranslations(): ConfigurationGroupTranslationBasicCollection
    {
        return $this->translations;
    }

    public function setTranslations(ConfigurationGroupTranslationBasicCollection $translations): void
    {
        $this->translations = $translations;
    }
}