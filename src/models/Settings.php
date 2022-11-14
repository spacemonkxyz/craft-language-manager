<?php
/**
 * @link        https://spacemonk.xyz
 * @copyright   Copyright (c) 2022 SpaceMonk
 */

namespace spacemonk\language_manager\models;

use craft\base\Model;
use spacemonk\language_manager\enums\LabelType;

/**
 * @author      SpaceMonk
 * @package     spacemonk
 * @since       0.1.0
 */
class Settings extends Model
{
    public bool $keepQueryParameters = false;
    public string $labelType = LabelType::LABEL_TYPE_CODE;

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            [['keepQueryParameters', 'labelType'], 'required'],
        ];
    }
}