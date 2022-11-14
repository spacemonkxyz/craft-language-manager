<?php
/**
 * @link        https://spacemonk.xyz
 * @copyright   Copyright (c) 2022 SpaceMonk
 */

namespace spacemonk\language_manager;

use Craft;
use craft\base\Model;
use craft\base\plugin;
use craft\events\RegisterTemplateRootsEvent;
use craft\i18n\PhpMessageSource;
use craft\web\View;
use spacemonk\language_manager\enums\LabelType;
use spacemonk\language_manager\models\Settings;
use spacemonk\language_manager\services\LanguageManagerService;
use spacemonk\language_manager\twigextensions\LanguageManagerTwigExtension;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\Event;
use yii\base\Exception;

/**
 * @author      SpaceMonk
 * @package     spacemonk
 * @since       0.1.0
 *
 * @property Settings $settings
 * @method Settings getSettings()
 */
class LanguageManager extends Plugin
{
    public static LanguageManager $plugin;

    public bool $hasCpSettings = true;

    /**
     * @inheritDoc
     */
    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        $this->_registerServices();
        $this->_registerTranslations();
        $this->_registerTemplateRoots();
        $this->_registerHooks();
        $this->_registerTwigExtensions();
    }

    /**
     * Register the services for this plugin
     *
     * @return void
     */
    private function _registerServices(): void
    {
        $this->setComponents([
            'languageManagerService' => LanguageManagerService::class
        ]);
    }

    /**
     * Register the translations for this plugin
     *
     * @return void
     */
    private function _registerTranslations(): void
    {
        Craft::$app->i18n->translations[$this->handle] = [
            'class' => PhpMessageSource::class,
            'basePath' => __DIR__ . '/translations',
            'allowOverrides' => true,
        ];
    }

    /**
     * Register the template roots for this plugin
     *
     * @return void
     */
    private function _registerTemplateRoots(): void
    {
        Event::on(
            View::class,
            View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS,
            static function (RegisterTemplateRootsEvent $event) {
                $event->roots['languageManager'] = __DIR__ . '/templates';
            }
        );
    }

    /**
     * Register the hooks for this plugin
     *
     * @return void
     */
    private function _registerHooks(): void
    {
        Craft::$app->view->hook('languageManagerHreflang', function (array $context) {
            $templatePath = 'languageManager/_frontend/hreflang.twig';
            return Craft::$app->view->renderTemplate($templatePath, $context, Craft::$app->view::TEMPLATE_MODE_SITE);
        });
        Craft::$app->view->hook('languageManagerNavigation', function (array $context) {
            $templatePath = 'languageManager/_frontend/language-navigation.twig';
            return Craft::$app->view->renderTemplate($templatePath, $context, Craft::$app->view::TEMPLATE_MODE_SITE);
        });
    }

    /**
     * Register the Twig extensions for this plugin
     *
     * @return void
     */
    private function _registerTwigExtensions(): void
    {
        $request = Craft::$app->request;
        if ($request->getIsSiteRequest() && !$request->getIsCpRequest()) {
            Craft::$app->view->registerTwigExtension(new LanguageManagerTwigExtension());
        }
    }

    /**
     * @inheritDoc
     */
    protected function createSettingsModel(): ?Model
    {
        return new Settings();
    }

    /**
     * @inheritDoc
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    protected function settingsHtml(): ?string
    {
        // Get and pre-validate the settings
        $settings = $this->getSettings();
        $settings->validate();

        // Get the settings that are being defined by the config file
        $overrides = Craft::$app->getConfig()->getConfigFromFile(strtolower($this->handle));

        // Get the label type options
        $labelTypeOptions = LabelType::getLabelTypeOptionsForControlPanel();

        return Craft::$app->getView()->renderTemplate(
            $this->handle . '/_settings/settings.twig',
            [
                'settings' => $settings,
                'overrides' => array_keys($overrides),
                'labelTypeOptions' => $labelTypeOptions
            ]
        );
    }
}