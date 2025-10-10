# 703bakehouse
A web application for my sister's potential small business bakery

https://www.703bakehouse.com


## Developing Locally
The dev branch is meant to be tested locally using Docker and a .env file to test all website functionality

### .env File
In the project root, create a .env file with the following format
```
AWS_KEY=abc
AWS_SECRET_KEY=xyz
STRIPE_SECRET_KEY=sk_abc
```

### Docker
The docker files are already commited in the dev branch, just run
```
docker-compose up -d
```
And then to end the container
```
docker-compose down
```

### Composer
To add or remove any packages from Composer, update the composer.json file, and run
```
composer update
```
And then just push the updated composer.json and composer.lock files, it will be rebuilt in lightsail on push to prod
