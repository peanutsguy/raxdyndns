FROM php:7.4-cli
COPY rax /dyndns
WORKDIR /dyndns
CMD [ "php", "./main.php" ]