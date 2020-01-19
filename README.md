# Mezzio Doctrine OAuth2 Example

Source code for https://marc.guyer.me/posts/mezzio-example/introduction

## Run tests

### Using Global Composer

```sh
composer test
```

### Using Docker

A `DockerFile` is included in the root of this project that can be used to create a Docker image. Using that Docker image, you can then run tests using the php version specified by the DockerFile.

#### Create Docker Image

```sh
docker build -t php73 .
```

#### Run Tests in Docker Container

```sh
docker run --rm --interactive --tty --volume $PWD:/app --user $(id -u):$(id -g) php73 composer test
```
