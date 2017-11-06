# yii2-snowflake

适用于 Yii2 的 雪花算法ID生成器。Based on the Twitter Snowflake algorithm.

[![Latest Stable Version](https://poser.pugx.org/xutl/yii2-snowflake/v/stable.png)](https://packagist.org/packages/xutl/yii2-snowflake)
[![Total Downloads](https://poser.pugx.org/xutl/yii2-snowflake/downloads.png)](https://packagist.org/packages/xutl/yii2-snowflake)
[![Reference Status](https://www.versioneye.com/php/xutl:yii2-snowflake/reference_badge.svg)](https://www.versioneye.com/php/xutl:yii2-snowflake/references)
[![Build Status](https://img.shields.io/travis/xutl/yii2-snowflake.svg)](http://travis-ci.org/xutl/yii2-snowflake)
[![Dependency Status](https://www.versioneye.com/php/xutl:yii2-snowflake/dev-master/badge.png)](https://www.versioneye.com/php/xutl:yii2-snowflake/dev-master)
[![License](https://poser.pugx.org/xutl/yii2-snowflake/license.svg)](https://packagist.org/packages/xutl/yii2-snowflake)


Installation
------------

Next steps will guide you through the process of installing using [composer](http://getcomposer.org/download/). Installation is a quick and easy three-step process.

### Step 1: Install component via composer

Either run

```
composer require --prefer-dist xutl/yii2-snowflake
```

or add

```json
"xutl/yii2-snowflake": "~1.0.0"
```

to the `require` section of your composer.json.

### Step 2: Configuring your application

Add following lines to your main configuration file:

```php
'components' => [
    'snowflake' => [
        'class' => 'xutl\snowflake\Snowflake',
        'workerId' => 0,
        'dataCenterId' => 0,
    ],
],
```

### Step 3: Configuring your Model Behavior

```php
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'snowflake'=>[
                'class' => 'xutl\snowflake\SnowflakeBehavior',
                'attribute' => 'id',
            ],
        ];
    }
```

## License

This is released under the MIT License. See the bundled [LICENSE.md](LICENSE.md)
for details.
