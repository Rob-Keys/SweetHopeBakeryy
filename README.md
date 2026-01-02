# SweetHopeBakeryy
A web application for my sister's potential small business bakery

https://www.sweethopebakeryy.com


## Developing Locally
The dev branch is meant to be tested locally using Docker and a .env file to test all website functionality

### .env File
In the project root, create a .env file with the following format:
```
AWS_KEY=abc
AWS_SECRET_KEY=xyz
STRIPE_SECRET_KEY=sk_abc
STRIPE_PUBLIC_KEY=pk_xyz
CAROLINE_EMAIL_ADDRESS=address@gmail.com
```

### Docker
The docker files are already commited in the dev branch.
Run once for the first time or after updating Dockerfile:
```
docker build .
```
Run everytime to spin up the container:
```
docker-compose up -d
```
And then to end the container:
```
docker-compose down
```

### Composer
To add or remove any packages from Composer, update the composer.json file, and run:
```
composer update
```
And then just push the updated composer.json and composer.lock files, it will be rebuilt in lightsail on push to prod.
If you need to create the vendor/ directory locally for some reason:
```
composer install
```

### Developing on Windows
Due to Windows poor I/O speeds, it is HIGHLY RECOMMENDED to use WSL to run docker. Cloning the repo to the ~/ root and not within the windows mnt/ system.
To open a VS code window:
```
wsl
cd ~/SweetHopeBakeryy
code .
```