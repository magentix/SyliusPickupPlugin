## Notes

* This Plugin is a generic plugin allows to add pickup delivery
* See [Pickup Demo Plugin](https://github.com/magentix/pickup-demo-plugin) for an example of implementation

## Installation

```bash
$ composer require magentix/pickup-plugin
```
    
Add plugin dependencies to your `AppKernel.php` file:

```php
public function registerBundles()
{
    $bundles = [
        ...
        new \MagentixPickupPlugin\MagentixPickupPlugin(),
    ];
}
```

Import required config in your `app/config/config.yml` file:

```yaml
# app/config/config.yml

imports:
    ...   
    - { resource: "@MagentixPickupPlugin/Resources/config/config.yml" }
```

Import routing in your `app/config/routing.yml` file:

```yaml
# app/config/routing.yml
...

magentix_pickup_plugin:
    resource: "@MagentixPickupPlugin/Resources/config/routing.yml"
```

Deploy Assets:

```bash
php bin/console sylius:theme:assets:install