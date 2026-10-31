<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Editar adopción y ventas</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>

<body>

  <div class="layout">

  <?php $paginaActiva = 'adopcion'; include __DIR__ . '/layout/sidebar.php'; ?>

    <main>
      <div class="migaja"><a href="adopcion_venta.php" class="link-tenue">Gestión de adopción y venta</a> / Editar</div>
      <div class="encabezado-pagina">
        <h1>Editar adopción y venta</h1>
      </div>

      <div class="tarjeta">
        <form action="guardar_adopción y venta.php" method="POST" enctype="multipart/form-data">

            <fieldset>

                <legend>Datos generales</legend>

                <div class="grid-formulario">

                    <div>
                        <label class="label-campo" for="especie">
                            Especie:
                        </label>

                        <input
                            type="text"
                            class="campo"
                            id="especie"
                            name="especie"
                            maxlength="30">
                    </div>

                    <div>
                        <label class="label-campo" for="raza">
                            Raza:
                        </label>

                        <input
                            type="text"
                            class="campo"
                            id="raza"
                            name="raza"
                            maxlength="30">
                    </div>

                    <div>
                        <label class="label-campo" for="sexo">
                            Sexo:
                        </label>

                        <select
                            class="campo"
                            id="sexo"
                            name="sexo">
                            <option value="Macho">Macho</option>
                            <option value="Hembra">Hembra</option>
                        </select>
                    </div>

                    <div>
                        <label class="label-campo" for="fecha_de_nacimiento">
                            Fecha de nacimiento *:
                        </label>

                        <input
                            type="date"
                            class="campo"
                            id="fecha_de_nacimiento"
                            name="fecha_de_nacimiento"
                            required>
                    </div>

                    <div>
                        <label class="label-campo" for="color">
                            Color *:
                        </label>

                        <input
                            type="text"
                            class="campo"
                            id="color"
                            name="color"
                            maxlength="20"
                            required>
                    </div>

                    <div>
                        <label class="label-campo" for="peso">
                            Peso (kg) *:
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            class="campo"
                            id="peso"
                            name="peso"
                            required>
                    </div>

                    <div class="campo-completo">
                        <label class="label-campo" for="caracteristicas">
                            Características *:
                        </label>

                        <textarea
                            class="campo"
                            id="caracteristicas"
                            name="caracteristicas"
                            rows="3"
                            required></textarea>
                    </div>

                    <div class="campo-completo">
                        <label class="label-campo" for="fotografia">
                            Fotografía:
                        </label>

                        <input
                            type="file"
                            class="campo"
                            id="fotografia"
                            name="fotografia"
                            accept="image/*">
                    </div>

                </div>

            </fieldset>

            <fieldset>

                <legend>Datos de ingreso</legend>

                <div class="grid-formulario">

                    <div>
                        <label class="label-campo" for="fecha_de_ingreso">
                            Fecha de ingreso *:
                        </label>

                        <input
                            type="datetime-local"
                            class="campo"
                            id="fecha_de_ingreso"
                            name="fecha_de_ingreso"
                            required>
                    </div>

                    <div>
                        <label class="label-campo" for="estado">
                            Estado:
                        </label>

                        <input
                            type="text"
                            class="campo"
                            id="estado"
                            name="estado">
                    </div>

                    <div class="campo-completo">
                        <label class="label-campo" for="procedencia">
                            Procedencia *:
                        </label>

                        <textarea
                            class="campo"
                            id="procedencia"
                            name="procedencia"
                            rows="3"
                            required></textarea>
                    </div>

                </div>

            </fieldset>

            <fieldset>

                <legend>Información comercial</legend>

                <div class="grid-formulario">

                    <div>
                        <label class="label-campo" for="costo_de_adquisicion">
                            Costo de adquisición:
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            class="campo"
                            id="costo_de_adquisicion"
                            name="costo_de_adquisicion">
                    </div>

                    <div>
                        <label class="label-campo" for="precio_de_venta">
                            Precio de venta *:
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            class="campo"
                            id="precio_de_venta"
                            name="precio_de_venta"
                            required>
                    </div>

                </div>

            </fieldset>

            <fieldset>

                <legend>Datos clínicos</legend>

                <div class="grid-formulario">

                    <div>
                        <label class="label-campo" for="desparasitacion">
                            Desparasitación *:
                        </label>

                        <input
                            type="date"
                            class="campo"
                            id="desparasitacion"
                            name="desparasitacion"
                            required>
                    </div>

                    <div class="campo-completo">
                        <label class="label-campo" for="vacunas">
                            Vacunas *:
                        </label>

                        <textarea
                            class="campo"
                            id="vacunas"
                            name="vacunas"
                            rows="3"
                            required></textarea>
                    </div>

                    <div class="campo-completo">
                        <label class="label-campo" for="observaciones">
                            Observaciones *:
                        </label>

                        <textarea
                            class="campo"
                            id="observaciones"
                            name="observaciones"
                            rows="3"
                            required></textarea>
                    </div>

                </div>

            </fieldset>

            <div class="acciones-formulario">
        <a class="btn btn-secundario btn-chico" href="adopcion_venta.php">Cancelar</a>
        <button type="submit" class="btn btn-chico">Guardar</button>
      </div>

        </form>

    
      </div>
    </main>

  </div>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>

</html>