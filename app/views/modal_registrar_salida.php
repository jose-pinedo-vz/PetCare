<dialog class="corte-dialog corte-dialog--compacto" id="modalSalida" aria-labelledby="tituloSalida">
    <form method="post" action="../controllers/corte_caja.php" id="formMovimiento">
        <div class="corte-dialog-encabezado">
            <div><span class="corte-kicker">Movimiento de efectivo</span><h2 id="tituloSalida">Registrar movimiento</h2></div>
            <button class="corte-dialog-cerrar" type="button" data-cerrar-dialog aria-label="Cerrar ventana">×</button>
        </div>
        <p class="corte-dialog-descripcion">El movimiento quedará asociado a este corte y al empleado que lo registra. El fondo fijo de $1,000 se conserva; los retiros se muestran aparte de los gastos operativos.</p>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
        <input type="hidden" name="periodo" value="<?= htmlspecialchars($periodo) ?>">
        <input type="hidden" name="accion" value="movimiento">
        <input type="hidden" name="corte" value="<?= (int)$corteAbierto['id'] ?>">
        <div class="corte-dialog-campos">
            <div>
                <label class="label-campo" for="tipoMovimiento">Tipo de movimiento</label>
                <select class="campo" id="tipoMovimiento" name="tipo" required>
                    <option value="ingreso manual">Otro ingreso de caja (no venta)</option>
                    <option value="nomina">Nómina</option>
                    <option value="retiro">Retiro / entrega a resguardo</option>
                    <option value="gasto">Gasto</option>
                </select>
            </div>
            <div>
                <label class="label-campo" for="conceptoMovimiento">Concepto</label>
                <input class="campo" id="conceptoMovimiento" name="concepto" type="text" maxlength="250" required>
            </div>
            <div>
                <label class="label-campo" for="importeMovimiento">Importe</label>
                <div class="corte-input-monto"><span aria-hidden="true">$</span><input class="campo" id="importeMovimiento" name="importe" type="number" min="0" step="0.01" value="0.00" required></div>
            </div>
            <div id="campoBeneficiario" hidden>
                <label class="label-campo" for="empleadoBeneficiario">Empleado beneficiario</label>
                <select class="campo" id="empleadoBeneficiario" name="empleado_beneficiario">
                    <option value="">Selecciona un empleado</option>
                    <?php foreach ($empleados as $empleado): ?>
                        <option value="<?= (int)$empleado['id_empleado'] ?>"><?= htmlspecialchars($empleado['nombre'] . ' ' . $empleado['apellido']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="label-campo" for="observacionesMovimiento">Observaciones (opcional)</label>
                <textarea class="campo" id="observacionesMovimiento" name="observaciones" rows="2" maxlength="2000"></textarea>
            </div>
        </div>
        <p class="corte-form-error" id="errorSalida" role="alert" hidden>Completa el concepto y los datos requeridos.</p>
        <div class="corte-dialog-acciones">
            <button class="btn-secundario" type="button" data-cerrar-dialog>Cancelar</button>
            <button class="btn" type="submit" id="guardarSalida">Guardar movimiento</button>
        </div>
    </form>
</dialog>
