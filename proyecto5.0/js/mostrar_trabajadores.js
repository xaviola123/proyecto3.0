function mostrarTrabajadores() {
    var tabla = document.getElementById("tablaTrabajadores");
    if (tabla.style.display === "none") {
        tabla.style.display = "block";
    } else {
        tabla.style.display = "none";
    }
}
