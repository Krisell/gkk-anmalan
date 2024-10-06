This pull request contains changes for _slimming_ your Laravel application by removing core files which are no longer included in a new Laravel application.

**Before merging**, you need to:

- Checkout the `shift` branch
- Review **all** comments for additional changes
- Thoroughly test your application ([no tests?](https://laravelshift.com/laravel-test-generator), [no CI?](https://laravelshift.com/ci-generator))

If you need help modernizing your Laravel application, check out the [Human Shifts](https://laravelshift.com/human-shifts).


1. :information_source: To slim down the Laravel installation, Laravel 11 no longer has most of the core files previously included in the default Laravel application. While you are welcome to publish and customize these files, they are no longer required.

Shift takes an iterative approach to removing core files which are not customized or where its customizations may be done elsewhere in a modern Laravel 11 application. As such, you may see some commits removing files and others _re-registering_ your customizations.

2. :x: Shift was unable to find some of your controllers and left them as string references in your routes.

You will need to search for the following controller names in your routes and replace them with the appropriate static `::class` reference manually.

- [ ] adminIndex
- [ ] loadExam
- [ ] loginSuperadmin

3. :information_source: Laravel 11 no longer requires you to maintain the default configuration files. Your configuration [now merges with framework defaults](https://laravel-news.com/laravel11-streamlined-configs). 

Shift [streamlined your configuration files]({#commit:5bb15093400d98997e4c7ab934db1dcf83978942}) by removing options that matched the Laravel defaults and preserving your _true customizations_. These are values which are not changeable through `ENV` variables.

If you wish to keep the full set of configuration files, Shift recommends running `artisan config:publish --all --force` to get the latest configuration files from Laravel 11, then reapplying the customizations Shift streamlined.

4. :information_source: Shift detected customized options within your configuration files which may be set with an `ENV` variable. To help keep your configuration files streamlined, you may set the following variables. Be sure adjust any values per environment.

```txt
ARGON_MEMORY=1024
ARGON_THREADS=2
ARGON_TIME=2
BCRYPT_ROUNDS=10
CACHE_STORE=file
DB_CONNECTION=mysql
MAIL_FROM_ADDRESS=noreply@gkk-styrkelyft.se
MAIL_FROM_NAME=GKK
MAIL_MAILER=smtp
QUEUE_CONNECTION=sync
QUEUE_FAILED_DRIVER=database
SESSION_DRIVER=cookie
SQS_QUEUE=your-queue-name
```

**Note:** some of these may simply be values which changed between Laravel versions. You may ignore any `ENV` variables you do not need to customize.

5. :information_source: Shift detected your application uses custom `ENV` variables for configuration options which have `ENV` variables in Laravel. Shift recommends renaming the following variables to use the one provided by Laravel:

- [ ] `FILESYSTEM_DRIVER` to `FILESYSTEM_DISK`

6. :warning: The `BROADCAST_DRIVER`, `CACHE_DRIVER`, and `DATABASE_URL` environment variables were renamed in Laravel 11 to `BROADCAST_CONNECTION`, `CACHE_STORE`, and `DB_URL`, respectively.

Shift [automated this change]({#commit:326d033d1be0f217524f05ea80d9e7c98b0f7322}) for your committed files, but you should review any additional locations where your environment is configured and update to the new variable names.

7. :information_source: Laravel 11 now updates the timestamp when publishing vendor migrations. This may cause problems in existing applications when these migrations were previously published and ran with their default timestamp.

To preserve the original behavior, Shift disabled this feature in your `database.php` configuration file. If you do not have any vendor migrations or have squashed all of your existing migrations, you may re-enable the `update_date_on_publish` option. If this is the only customization within `database.php`, you may remove this configuration file.

8. :information_source: The following files previously included in a Laravel application appeared to be customized and were not removed. Shift encourages you to review your customizations to see if they are still needed or may be done elsewhere in a modern Laravel application. Removing these files will keep your application modern and make future maintenance easier.

- [ ] app/Http/Middleware/TrustProxies.php
- [ ] app/Providers/RouteServiceProvider.php

9. :warning: Many of the default drivers changed in Laravel 11. For example, the default database driver is `sqlite` and the default cache store is `database`. If you experience errors setting up your environment, be sure you have properly set your `ENV` variables for these drivers. If you wish to adopt the new defaults, you may follow the documentation to set them up for your application.