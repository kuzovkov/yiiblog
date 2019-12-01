#!/bin/sh

################################
# Install Yii #
################################
VERSION=2.0.29

curl -o app.tar.gz -fSL "https://github.com/yiisoft/yii2/releases/download/$VERSION/yii-basic-app-$VERSION.tgz"
tar -xzf app.tar.gz ; \
	rm app.tar.gz; \
	chmod -R 777 ./basic

