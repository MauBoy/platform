<?php declare(strict_types=1);

namespace Shopware\Api\Context\Struct;

use Shopware\Api\Language\Struct\LanguageBasicStruct;

class ContextCartModifierTranslationDetailStruct extends ContextCartModifierTranslationBasicStruct
{
    /**
     * @var ContextCartModifierBasicStruct
     */
    protected $contextCartModifier;

    /**
     * @var LanguageBasicStruct
     */
    protected $language;

    public function getContextCartModifier(): ContextCartModifierBasicStruct
    {
        return $this->contextCartModifier;
    }

    public function setContextCartModifier(ContextCartModifierBasicStruct $contextCartModifier): void
    {
        $this->contextCartModifier = $contextCartModifier;
    }

    public function getLanguage(): LanguageBasicStruct
    {
        return $this->language;
    }

    public function setLanguage(LanguageBasicStruct $language): void
    {
        $this->language = $language;
    }
}