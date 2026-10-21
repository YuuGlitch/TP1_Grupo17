const URL_API_Notas = "http://localhost/AppNotas/backend/notas.php";
const URL_API_Usuarios = "http://localhost/AppNotas/backend/usuarios.php";


// login
// habilita la creación de notas cuando se inicio sesion con un usuario valido
document.getElementById("formIngreso").addEventListener("submit", async function(event) {
    event.preventDefault();
    const email = document.getElementById("correo").value; 
    const contrasenia = document.getElementById("contrasenia").value;  
    
    try {
        const response = await fetch(URL_API_Usuarios, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ email, contrasenia })
        });

        const result = await response.json();

        if (result.success) {
            document.getElementById("mensaje").textContent = "Acceso concedido";
            campos2.disabled = false;
        } 
        else {
            document.getElementById("mensaje").textContent = "Usuario o contraseña incorrectos";
        }
    } 
    catch (error) {
        console.error("Error:", error);
        document.getElementById("mensaje").textContent = "Error de conexión con el servidor";
    }

});

async function listarNotas() {
  const respuesta = await fetch(URL_API_Notas);
  const notas = await respuesta.json();
  renderizarNotas(notas);
}

function renderizarNotas(notas) {
    const cuerpo = document.querySelector("#Tabla");
    cuerpo.innerHTML = "";

    notas.forEach(function(nota) {
        const fila = document.createElement("tr");
        fila.dataset.id = nota.id;

        const esFavorita = Number(nota.favorita) === 1 || nota.favorita === true;

        fila.innerHTML = `
            <td>${nota.id}</td>
            <td>${nota.titulo ?? ""}</td>
            <td>${nota.contenido ?? ""}</td>
            <td>
                <button class="btn-editar">Editar</button>
                <button class="btn-borrar">Borrar</button>
                <button class="btn-favorita" aria-pressed="${esFavorita}">
                    ${esFavorita ? "★ Favorita" : "☆ Favorito"}
                </button>
            </td>
        `;

        cuerpo.appendChild(fila);
    });
}

async function cambiarFavorito(nota, boton) {
    const estadoActual = Number(nota.favorita) === 1 || nota.favorita === true;
    const nuevoEstado = !estadoActual;

    boton.disabled = true;

    try {
        const respuesta = await fetch(URL_API_Notas, {
            method: "PUT",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                id: nota.id,
                titulo: nota.titulo,
                contenido: nota.contenido,
                favorita: nuevoEstado
            })
        });

        const resultado = await respuesta.json();

        if (!respuesta.ok) {
            throw new Error(resultado.error || "No se pudo actualizar el favorito.");
        }

        await listarNotas();
    } catch (error) {
        console.error("Error al actualizar favorito:", error);
        document.getElementById("mensaje").textContent =
            "No se pudo actualizar el favorito.";
        boton.disabled = false;
    }
}

// Registrar nuevo usuario
document.querySelector("#formRegistro").addEventListener("submit", async function (evento) {
    evento.preventDefault();
    const mensaje = document.querySelector("#mensaje");
    mensaje.textContent = "Registrando…";

    const nuevoUsuario = {
        email: document.querySelector("#correo_r").value,
        contrasenia: document.querySelector("#contrasenia_r").value
    };

    const respuesta = await fetch(URL_API_Usuarios, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(nuevoUsuario),
    });

    if (!respuesta.ok) {                       
        const error = await respuesta.json();
        mensaje.textContent = `Error ${respuesta.status}: ${error.error}`;
        return;
    }

    mensaje.textContent = "Registrado con exito";
    document.querySelector("#formRegistro").reset();
    listarNotas()
});


document.querySelector("#formNotas").addEventListener("submit", async function (evento) {
    evento.preventDefault();
    const nuevaNota = {
        titulo: document.querySelector("#titulo").value,
        contenido: document.querySelector("#contenido").value
    };

    const respuesta = await fetch(URL_API_Notas, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(nuevaNota),
    });

    if (!respuesta.ok) {                       
        const error = await respuesta.json();
        mensaje.textContent = `Error ${respuesta.status}: ${error.error}`;
        return;
    }

    document.querySelector("#formNotas").reset();
    listarNotas()
});



// Borrar nota
document.querySelector("#cuerpoTabla").addEventListener("click", async function (evento) {
  const fila = evento.target.closest("tr");
  if (!fila) return;
  const id = fila.dataset.id;

  if (evento.target.classList.contains("btn-borrar")) {
    await fetch(`${URL_API_Notas}?id=${id}`, { method: "DELETE" });
    fila.remove();
  }

  if (evento.target.classList.contains("btn-editar")) {
    const tituloNuevo = prompt("Nuevo titulo:", fila.children[1].textContent);
    if (tituloNuevo === null) return;
    const nuevocontenido = prompt("Nuevo contenido:", fila.children[2].textContent);

    const respuesta = await fetch(`${URL_API_Notas}?id=${id}`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ titulo: tituloNuevo, contenido: nuevocontenido }),
    });
    const notaActualizada = await respuesta.json();
    fila.children[1].textContent = notaActualizada.titulo;
    fila.children[2].textContent = notaActualizada.contenido;
  }
});

listarNotas();