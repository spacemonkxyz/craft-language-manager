<?php
/**
 * @link        https://spacemonk.xyz
 * @copyright   Copyright (c) 2022 SpaceMonk
 */

namespace spacemonk\language_manager\services;

use Craft;
use craft\errors\SiteNotFoundException;
use spacemonk\language_manager\enums\LabelType;
use spacemonk\language_manager\LanguageManager;
use yii\base\Component;

/**
 * @author      SpaceMonk
 * @package     spacemonk
 * @since       0.1.0
 */
class LanguageManagerService extends Component
{
    private array $pagesForActivePage = [];

    /**
     * Get all pages for the active page in all languages
     *
     * @return array objects containing url, label, and other attributes
     * @throws SiteNotFoundException
     * @noinspection PhpUnused
     */
    public function getPagesForActivePage(): array
    {
        if ($this->pagesForActivePage) {
            return $this->pagesForActivePage;
        }
        $pluginSettings = LanguageManager::$plugin->getSettings();

        $activePage = Craft::$app->urlManager->getMatchedElement();
        if (!$activePage) {
            return [];
        }
        $allSites = Craft::$app->getSites();
        $activeSite = $allSites->getCurrentSite();
        $siteGroup = $allSites->getGroupById($activeSite->groupId);
        $sitesInGroup = $siteGroup ? $siteGroup->getSites() : [];
        $queryString = Craft::$app->getRequest()->getQueryStringWithoutPath();

        foreach ($sitesInGroup as $site) {
            $url = $site->getBaseUrl();
            if (empty($url)) {
                continue;
            }

            $fallback = false;
            $currentPage = ($site->id === $activeSite->id) ? $activePage : Craft::$app->getElements()->getElementById($activePage->id, $activePage::class, $site->id);
            if ($currentPage && $currentPage->getEnabledForSite($site->id)) {
                $url = $currentPage->getUrl();
            } else {
                $fallback = true;
            }

            $languageParts = explode('-', $site->language);
            $countryCode = $languageParts[count($languageParts) - 1];

            $this->pagesForActivePage[] = [
                'url' => $url,
                'queryParameters' => ($pluginSettings->keepQueryParameters && !empty($queryString)) ? "?$queryString" : '',
                'label' => $this->_getLabel($site->language),
                'isoCountryCode' => strtoupper($countryCode),
                'isoLanguageCode' => $site->language,
                'isActive' => $site->id === $activeSite->id,
                'isPrimarySite' => $site->primary,
                'isFallback' => $fallback
            ];
        }
        return $this->pagesForActivePage;
    }

    /**
     * Get the label for the language switcher for the frontend for the specified language.
     *
     * @param string $language language string (e.g. 'en' or 'en-US')
     * @return string label for the language
     */
    private function _getLabel(string $language): string
    {
        $languageParts = explode('-', $language);
        $languageCode = $languageParts[0];

        $translationLanguage = LanguageManager::$plugin->getSettings()->showLanguageInThatLanguage === true ? $language : Craft::$app->language;

        return match (LanguageManager::$plugin->getSettings()->labelType) {
            LabelType::LABEL_TYPE_CUSTOM => Craft::t(LanguageManager::$plugin->handle, $language),
            LabelType::LABEL_TYPE_NAME => Craft::$app->getI18n()->getLocaleById($languageCode)->getDisplayName($translationLanguage),
            LabelType::LABEL_TYPE_NAME_LONG => Craft::$app->getI18n()->getLocaleById($language)->getDisplayName($translationLanguage),
            LabelType::LABEL_TYPE_CODE_LONG => strtoupper($language),
            default => strtoupper($languageCode),
        };
    }
}