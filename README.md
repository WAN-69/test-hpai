
# Test PT HPAI - Native PHP

Repo ini ditujukan untuk test kandidat PT HPAI


## Instalasi di lokal

Clone Repo ini

```bash
  git clone https://github.com/WAN-69/test-hpai.git
```

Ganti direktori ke folder hasil clone

```bash
  cd test-hpai
```

Instal dependencies

```bash
  composer install
```

Build docker image

```bash
  docker build -t <image-name> -f dockerFile .
```

Jalankan docker

```bash
  docker-compose up -d
```

Menjalankan migrasi database via docker

```bash
  docker-compose exec app php helpers/Migrate.php
```

Menjalankan unit test via docker

```bash
  docker exec -it php-api ./vendor/bin/phpunit tests/NamaUnitTest.php
```

