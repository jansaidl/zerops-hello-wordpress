# Zerops Hello WordPress

```yaml
#yamlPreprocessor=on
project:
  name: zerops-hello-wordpress
  tags:
    - hello
    - nonha
    - wordpress

services:
  - hostname: prodapp
    type: php-nginx@8.1+1.22
    buildFromGit: https://github.com/fxck/zerops-hello-wordpress
    enableSubdomainAccess: true
    envSecrets:
      WORDPRESS_TITLE: WordPress on Zerops
      WORDPRESS_URL: ${zeropsSubdomain}

      WORDPRESS_ADMIN_EMAIL: your@email.com
      WORDPRESS_ADMIN_USER: admin
      WORDPRESS_ADMIN_PASSWORD: <@generateRandomString(<8>)>

      WORDPRESS_DEBUG: "false"
      WORDPRESS_DEBUG_DISPLAY: "false"
      WORDPRESS_DEBUG_LOG: "false"

      WORDPRESS_DB_HOST: ${db_hostname}
      WORDPRESS_DB_NAME: ${db_hostname}-prod
      WORDPRESS_DB_PASSWORD: ${db_password}
      WORDPRESS_DB_USER: ${db_user}
      WORDPRESS_TABLE_PREFIX: wp_

      WORDPRESS_STORAGE_ACCESS_KEY: ${storage_secretAccessKey}
      WORDPRESS_STORAGE_BUCKET: ${storage_bucketName}
      WORDPRESS_STORAGE_KEY_ID: ${storage_accessKeyId}
      WORDPRESS_STORAGE_URL: ${storage_apiUrl}

      WORDPRESS_AUTH_KEY: <@generateRandomString(<64>)>
      WORDPRESS_AUTH_SALT: <@generateRandomString(<64>)>
      WORDPRESS_LOGGED_IN_KEY: <@generateRandomString(<64>)>
      WORDPRESS_LOGGED_IN_SALT: <@generateRandomString(<64>)>
      WORDPRESS_NONCE_KEY: <@generateRandomString(<64>)>
      WORDPRESS_NONCE_SALT: <@generateRandomString(<64>)>
      WORDPRESS_SECURE_AUTH_KEY: <@generateRandomString(<64>)>
      WORDPRESS_SECURE_AUTH_SALT: <@generateRandomString(<64>)>

      WORDPRESS_REDIS_USER_SESSION_HOST: ${redis_hostname}
    minContainers: 1
    nginxConfig: |-
      server {
          listen 80;
          listen [::]:80;

          server_name _;  # Consider specifying your actual domain here if possible

          # Be sure that you set up a correct document root!
          # Assuming WordPress is installed directly under /var/www/public
          root /var/www;

          index index.php index.html index.htm;  # Ensure Nginx looks for index.php first

          location / {
              # First attempt to serve request as file, then as directory, then fall back to index.php
              try_files $uri $uri/ /index.php?$args;
          }

          # Pass PHP scripts to FastCGI server
          location ~ \.php$ {
              include snippets/fastcgi-php.conf;
              # With php-fpm (or other unix sockets):
              fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
              # With php-cgi (or other tcp sockets):
              # fastcgi_pass 127.0.0.1:9000;

              fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
              include fastcgi_params;
          }

          # Deny access to sensitive files
          location ~ /\.ht {
              deny all;
          }

          # Optimize serving of static files
          location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
              expires max;
              log_not_found off;
          }

          access_log syslog:server=unix:/dev/log,facility=local1 default_short;
          error_log syslog:server=unix:/dev/log,facility=local1;
      }

  - hostname: devapp
    type: php-nginx@8.1+1.22
    buildFromGit: https://github.com/fxck/zerops-hello-wordpress
    enableSubdomainAccess: true
    envSecrets:
      WORDPRESS_TITLE: WordPress on Zerops
      WORDPRESS_URL: ${zeropsSubdomain}

      WORDPRESS_ADMIN_EMAIL: your@email.com
      WORDPRESS_ADMIN_USER: admin
      WORDPRESS_ADMIN_PASSWORD: <@generateRandomString(<8>)>

      WORDPRESS_DEBUG: "false"
      WORDPRESS_DEBUG_DISPLAY: "false"
      WORDPRESS_DEBUG_LOG: "false"

      WORDPRESS_DB_HOST: ${db_hostname}
      WORDPRESS_DB_NAME: ${db_hostname}-dev
      WORDPRESS_DB_PASSWORD: ${db_password}
      WORDPRESS_DB_USER: ${db_user}
      WORDPRESS_TABLE_PREFIX: wp_

      WORDPRESS_STORAGE_ACCESS_KEY: ${storage_secretAccessKey}
      WORDPRESS_STORAGE_BUCKET: ${storage_bucketName}
      WORDPRESS_STORAGE_KEY_ID: ${storage_accessKeyId}
      WORDPRESS_STORAGE_URL: ${storage_apiUrl}

      WORDPRESS_AUTH_KEY: <@generateRandomString(<64>)>
      WORDPRESS_AUTH_SALT: <@generateRandomString(<64>)>
      WORDPRESS_LOGGED_IN_KEY: <@generateRandomString(<64>)>
      WORDPRESS_LOGGED_IN_SALT: <@generateRandomString(<64>)>
      WORDPRESS_NONCE_KEY: <@generateRandomString(<64>)>
      WORDPRESS_NONCE_SALT: <@generateRandomString(<64>)>
      WORDPRESS_SECURE_AUTH_KEY: <@generateRandomString(<64>)>
      WORDPRESS_SECURE_AUTH_SALT: <@generateRandomString(<64>)>

      WORDPRESS_REDIS_USER_SESSION_HOST: ${redis_hostname}
    minContainers: 1
    maxContainers: 1
    nginxConfig: |-
      server {
          listen 80;
          listen [::]:80;

          server_name _;  # Consider specifying your actual domain here if possible

          # Be sure that you set up a correct document root!
          # Assuming WordPress is installed directly under /var/www/public
          root /var/www;

          index index.php index.html index.htm;  # Ensure Nginx looks for index.php first

          location / {
              # First attempt to serve request as file, then as directory, then fall back to index.php
              try_files $uri $uri/ /index.php?$args;
          }

          # Pass PHP scripts to FastCGI server
          location ~ \.php$ {
              include snippets/fastcgi-php.conf;
              # With php-fpm (or other unix sockets):
              fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
              # With php-cgi (or other tcp sockets):
              # fastcgi_pass 127.0.0.1:9000;

              fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
              include fastcgi_params;
          }

          # Deny access to sensitive files
          location ~ /\.ht {
              deny all;
          }

          # Optimize serving of static files
          location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
              expires max;
              log_not_found off;
          }

          access_log syslog:server=unix:/dev/log,facility=local1 default_short;
          error_log syslog:server=unix:/dev/log,facility=local1;
      }

  - hostname: storage
    type: object-storage
    objectStorageSize: 2
    pririty: 10

  - hostname: redis
    type: keydb@6
    mode: NON_HA
    priority: 10

  - hostname: db
    type: mariadb@10.6
    mode: NON_HA
    priority: 10

  - hostname: adminer
    type: php-apache@8.0+2.4
    buildFromGit: https://github.com/zeropsio/recipe-adminer@main
    enableSubdomainAccess: true
    minContainers: 1
    maxContainers: 1
    priority: 10
```
