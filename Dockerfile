FROM debian
COPY . /home/phpizza
WORKDIR /home/phpizza
EXPOSE 80 443
RUN bash docker/install_lamp.sh
RUN php docker/install_phpizza.php
CMD ["lighttpd", "-D", "-f", "/home/phpizza/lighttpd.conf"]