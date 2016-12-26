FROM php:5.6

RUN apt-get update -y && \
  apt-get install software-properties-common -y && \
  apt-add-repository ppa:git-core/ppa -y && \
  apt-get install -y git unzip zip && \
  curl -sS https://getcomposer.org/installer | php \
    && chmod +x composer.phar \
    && mv composer.phar /usr/local/bin/composer && \
  curl -Ls https://gist.github.com/andkirby/389f18642fc08d1b0711d17978445f2b/raw/bashrc_ps1_install.sh | bash; . ~/.bashrc && \
  curl -Ls https://gist.github.com/andkirby/0e2982bee321aa611bc66385dee5f399/raw/bashrc_init_install.sh | bash

ENV PATH ${PATH}:/code/vendor/bin

RUN mkdir /root/.ssh
COPY known_hosts /root/.ssh/
COPY entrypoint.sh /root/
ENTRYPOINT /root/entrypoint.sh

WORKDIR /code
