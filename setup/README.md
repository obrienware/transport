# Upload folders:

In some cases, the underlying OS doen't allow the application to create the `documents` folder. In this case you may need to create it manually (viz. `mkdir documents`), and give it the permissions it needs. The easiest way is: `chown -R www-data:www-data documents`