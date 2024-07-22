# StackOverflow API Project
###Descripción
Este proyecto es una API diseñada para interactuar con datos de StackOverflow, implementada utilizando una arquitectura hexagonal y principios de Domain-Driven Design (DDD). La aplicación proporciona una interfaz RESTful que permite a los usuarios obtener preguntas de StackOverflow basadas en diversos filtros.

## Características
- Arquitectura Hexagonal: Se ha implementado una arquitectura hexagonal para separar la lógica de negocio de las dependencias externas.
- Domain-Driven Design (DDD): El diseño de la aplicación sigue los principios de DDD para una mejor estructuración y organización del código.
- 100% de Cobertura de Pruebas: Todos los componentes del proyecto tienen una cobertura de pruebas del 100%, asegurando que el código sea confiable y robusto.
- Swagger API: Documentación interactiva para probar la API directamente desde el navegador. Accede a la documentación en /api/doc.
- Logging: Implementación de logging con Monolog para registrar eventos importantes y errores en la aplicación.

## Tecnologías Utilizadas
- PHP: Lenguaje de programación para el desarrollo del backend.
- Symfony: Framework PHP utilizado para desarrollar la API.
- Docker: Contenerización de la aplicación para un entorno de desarrollo consistente.
- Swagger: Documentación de la API y pruebas interactivas.
- MySQL: Sistema de gestión de bases de datos utilizado para almacenar datos.
## Instalación
### Requirements
- Docker y Docker Compose instalados en tu máquina.
### Installation steps
#### 1. Clona el Repositorio
git clone git@github.com:rmontanodev/ApiStackOverflowQuestions.git
cd ApiStackOverflowQuestions

#### 2. Build
docker-compose build

#### 3. Run it
docker-compose up -d

#### 4. Run tests
./vendor/bin/phpunit 
####There is already a report about the coverage and complexity /build/coverage/index.html
##### Generate a new report
./vendor/bin/phpunit --coverage-html coverage

#### 5. Test API
You can check endpoints and test it in http://localhost:8080/api/doc.