# Laravel Data Transfer and Transformation Object Library

## Installation

```
php composer require framespictures/el-dto
```

## Note

    PLEASE NOTE THIS PROJECT IS WORK IN PROGRESS! API MAY CHANGE WITHOUT WARNING!
    IF YOU WANT TO INCORPORATE IT INTO YOUR PROJECT PLEASE STICK TO ONE PARTICULAR VERSION.

## Data Transfer Object Theory

A Data Transfer Object is an object that is used to encapsulate data, and send it from one subsystem of an application to another. DTOs are most commonly used by the Services layer in an N-Tier application to transfer data between itself and the UI layer. The main benefit here is that it reduces the amount of data that needs to be sent across the wire in distributed applications. They also make great models in the MVC pattern.

Another use for DTOs can be to encapsulate parameters for method calls. This can be useful if a method takes more than 4 or 5 parameters.

Using DTOs internally vastly simplifies PHP code allowing for IDE code hinting and avoiding mistakes in array string keys which is a common source of errors. It also greatly improves refactoring.

Using it in Repository Pattern in conjunction with Eloquent, allows for true logic and persistence separation.

## Usual Use Cases

### API Output

### Array to Structured Model Conversion

### Data Mapping

### Input Mapping

### Data Transformation, Cleanup and Chaining

### Code Driven Sets

## Classes

### Basic DTO

### Flexible DTO

### Mapper

#### Basic functionality

#### Multi level mapping

#### Advanced mapping

### Set Model

#### Database Set

### Config Models

#### Laravel Config

### Class Maps