#!/bin/sh

################################
# Install Yii #
################################

curl -o app.tar.gz -fSL "https://github.com/yiisoft/yii2/releases/download/2.0.29/yii-basic-app-2.0.29.tgz"
tar -xzf app.tar.gz ; \
	rm app.tar.gz; \
	chmod -R 777 ./basic

