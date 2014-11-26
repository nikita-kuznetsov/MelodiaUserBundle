#MelodiaUserBundle

##Installation

Step 1: Download the Bundle
---------------------------

```json
// composer.json
"scripts": {
  "post-install-cmd": [
    "Incenteev\\ParameterHandler\\ScriptHandler::buildParameters",
    "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::buildBootstrap",

    "Melodia\\UserBundle\\Composer\\ScriptHandler:generateSSHKeys",
    // ...
  ],
  "post-update": [
    "Incenteev\\ParameterHandler\\ScriptHandler::buildParameters",
    "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::buildBootstrap",

    "Melodia\\UserBundle\\Composer\\ScriptHandler:generateSSHKeys",
    // ...
  ],
}
// ...
"repositories": [
  {
    "type": "vcs",
    "url": "https://github.com/melodia/MelodiaUserBundle.git"
  }
]
```

```bash
$ composer require "melodia/user-bundle" "dev-master"
```

Step 2: Enable the Bundle
-------------------------

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Melodia\UserBundle\MelodiaUserBundle(),
        );

        // ...
    }

    // ...
}
```

Step 3: Configure dependencies of the Bundle
------------------------------------------------

```yaml
# app/config/parameters.yml

parameters:
    ...
    ssh_key.passphrase: YourPassPhrase
    ssh_keys_dir: app/var/jwt


# app/config/config.yml

lexik_jwt_authentication:
    private_key_path:   %kernel.root_dir%/var/jwt/private.pem
    public_key_path:    %kernel.root_dir%/var/jwt/public.pem
    pass_phrase:        %ssh_key.passphrase%
    token_ttl:          86400

# Common configuration for all Melodia API bundles

nelmio_api_doc: ~

jms_serializer:
    metadata:
        auto_detection: true
    property_naming:
        separator:  ~
        lower_case: false

fos_rest:
    param_fetcher_listener: force
    view:
        view_response_listener: force
        jsonp_handler: ~
    serializer:
        serialize_null: true
    routing_loader:
        default_format: json
        include_format: false
    format_listener:
      rules:
        - { path: '^/api', priorities: ['json'] }
        - { path: '^/', priorities: ['html'] }

stof_doctrine_extensions:
    orm:
        default:
            softdeleteable: true
```

Step 4: Import API router
-------------------------

```yaml
# app/config/routing.yml

melodia_user_api_:
    resource: "@MelodiaUserBundle/Resources/config/routing/api.yml"
    prefix:   /api
```
