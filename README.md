## Notes

* This Plugin is a generic plugin allows to add pickup delivery
* See [Pickup Demo Plugin](https://github.com/magentix/SyliusPickupDemoPlugin) for an example of implementation

## Installation

```bash
$ composer require magentix/sylius-pickup-plugin
```
    
Add the plugin to the config/bundles.php file:

```php
Magentix\SyliusPickupPlugin\MagentixSyliusPickupPlugin::class => ['all' => true],
```

Add the plugin's config to by creating the file config/packages/magentix_sylius_pickup_plugin.yaml with the following content:

```yaml
imports:
    - { resource: "@MagentixSyliusPickupPlugin/Resources/config/config.yml" }
```

Add the plugin's routing by creating the file config/routes/magentix_sylius_pickup_plugin.yaml with the following content:

```yaml
magentix_sylius_pickup_plugin:
    resource: "@MagentixSyliusPickupPlugin/Resources/config/routing.yml"
```

Finish the installation by updating the database schema and installing assets:

```bash
bin/console doctrine:migrations:diff
bin/console doctrine:migrations:migrate
bin/console assets:install
bin/console sylius:theme:assets:install