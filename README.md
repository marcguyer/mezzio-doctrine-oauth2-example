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
docker build -t mezzio-doctrine-oauth2-example .
```

#### Run Composer in Docker Container

##### Run tests

```sh
docker run --rm --interactive --tty --volume $PWD:/app mezzio-doctrine-oauth2-example composer test
```
