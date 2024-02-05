# Unleashed resmush.it

> *Unleashed* reSmush.it is a *self-hosted* FREE API that provides image optimization. *Unleashed* reSmush.it has been implemented on the most common CMS such as Wordpress, Drupal or Magento.
> Moreover, *Unleashed* reSmush.it is the most used image optimization API with more than 26 billions images already treated, and is still totally Free of charge!



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

A cronjob has to be defined every 15 minutes to run the `cronjob.php` file. This script will remove files older than 15 minutes (defined with variable `EXPIRES`) based on their modification time. The use of RAMFS will speed up this process. Simply run CRON task from host like `*/15 * * * * docker exec -it <container> php /var/www/html/cronjob.php`

### Wotking with API

All methods are accepted with POST or GET HTTP verbs

- `img` : internet-accessible url of the picture to optimize
- `qlty` : factor of optimization, used by JPG optimization from 0 (weakest) to 100 (best quality). Default value is `92`
- `format` : to force output format. Accepted values are `webp` of `avif`. Default value is `NULL` 
- `exif` : to preserve EXIF data. Accepted values are `true` of `false`. Default value is `false` so that EXIF data are stripped.  
- `key` : (for internal purposes), with a valid key defined in the variable `REMOTE_KEY_FULL_RESPONSE` the returned payload contains a more detailed feedback including gentime. 

### Accepted file formats

Accepted image files format as input are :
- jpg/jpeg
- gif
- png
- tif/tiff
- bmp
- webp

defined in the variable `$_AUTHORIZED_EXTENSIONS` 

## Optimization methods

The optimization process uses C/C++ linux libraries (`optipng`, `pngquant` and `jpegoptim`) as this is the most efficient way of doint a conversion (less CPU consumption, fastest time of execution). Most recently (~2020) the `webp` support has been added natively to the API by the use of the library `cwebp`. The same has been performed with `avif` through the library `avif`. However, this late library is ressource-consuming (and time consuming) and needs higher GPU workers, incompatible for now with a free offer.

The `exif` data are removed by default to reduce file size, but they can be preserved by the use of the query parameter `exif` set to `true`

### Plugins, integration modules and 3rd-party code

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

### CLI / Bash

In the file [resmushit-cli.sh](https://github.com/charlyie/resmushit-cli/blob/master/resmushit-cli.sh) update `API_URL` variable

```diff
- API_URL="http://api.resmush.it"
+ API_URL="https://YOUR_DOMAIN"
```