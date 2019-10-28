###I. Install

```bash
git clone https://github.com/kuzovkov/yii-tpl mysite

cd mysite

./install-docker.sh #if docker is not installed

#build container
sudo docker-compose build

#run
sudo docker-compose up -d
#checking:
sudo docker-compose ps
http://localhost:8005/info.php

#download and unpack yii
./get-yii.sh

#install
```

Open `basic/config/web.php` and insert `cookieValidationKey` value:
```php
$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'sqshqishqiushiqw',
        ],
        'cache' => [ 
``` 
http://localhost:8005/
```
Please use `dbname`, `dbuser`, `dbpass` from `docker-compose.yml`.

Set `db` as `dbhost`

###II. Developing

```bash
#dump database
./dbdump.sh
```

```bash
#recovery database
./dbrec.sh
```    

###III. Trobleshuting
    
Fixing "Unknown collation: ‘utf8mb4_unicode_520_ci"

```bash
sed -i 's/utf8mb4_unicode_520_ci/utf8mb4_unicode_ci/g' dump.sql
```