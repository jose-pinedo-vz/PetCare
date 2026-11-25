<dialog class="corte-dialog" id="modalArqueo" aria-labelledby="tituloArqueo">
    <form method="post" action="../controllers/corte_caja.php" id="formArqueo">
        <div class="corte-dialog-encabezado">
            <div><span class="corte-kicker">Conteo físico</span><h2 id="tituloArqueo">Arqueo ciego de efectivo</h2></div>
            <button class="corte-dialog-cerrar" type="button" data-cerrar-dialog aria-label="Cerrar ventana">×</button>
        </div>
        <p class="corte-dialog-descripcion">Declara el efectivo físico antes de consultar el resultado del corte.</p>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
        <input type="hidden" name="periodo" value="<?= htmlspecialchars($periodo) ?>">
        <input type="hidden" name="accion" value="declarar_arqueo">
        <input type="hidden" name="corte" value="<?= (int)$corteAbierto['id'] ?>">
        <input type="hidden" id="efectivoContado" name="efectivo_contado" value="">
        <nav class="corte-tabs" role="tablist" aria-label="Modo de captura del arqueo">
            <button class="corte-tab activa" id="modoDenominacion" type="button" role="tab" aria-selected="true" aria-controls="panelDenominacion" data-modo="denominacion">Por denominación</button>
            <button class="corte-tab" id="modoExacto" type="button" role="tab" aria-selected="false" aria-controls="panelExacto" tabindex="-1" data-modo="exacto">Monto exacto</button>
        </nav>
        <div id="panelDenominacion" role="tabpanel" aria-labelledby="modoDenominacion">
        <div class="corte-tabla-envoltura corte-arqueo-tabla">
            <table class="corte-tabla">
                <thead><tr><th>Denominación</th><th>Cantidad</th><th class="corte-monto">Subtotal</th></tr></thead>
                <tbody>
                    <tr><th scope="row">$1,000</th><td><input class="campo" type="number" min="0" step="1" value="0" data-cantidad data-valor="1000" aria-label="Cantidad de billetes de mil pesos"></td><td class="corte-monto" data-subtotal>$0.00</td></tr>
                    <tr><th scope="row">$500</th><td><input class="campo" type="number" min="0" step="1" value="0" data-cantidad data-valor="500" aria-label="Cantidad de billetes de quinientos pesos"></td><td class="corte-monto" data-subtotal>$0.00</td></tr>
                    <tr><th scope="row">$200</th><td><input class="campo" type="number" min="0" step="1" value="0" data-cantidad data-valor="200" aria-label="Cantidad de billetes de doscientos pesos"></td><td class="corte-monto" data-subtotal>$0.00</td></tr>
                    <tr><th scope="row">$100</th><td><input class="campo" type="number" min="0" step="1" value="0" data-cantidad data-valor="100" aria-label="Cantidad de billetes de cien pesos"></td><td class="corte-monto" data-subtotal>$0.00</td></tr>
                    <tr><th scope="row">$50</th><td><input class="campo" type="number" min="0" step="1" value="0" data-cantidad data-valor="50" aria-label="Cantidad de billetes de cincuenta pesos"></td><td class="corte-monto" data-subtotal>$0.00</td></tr>
                    <tr><th scope="row">$20</th><td><input class="campo" type="number" min="0" step="1" value="0" data-cantidad data-valor="20" aria-label="Cantidad de billetes de veinte pesos"></td><td class="corte-monto" data-subtotal>$0.00</td></tr>
                    <tr><th scope="row">$10</th><td><input class="campo" type="number" min="0" step="1" value="0" data-cantidad data-valor="10" aria-label="Cantidad de monedas de diez pesos"></td><td class="corte-monto" data-subtotal>$0.00</td></tr>
                    <tr><th scope="row">$5</th><td><input class="campo" type="number" min="0" step="1" value="0" data-cantidad data-valor="5" aria-label="Cantidad de monedas de cinco pesos"></td><td class="corte-monto" data-subtotal>$0.00</td></tr>
                    <tr><th scope="row">$2</th><td><input class="campo" type="number" min="0" step="1" value="0" data-cantidad data-valor="2" aria-label="Cantidad de monedas de dos pesos"></td><td class="corte-monto" data-subtotal>$0.00</td></tr>
                    <tr><th scope="row">$1</th><td><input class="campo" type="number" min="0" step="1" value="0" data-cantidad data-valor="1" aria-label="Cantidad de monedas de un peso"></td><td class="corte-monto" data-subtotal>$0.00</td></tr>
                    <tr><th scope="row">$0.50</th><td><input class="campo" type="number" min="0" step="1" value="0" data-cantidad data-valor="0.5" aria-label="Cantidad de monedas de cincuenta centavos"></td><td class="corte-monto" data-subtotal>$0.00</td></tr>
                    <tr><th scope="row">$0.10</th><td><input class="campo" type="number" min="0" step="1" value="0" data-cantidad data-valor="0.1" aria-label="Cantidad de monedas de diez centavos"></td><td class="corte-monto" data-subtotal>$0.00</td></tr>
                    <tr><th scope="row">$0.05</th><td><input class="campo" type="number" min="0" step="1" value="0" data-cantidad data-valor="0.05" aria-label="Cantidad de monedas de cinco centavos"></td><td class="corte-monto" data-subtotal>$0.00</td></tr>
                    <tr><th scope="row">$0.01</th><td><input class="campo" type="number" min="0" step="1" value="0" data-cantidad data-valor="0.01" aria-label="Cantidad de monedas de un centavo"></td><td class="corte-monto" data-subtotal>$0.00</td></tr>
                </tbody>
                <tfoot><tr><th colspan="2">Total declarado</th><th class="corte-monto" id="totalArqueo">$0.00</th></tr></tfoot>
            </table>
        </div>
        </div>
        <div id="panelExacto" role="tabpanel" aria-labelledby="modoExacto" hidden>
            <div class="corte-dialog-campos">
                <label class="label-campo" for="montoExacto">Total de efectivo contado</label>
                <input class="campo" type="number" id="montoExacto" min="0" step="0.01" inputmode="decimal" placeholder="Ej. 1540.50" aria-describedby="ayudaMontoExacto">
                <p class="corte-dialog-descripcion" id="ayudaMontoExacto">Escribe el total físico sin desglosar. Acepta centavos.</p>
            </div>
        </div>
        <div class="corte-dialog-campos">
            <label class="label-campo" for="observacionesCorte">Observaciones (opcional)</label>
            <textarea class="campo" id="observacionesCorte" name="observaciones" rows="2" maxlength="2000"></textarea>
        </div>
        <p class="corte-form-error" id="errorArqueo" role="alert" hidden>Verifica el total contado.</p>
        <div class="corte-dialog-acciones">
            <button class="btn-secundario" type="button" data-cerrar-dialog>Cancelar</button>
            <button class="btn" type="submit" id="aplicarArqueo">Enviar declaración</button>
        </div>
    </form>
</dialog>
