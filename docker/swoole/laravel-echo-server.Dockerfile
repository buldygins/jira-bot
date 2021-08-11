FROM node:12-alpine

RUN yarn global add laravel-echo-server && yarn cache clean

RUN mkdir /home/laravel-echo-server
WORKDIR /home/laravel-echo-server

CMD laravel-echo-server start
