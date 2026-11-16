<dialog class="corte-dialog" id="modalArqueo" aria-labelledby="tituloArqueo">
    <div class="corte-dialog-encabezado">
        <div><span class="corte-kicker">Conteo físico</span><h2 id="tituloArqueo">Desglose de efectivo</h2></div>
        <button class="corte-dialog-cerrar" type="button" data-cerrar-dialog aria-label="Cerrar ventana">×</button>
    </div>
    <p class="corte-dialog-descripcion">Ingresa cuántos billetes y monedas hay de cada denominación.</p>
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
            </tbody>
            <tfoot><tr><th colspan="2">Total contado</th><th class="corte-monto" id="totalArqueo">$0.00</th></tr></tfoot>
        </table>
    </div>
    <div class="corte-dialog-acciones">
        <button class="btn-secundario" type="button" data-cerrar-dialog>Cancelar</button>
        <button class="btn" type="button" id="aplicarArqueo">Aplicar total contado</button>
    </div>
</dialog>
