AppNotas

1. Descripción del proyecto
AppNotas es una aplicación web de notas desarrollada como proyecto académico. La idea surgió a partir de la necesidad de crear una aplicación que fuera viable dentro del tiempo disponible y de los horarios que teníamos para trabajar en el proyecto.
El objetivo principal fue priorizar la funcionalidad de la aplicación y el desarrollo de sus características principales, dejando el diseño visual y la estética para una futura etapa de desarrollo.


2. Tecnologías y estructura del proyecto
El proyecto está compuesto por los siguientes archivos:
-index.html: estructura principal de la interfaz.
-styles.css: estilos visuales de la aplicación.
-app.js: lógica del lado del navegador e interacción con la interfaz.
-notasDataBase.sql: script SQL relacionado con la base de datos.
-usuarios.php: archivo relacionado con la gestión de usuarios.
-notas.php: archivo relacionado con las operaciones sobre las notas.
-servicios.php: gestión de servicios de la aplicación.
-config.php: configuración del backend.
-conexion.php: conexión con la base de datos.
Se utilizaron HTML, CSS y JavaScript para el frontend, PHP para el backend y MySQL para almacenar los datos.
Cabe destacar que styles.css quedó prácticamente vacío, ya que decidimos concentrarnos en implementar la funcionalidad antes que en desarrollar una interfaz visual más elaborada. El diseño estético quedó pendiente para la entrega del próximo trabajo.




3. Funcionalidades previstas
Gestión de usuarios
La aplicación fue diseñada para permitir el registro de cuentas mediante un correo electrónico y una contraseña, así como el inicio de sesión con una cuenta previamente registrada.
Además, el formulario de registro incluye un campo opcional para indicar el Pokémon favorito del usuario. Esta funcionalidad se incorporó con el objetivo de integrar una API externa, en este caso PokéAPI, como parte de los requisitos del proyecto.

Gestión de notas
La aplicación se planteó como una herramienta sencilla para administrar notas personales. Las funcionalidades previstas incluyen:
•	Crear notas nuevas.
•	Asignar un título a cada nota.
•	Editar notas existentes.
•	Eliminar notas.
•	Ordenar las notas según la fecha de su última modificación.
•	Marcar notas como favoritas y quitarles esa marca.
•	Filtrar las notas para mostrar únicamente las favoritas.

4. Servicios externos e integración con APIs
PokéAPI
Se eligió PokéAPI como servicio externo para incorporar información relacionada con Pokémon y permitir que el usuario indicara su Pokémon favorito durante el registro.
La integración se planteó para cumplir con el requisito académico de utilizar una API externa y añadir una funcionalidad complementaria a la aplicación.
Detalles técnicos pendientes de documentar: se debe especificar si la solicitud a PokéAPI se realiza desde el navegador mediante JavaScript o desde el backend mediante PHP, así como el proveedor de la API, los datos que se envían y el comportamiento de la aplicación si el servicio no está disponible.



5. Despliegue

Se eligió como proveedor a github.
La razón es que github nos proporciona un servicio de modelo PaaS que facilita la exposición del proyecto en una url accesible mediante multiples 
dispositivos con solo darle los datos y el código.
Para volver a desplegar:
-descargar los archivos y colocar la carpeta en la carpeta htdocs de xampp
-iniciar xampp
-ingresar a phpMyAdmin e importar el archivo "notasDataBase.sql" para cargar las bases de datos
-acceder a la dirección "https://localhost/AppNotas/frontend/index.html"

o accediendo desde la URL: https://yuuglitch.github.io/TP1_Grupo17/

6. Limitaciones conocidas
El desarrollo estuvo condicionado por el tiempo. las diferencias de horarios y otras cuestiones externas que nos dificultaron. Por este motivo, no fue posible pulir todas las funcionalidades como nos hubiera gustado.
Entre las limitaciones y problemas que encontramos durante el desarrollo:
Problemas con el registro de usuarios
Durante el desarrollo tuvimos un problema por el cual, al intentar registrar una cuenta nueva, la página quedaba congelada y el proceso no finalizaba correctamente. Como solución temporal incorporamos manualmente cuentas previamente registradas mediante phpMyAdmin para poder continuar con las pruebas.
Luego tuvimos inconvenientes que impidieron completar el proceso de registro y guardar correctamente las nuevas cuentas en la base de datos.
Problemas con el inicio de sesión
También tuvimos un error al intentar iniciar sesión, incluso utilizando cuentas que ya se encontraban registradas en la base de datos. La aplicación no permitía continuar hacia la funcionalidad principal y devolvía un error HTTP 500.
Este error indica que se produjo un fallo interno en el servidor. No pudimos identificar ni corregir su causa antes de la entrega, por lo que el acceso a la aplicación quedó comprometido.
En consecuencia, aunque se desarrollaron las distintas partes del proyecto y se plantearon las funcionalidades principales, no se logró garantizar el funcionamiento completo del flujo de registro e inicio de sesión al momento de la entrega.





7. Organización del trabajo
El proyecto fue desarrollado de manera colaborativa mediante Visual Studio Code compartido.
Inicialmente, la organización contemplaba una división entre frontend y backend, con un integrante encargado principalmente de cada área. Sin embargo, a medida que avanzó el desarrollo, ambos integrantes participaron en las distintas partes de la aplicación, colaborando en la implementación, revisión y resolución de problemas.

8. Trabajo futuro
Entre los aspectos que buscamos mejorar en una futura versión se encuentran:
•	Resolver los errores de registro e inicio de sesión.
•	Verificar el correcto funcionamiento de las operaciones con la base de datos.
•	Completar y probar las funcionalidades de gestión de notas.
•	Mejorar el diseño visual y la experiencia de usuario.
•	Documentar y comprobar el manejo de errores de los servicios externos.
•	Completar las instrucciones de despliegue para facilitar la instalación y ejecución de la aplicación.
