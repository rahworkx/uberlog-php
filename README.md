uberlog-php
==========

This is a client library to interact with UberLog.

**Requirements:**

- Composer


Installing
-----------

Add the following entry to your composer.json under the key "repositories"
```json
{
"type": "vcs",
"url": "https://github.com/KSCTECHNOLOGIES/uberlog-php.git"
}

```
Add the following entry to your composer.json under the key "require"
```json
"kreate_technology/uberlog-php":"dev-master"
```

After you added the entries above to your composer.json, you can run:
```shell
composer install
```

Using
-----------

Include composer's autoload on your file and you can use:
```php
UberLog\Log::info("mycategory","myslug");
UberLog\Log::info("mycategory","myslug", array("user_id" => 123));
```
You have the following methods available staticaly on the class UberLog\Log:
```php
public static function info($category, $slug, $extra_info_array=array());
public static function debug($category, $slug, $extra_info_array=array());
public static function success($category, $slug, $extra_info_array=array());
public static function error($category, $slug, $extra_info_array=array());
```
