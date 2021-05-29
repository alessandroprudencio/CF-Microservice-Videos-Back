# CF-Microservice-Videos-Back

Microsserviço - Backend do catálogo de vídeos com Laravel

### Pré-requisitos

O que você precisa para instalar o software

```
Docker
```
```
Docker Compose
```

### Instalação

```
git clone git@github.com:alessandroprudencio/CF-Microservice-Videos-Back.git
```

```
cd CF-Microservice-Videos-Back
```

```
docker-compose up -d
```

Pronto sua aplicação estará rodando no endereço http://localhost:8000

Rodar migrations e Seeds

```
docker exec -it micro-videos-app bash
```

```
php artisan migrate:refresh --seed
```

### Documentação

http://localhost:8000/api/documentation


### Atualizar documentação da api

```
php artisan l5-swagger:generate
```

## Construído com

* [Laravel](https://laravel.com/)

## Contribuição

Faça um Fork do projeto Crie uma Branch para sua Feature (git checkout -b feature/FeatureIncrivel)
Adicione suas mudanças (git add .)
Comite suas mudanças (git commit -m 'Adicionando uma Feature incrível!)
Faça o Push da Branch (git push origin feature/FeatureIncrivel)
Abra um Pull Request

## Author

* Alessandro Prudencio
* alessandroconectado@gmail.com
* +55 (67) 99269-6705
* [Linkedin](https://www.linkedin.com/in/alessandro-prudencio/)


