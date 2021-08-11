FROM mariadb
RUN apt-get update \
      && apt-get install -y mc

# RUN mkdir -p /docker-entrypoint-initdb.d

# COPY ./initdb-postgis.sh /docker-entrypoint-initdb.d/postgis.sh
# COPY ./update-postgis.sh /usr/local/bin
