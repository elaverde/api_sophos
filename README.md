# Proyecto de API con Slim 3 y Firebase JW

Este proyecto es una implementación de una API REST utilizando el framework Slim 3 y Firebase JWT para la autenticación de usuarios.

## Requisitos

* PHP >= 7.0
* Un servidor web (Apache)
* Un gestor de base de datos (MySQL)

## Instalación

1. Descargar el proyecto en su equipo local.
2. Instalar las dependencias utilizando composer con el comando`composer install` en la línea de comandos.
3. Configurar el archivo`.env` con las credenciales necesarias para el uso de Firebase JWT.

# Para ejecutar las migraciones existentes

composer migrate

# Para crear una nueva migración

composer create-migration NombreDeTuMigracion

## Uso

Este proyecto incluye los siguientes endpoints:

* `POST v1/login`: para el inicio de sesión de un usuario.
* `POST v1/users`: para crear un nuevo usuario administrador (autenticación requerida).
* `PUT v1/users/{id}`: para actualizar un usuario administrador (autenticación requerida).
* `DELETE v1/users/{id}`: para eliminar un usuario administrador (autenticación requerida).
* `GET v1/users/{filter}/{search}`: para obtener una lista de usuarios filtrados por nombre o correo electrónico.

Además, se ha implementado un middleware de autenticación que valida la sesión del usuario en procesos como store, update y delete.

##**Documentación:**

[https://app.swaggerhub.com/apis-docs/EDILSONLAVEERDE182/Sophos/0.0.1#/Login/postv1_login](https://)
