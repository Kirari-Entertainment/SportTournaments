FROM mariadb:lts-noble

ENV MYSQL_DATABASE=kirari \
    MYSQL_USER=kirari \
    MYSQL_PASSWORD=Contrasenna \
    MYSQL_ROOT_PASSWORD=KIRARINET

COPY ./populateMainDB.sql /docker-entrypoint-initdb.d