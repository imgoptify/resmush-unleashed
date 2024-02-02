# Unleashed resmush.it

> reSmush.it is a FREE API that provides image optimization. reSmush.it has been implemented on the most common CMS such as Wordpress, Drupal or Magento.
> Moreover, reSmush.it is the most used image optimization API with more than 26 billions images already treated, and is still totally Free of charge!

This repository and the code in it allows you to set up your resmush instance on your infrastructure using a modern approach. It will be entirely your infrastructure, no vendor lock-in to external third party systems, dependent on payment by the owner or wanting to block access from some country for example, although silly, right? :)

I'd also like to mention that there is full backwards compatibility with the resmush API, so all you need to do is replace the endpoint from `https://api.resmush.it/` in the integration module you use with your domain.

## Unleashed version? What is it?

This version of reSmush.it is called "Unleashed" because

- here is a full source code, so you can run this API yourself on your server and get full control over it
- There are increased limits of uploaded image (from 5MB to 25MB, or you can increase it here `config/webservice.ini.php:29` with the `RESMUSH_MAX_FILESIZE` parameter).
- removed analytics :)