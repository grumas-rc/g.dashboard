# Makefile
init:
	sudo chown -R ${USER}:www-data .
	sudo find . -type f -exec chmod 664 {} \;
	sudo find . -type d -exec chmod 775 {} \;
	sudo chgrp -R www-data storage bootstrap/cache
	sudo chmod -R ug+rw storage bootstrap/cache
	cp -n .env.example .env
	ln -s ../storage/app/public/ public/storage
	php composer.phar install
	php artisan key:generate

fix-permissions:
	sudo chown -R ${USER}:www-data .
	sudo find . -type f -exec chmod 664 {} \;
	sudo find . -type d -exec chmod 775 {} \;
	sudo chgrp -R www-data storage bootstrap/cache
	sudo chmod -R ug+rw storage bootstrap/cache
