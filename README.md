# Notes

So there are a couple of notes in getting this set-up up and running on your own server:

First off I run everything inside Docker containers. I have a home-grown version of an apache/php webserver that I use. (The docker image is called obrienware/apache-webserver). This webserver only exposes port 80 (it's designed to sit behind a proxy. I also use nginx as the proxy as well as certbot to create any necessay server certificates for the app), and relies on redis for storing session information. That way I can spin up multiple instances of the web application without issue.

One more note on paths: All the classes for the application are in a folder called `/classes` (off the root folder). I have made this folder searchable in my php config such that any folder will find the class files.

I use several 3rd party APIs (for email, text messaging, weather information, geo-coding, etc.). Some are paid for and others are free-to-use. In any case, always check their respective licensing. I've made a note of these in `doc.apis.md`.

In any case, this is what my docker-compose.yaml file looks like:

```
services:

  certbot:
    image: serversideup/certbot-dns-cloudflare
    volumes:
      - ./certbot_data:/etc/letsencrypt
    environment:
      CLOUDFLARE_API_TOKEN: "Use Your Own Token"
      CERTBOT_EMAIL: "Your own email address"
      CERTBOT_DOMAIN: "Your own FQDN"
      CERTBOT_KEY_TYPE: "rsa"
  
  nginx:
    image: nginx:1.11-alpine
    platform: linux/amd64
    restart: unless-stopped
    ports:
      - 80:80
      - 443:443
      - 4000-4100:4000-4100
    volumes:
      - ./nginx/db_auth.conf:/etc/nginx/db_auth.conf:ro
      - ./nginx/nginx.conf:/etc/nginx/nginx.conf:ro
      - ./nginx/sites:/etc/nginx/sites-enabled
      - ./certbot_data:/etc/letsencrypt
    depends_on:
      - certbot

  redis:
    image: redis
    environment:
      - REDIS_PASSWORD=YOUR_REDIS_PASSWORD
    command: redis-server --appendonly yes
    volumes:
      - ./redis:/data
    restart: unless-stopped

  transport-web:
    image: obrienware/apache-webserver
    platform: linux/amd64
    environment:
      - MODE=production
      - SERVER_NAME="Your FQDN"
      - SERVER_ALIAS="Any alias to your FQDN"
      - SERVER_ROOT=/var/www/html
      - DB_USER=root
      - DB_PASS=YourOwnRootPassword
      - DB_DATABASE=transport
      - DB_HOST=mysql
      - GOOGLE_API_KEY=YourGoogleAPIKey
      - SPARKPOST_KEY=YourSparkpostKey
    volumes:
      - ./webs/transport:/var/www/html
    depends_on:
      - redis
    restart: unless-stopped

  mysql:
    image: mariadb
    environment:
      MARIADB_DATABASE: transport
      MARIADB_ROOT_PASSWORD: YourOwnRootPassword
      TZ: 'America/Denver'
    volumes:
      - ./mysql:/var/lib/mysql
    restart: unless-stopped
    # We do not need to expose any ports here
    ports:
      - 13306:3306 #but for testing/developing

  phpmyadmin:
    image: phpmyadmin/phpmyadmin:latest
    platform: linux/amd64
    restart: unless-stopped
    hostname: '127.0.0.1'
    depends_on:
      - mysql
    environment:
      PMA_HOST: mysql
      MYSQL_ROOT_PASSWORD: YourOwnRootPassword
      PMA_ABSOLUTE_URI: 'YourURI'
    # We do not need to expose any ports here
```