<dialog class="corte-dialog corte-dialog--compacto" id="modalCierreMensual" aria-labelledby="tituloCierreMensual">
    <div class="corte-dialog-encabezado">
        <div><span class="corte-kicker">Consolidación mensual</span><h2 id="tituloCierreMensual">Generar corte mensual</h2></div>
        <button class="corte-dialog-cerrar" type="button" data-cerrar-dialog aria-label="Cerrar ventana">×</button>
    </div>
    <p class="corte-dialog-descripcion">Revisa el periodo antes de generar el reporte de cierre.</p>
    <div class="corte-dialog-campos">
        <div><label class="label-campo" for="periodoCierreMensual">Mes / año</label><input class="campo" id="periodoCierreMensual" type="month" value="2026-11"></div>
    </div>
    <div class="corte-confirmacion-resumen">
        <div><span>Cortes diarios incluidos</span><strong>26</strong></div>
        <div><span>Ingresos brutos</span><strong>$186,420.00</strong></div>
        <div><span>Egresos</span><strong>$48,350.00</strong></div>
        <div><span>Balance neto</span><strong>$138,070.00</strong></div>
    </div>
    <p class="corte-dialog-nota">Vista demostrativa: no se consolidarán ni guardarán registros.</p>
    <div class="corte-dialog-acciones">
        <button class="btn-secundario" type="button" data-cerrar-dialog>Cancelar</button>
        <button class="btn" type="button" id="confirmarCierreMensual">Generar cierre mensual</button>
    </div>
</dialog>
