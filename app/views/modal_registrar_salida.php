<dialog class="corte-dialog corte-dialog--compacto" id="modalSalida" aria-labelledby="tituloSalida">
    <div class="corte-dialog-encabezado">
        <div><span class="corte-kicker">Movimiento de efectivo</span><h2 id="tituloSalida">Registrar salida / gasto</h2></div>
        <button class="corte-dialog-cerrar" type="button" data-cerrar-dialog aria-label="Cerrar ventana">×</button>
    </div>
    <p class="corte-dialog-descripcion">La captura solo se reflejará en esta vista de demostración.</p>
    <div class="corte-dialog-campos">
        <div><label class="label-campo" for="tipoSalida">Tipo de salida</label><select class="campo" id="tipoSalida"><option>Gasto menor / caja chica</option><option>Retiro de efectivo</option></select></div>
        <div><label class="label-campo" for="conceptoSalida">Concepto</label><input class="campo" id="conceptoSalida" type="text" maxlength="80" placeholder="Ej. Compra de material de limpieza"></div>
        <div><label class="label-campo" for="montoSalida">Monto</label><div class="corte-input-monto"><span aria-hidden="true">$</span><input class="campo" id="montoSalida" type="number" min="0.01" step="0.01" placeholder="0.00"></div></div>
        <div><label class="label-campo" for="notaSalida">Nota (opcional)</label><textarea class="campo" id="notaSalida" rows="2" placeholder="Información adicional"></textarea></div>
    </div>
    <p class="corte-form-error" id="errorSalida" role="alert" hidden>Ingresa un concepto y un monto mayor a cero.</p>
    <div class="corte-dialog-acciones">
        <button class="btn-secundario" type="button" data-cerrar-dialog>Cancelar</button>
        <button class="btn" type="button" id="guardarSalida">Agregar movimiento</button>
    </div>
</dialog>
