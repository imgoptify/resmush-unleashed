# Unleashed resmush.it

> reSmush.it is a FREE API that provides image optimization. reSmush.it has been implemented on the most common CMS such as Wordpress, Drupal or Magento.
> Moreover, reSmush.it is the most used image optimization API with more than 26 billions images already treated, and is still totally Free of charge!

This repository and the code in it allows you to set up your resmush instance on your infrastructure using a modern approach. It will be entirely your infrastructure, no vendor lock-in to external third party systems, dependent on payment by the owner or wanting to block access from some country for example, although silly, right? :)

I'd also like to mention that there is full backwards compatibility with the resmush API, so all you need to do is replace the endpoint from `api.resmush.it` to your own domain in the integration code you use.

## Unleashed version? What is it?

This version of reSmush.it is called "Unleashed" because

- here is a full source code, so you can run this API yourself on your server and get full control over it
- There are increased limits of uploaded image (from 5MB to 25MB, or you can increase it here `config/webservice.ini.php:23` with the `MAX_FILESIZE` parameter or change param `ENV_MAX_FILESIZE` in `docker-compose.yml` file).
- removed analytics :)

## Getting Started

- Clone repo to your server like `git clone https://github.com/imgoptify/resmush-unleashed.git`
- Make necessary changes in `docker-compose.yml` file
- Run `docker compose up -d`

### CRON

Wanna remove processed images? Simply run CRON task from host like `*/15 * * * * docker exec -it <container> php /var/www/html/cronjob.php`

## How I should update my code to make it work with my instance?

All you need to do is find the API endpoint URL (domain `api.resmush.it`) from resmush and replace it with yours. Below are the changes for the most popular plugins, which you can use as an example to make changes to your own module or implementation.

By the way, you can increase the security level of your project by using your instance with SSL and replacing the URL with `https://`. All implementations of this API that currently exist use the insecure `http://`.

### Wordpress

Update the defined variable `RESMUSHIT_ENDPOINT` in the file `resmushit.settings.php`

```diff
- define('RESMUSHIT_ENDPOINT', 'http://api.resmush.it/');
+ define('RESMUSHIT_ENDPOINT', 'https://YOUR_DOMAIN/');
```

### Laravel

Update the constant `ENDPOINT` in the file `src/ReSmushIt.php`

```diff
- private const ENDPOINT = 'http://api.resmush.it/';
+ private const ENDPOINT = 'https://YOUR_DOMAIN/';
```

### Drupal

In the `src/Plugin/ImageAPIOptimizeProcessor/ReSmushit.php` file, update the hardcoded URL according to the following code

```diff
- $response = $this->httpClient->post('http://api.resmush.it/ws.php', ['multipart' => $fields]);
+ $response = $this->httpClient->post('https://YOUR_DOMAIN/ws.php', ['multipart' => $fields]);
```
