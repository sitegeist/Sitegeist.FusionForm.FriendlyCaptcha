<?php
declare(strict_types=1);

namespace Sitegeist\FusionForm\FriendlyCaptcha\Eel;

use Neos\Eel\ProtectedContextAwareInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\I18n\Service as LocalizationService;

class FriendlyCaptchaHelper implements ProtectedContextAwareInterface
{
    /**
     * @Flow\Inject
     * @var LocalizationService
     */
    protected $localizationService;

    public function currentLocaleLanguage(): ?string
    {
        return $this->localizationService->getConfiguration()->getCurrentLocale()->getLanguage();
    }

    public function allowsCallOfMethod($methodName): bool
    {
        return true;
    }
}
