<?php
/**
 * @link        https://spacemonk.xyz
 * @copyright   Copyright (c) 2022 SpaceMonk
 */

namespace spacemonk\language_manager\twigextensions;

use spacemonk\language_manager\LanguageManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author      SpaceMonk
 * @package     spacemonk
 * @since       0.1.0
 */
class LanguageManagerTwigExtension extends AbstractExtension
{
    /**
     * Returns the name of the Twig extension
     *
     * @return string extension name
     */
    public function getName(): string
    {
        return 'Language Manager';
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('getLanguageManagerPages', [LanguageManager::$plugin->languageManagerService, 'getPagesForActivePage'])
        ];
    }
}