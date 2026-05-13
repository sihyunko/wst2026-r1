FROM php:8.2.12-apache

RUN apt-get update && apt-get install -y wget unzip \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && a2enmod rewrite

WORKDIR /usr/share

RUN wget https://files.phpmyadmin.net/phpMyAdmin/5.2.1/phpMyAdmin-5.2.1-all-languages.zip \
    && unzip phpMyAdmin-5.2.1-all-languages.zip \
    && mv phpMyAdmin-5.2.1-all-languages phpmyadmin \
    && rm phpMyAdmin-5.2.1-all-languages.zip

RUN cp /usr/share/phpmyadmin/config.sample.inc.php /usr/share/phpmyadmin/config.inc.php

RUN sed -i "s/\$cfg\['blowfish_secret'\] = '';/\$cfg['blowfish_secret'] = 'xamppdockersecret1234567890';/" /usr/share/phpmyadmin/config.inc.php

RUN cat >> /usr/share/phpmyadmin/config.inc.php <<'EOF'
$i = 1;
$cfg['Servers'][$i]['host'] = 'db';
$cfg['Servers'][$i]['port'] = '3306';
$cfg['Servers'][$i]['auth_type'] = 'cookie';
$cfg['Servers'][$i]['AllowNoPassword'] = true;
EOF

RUN cat > /etc/apache2/conf-available/phpmyadmin.conf <<'EOF'
Alias /phpmyadmin /usr/share/phpmyadmin

<Directory /usr/share/phpmyadmin>
    Options Indexes FollowSymLinks
    DirectoryIndex index.php
    AllowOverride All
    Require all granted
</Directory>
EOF

RUN a2enconf phpmyadmin