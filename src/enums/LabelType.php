<?php
/**
 * @link        https://spacemonk.xyz
 * @copyright   Copyright (c) 2022 SpaceMonk
 */

namespace spacemonk\language_manager\enums;

use Craft;
use spacemonk\language_manager\LanguageManager;

/**
 * @author      SpaceMonk
 * @package     spacemonk
 * @since       0.1.0
 */
abstract class LabelType
{
    public const LABEL_TYPE_CODE = 'code';
    public const LABEL_TYPE_CODE_LONG = 'code-long';
    public const LABEL_TYPE_NAME = 'name';
    public const LABEL_TYPE_NAME_LONG = 'name-long';
    public const LABEL_TYPE_CUSTOM = 'custom';

    private const TRANSLATION_KEY_PREFIX = 'label-type-';

    /**
     * Get all label type options for the settings in the control panel
     *
     * @return array label type options
     */
    public static function getLabelTypeOptionsForControlPanel(): array
    {
        $labelTypes = self::_getAllLabelTypes();
        $labelTypeOptions = [];
        foreach ($labelTypes as $labelType) {
            $labelTypeOptions[] = [
                'label' => Craft::t(LanguageManager::$plugin->handle, self::_getLabelTypeTranslationKey($labelType)),
                'value' => $labelType
            ];
        }
        return $labelTypeOptions;
    }

    /**
     * Get the translation key for the specified label type
     *
     * @param string $labelType the label type
     * @return string the translation key
     */
    private static function _getLabelTypeTranslationKey(string $labelType): string
    {
        return self::TRANSLATION_KEY_PREFIX . $labelType;
    }

    /**
     * Get an array of all label types
     *
     * @return string[] all label types
     */
    private static function _getAllLabelTypes(): array
    {
        return [self::LABEL_TYPE_CODE, self::LABEL_TYPE_CODE_LONG, self::LABEL_TYPE_NAME, self::LABEL_TYPE_NAME_LONG, self::LABEL_TYPE_CUSTOM];
    }
}