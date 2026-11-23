<?php

require_once __DIR__ . '/../../models/Mascota.php';

$especiesBD = Mascota::obtenerEspecies();     
$razasBD    = Mascota::obtenerRazas();        
$coloresBD  = Mascota::coloresDisponibles();

// id de la especie elegida (para saber qué razas mostrar)
$idEspecieElegida = '';
foreach ($especiesBD as $e) {
    if ($e['especie'] === $valEspecie) {
        $idEspecieElegida = (string) $e['id'];
    }
}

if ($valColor !== '' && $valColor !== 'Otro' && !in_array($valColor, $coloresBD, true)) {
    $valColorOtro = $valColor;
    $valColor = 'Otro';
}
?>
<div class="campo">
    <label class="campo__etiqueta" for="especie">Especie <span class="campo__obligatorio">*</span></label>
    <select class="campo__entrada" id="especie" name="especie" required>
        <option value="">Elige una especie</option>
        <?php foreach ($especiesBD as $e): ?>
            <option value="<?= htmlspecialchars($e['especie']) ?>"
                    data-id="<?= (int) $e['id'] ?>"
                    <?= $e['especie'] === $valEspecie ? 'selected' : '' ?>>
                <?= htmlspecialchars($e['especie']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div class="campo">
    <label class="campo__etiqueta" for="raza">Raza <span class="campo__obligatorio">*</span></label>
    <select class="campo__entrada" id="raza" name="raza" required <?= $idEspecieElegida === '' ? 'disabled' : '' ?>>
        <option value="">Elige primero la especie</option>
        <?php foreach ($razasBD as $r): ?>
            <?php $esDeLaEspecie = (string) $r['especie'] === $idEspecieElegida; ?>
 
            <option value="<?= htmlspecialchars($r['raza']) ?>"
                    data-especie="<?= (int) $r['especie'] ?>"
                    <?= $esDeLaEspecie ? '' : 'hidden disabled' ?>
                    <?= $esDeLaEspecie && $r['raza'] === $valRaza ? 'selected' : '' ?>>
                <?= htmlspecialchars($r['raza']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div class="campo">
    <label class="campo__etiqueta" for="color">Color <span class="campo__obligatorio">*</span></label>
    <select class="campo__entrada" id="color" name="color" required>
        <option value="">Elige un color</option>
        <?php foreach ($coloresBD as $c): ?>
            <option value="<?= htmlspecialchars($c) ?>" <?= $c === $valColor ? 'selected' : '' ?>><?= htmlspecialchars($c) ?></option>
        <?php endforeach; ?>
        <option value="Otro" <?= $valColor === 'Otro' ? 'selected' : '' ?>>Otro (especificar)</option>
    </select>
</div>

<div class="campo" id="campo-color-otro" <?= $valColor === 'Otro' ? '' : 'hidden' ?>>
    <label class="campo__etiqueta" for="color_otro">¿Qué color? <span class="campo__obligatorio">*</span></label>
    <input class="campo__entrada" type="text" id="color_otro" name="color_otro" maxlength="50"
           placeholder="p. ej. Marrón rojizo"
           pattern="[A-Za-zÁÉÍÓÚÜÑáéíóúüñ ,]+" title="Solo letras, espacios y comas"
           value="<?= htmlspecialchars($valColorOtro) ?>"
           <?= $valColor === 'Otro' ? 'required' : '' ?>>
</div>

<script>

(function () {
    const especie = document.getElementById('especie');
    const raza = document.getElementById('raza');
    const color = document.getElementById('color');
    const campoOtro = document.getElementById('campo-color-otro');
    const colorOtro = document.getElementById('color_otro');

    especie.addEventListener('change', function () {
        const opcion = especie.options[especie.selectedIndex];
        const idEspecie = opcion ? opcion.dataset.id : '';

        raza.value = '';
        raza.disabled = !idEspecie;
        raza.options[0].textContent = idEspecie ? 'Elige una raza' : 'Elige primero la especie';

        for (const r of raza.options) {
            if (!r.dataset.especie) continue;          // la opción "Elige..."
            const visible = r.dataset.especie === idEspecie;
            r.hidden = !visible;
            r.disabled = !visible;
        }
    });

    color.addEventListener('change', function () {
        const esOtro = color.value === 'Otro';
        campoOtro.hidden = !esOtro;
        colorOtro.required = esOtro;
        if (!esOtro) colorOtro.value = '';
    });
})();
</script>
