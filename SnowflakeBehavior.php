<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\snowflake;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\BaseActiveRecord;
use yii\behaviors\AttributeBehavior;

/**
 * Class SnowflakeBehavior
 * @package xutl\snowflake
 */
class SnowflakeBehavior extends AttributeBehavior
{
    /**
     * @var string
     */
    public $attribute = 'id';

    /**
     * @inheritdoc
     */
    public $value;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (empty($this->attributes)) {
            $this->attributes = [BaseActiveRecord::EVENT_BEFORE_INSERT => [$this->attribute],];
        }

        if ($this->attribute === null) {
            throw new InvalidConfigException('Either "attribute" property must be specified.');
        }
    }

    /**
     * @inheritdoc
     */
    protected function getValue($event)
    {
        if ($this->value === null) {
            return Yii::$app->snowflake->next();
        }
        return parent::getValue($event);
    }
}