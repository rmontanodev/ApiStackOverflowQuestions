# StackOverflow API Project
### Description
The idea of this project is an API designed to interact with StackOverflow data, implemented using a hexagonal architecture and Domain-Driven Design (DDD) principles. The application provides a RESTful interface that allows users to retrieve StackOverflow questions based on various filters.
## Features
- **Hexagonal Architecture**: A hexagonal architecture has been implemented to separate business logic from external dependencies.
- **Domain-Driven Design (DDD)**: The application design follows DDD principles for better code structuring and organization.
- **Testing**: All project components have 100% test coverage, ensuring that the code is reliable and robust.
- **Swagger API**: Interactive documentation for testing the API directly from the browser. Access the documentation at /api/doc.
- **Logging**: Logging implementation with Monolog to record important events and errors in the application.

## Tecnologías Utilizadas
- **PHP**: Programming language for backend development.
- **Symfony**: PHP framework used to develop the API.
- **Docker**: Containerization of the application for a consistent development environment.
- **Swagger**: API documentation and interactive testing.
- **MySQL**: Database management system used to store data.
## Installation
### Requirements
- Docker and Docker Compose installed on your machine.
### Installation steps
#### 1. Clone el Repositorio
git clone git@github.com:rmontanodev/ApiStackOverflowQuestions.git
#### 1.1 go to repo
cd ApiStackOverflowQuestions

#### 2. Build
docker build -t apistackoverflowquestions-app:latest .

#### 3. Run it
docker-compose up -d

#### 4. Run tests
./vendor/bin/phpunit --coverage-html build/coverage

#### 5. Test API
You can check endpoints and test it in http://localhost:8080/api/doc.