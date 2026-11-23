<dialog class="corte-dialog corte-dialog--compacto" id="modalConfirmarCierre" aria-labelledby="tituloConfirmarCierre">
    <div class="corte-dialog-encabezado">
        <div><span class="corte-kicker">Confirmación de turno</span><h2 id="tituloConfirmarCierre">¿Cerrar este corte?</h2></div>
        <button class="corte-dialog-cerrar" type="button" data-cerrar-dialog aria-label="Cerrar ventana">×</button>
    </div>
    <div class="corte-confirmacion-resumen">
        <p>Vas a marcar como cerrado el corte del turno <strong>Matutino</strong>.</p>
        <div><span>Efectivo esperado en caja</span><strong id="resumenCierreEsperado">$5,280.00</strong></div>
        <div><span>Efectivo contado</span><strong id="resumenCierreContado">$5,280.00</strong></div>
        <div><span>Diferencia</span><strong id="resumenCierreDiferencia">$0.00</strong></div>
    </div>
    <p class="corte-dialog-nota">En la versión conectada, esta acción bloqueará la edición del turno y registrará al cajero que lo cierra.</p>
    <div class="corte-dialog-acciones">
        <button class="btn-secundario" type="button" data-cerrar-dialog>Volver a revisar</button>
        <button class="btn btn-peligro" type="button" id="confirmarCierre">Confirmar cierre</button>
    </div>
</dialog>
