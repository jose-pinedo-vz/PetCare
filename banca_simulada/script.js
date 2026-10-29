async function cargarSaldoBanco(numeroTarjeta)
{
  const tarjetaLimpia=numeroTarjeta.replace(/\s/g, '');
  if (tarjetaLimpia.length < 15)
  {
    document.querySelector('.balance-amount').textContent='$0.00 MXN';
    return;
  }

  try
  {
    const res=await fetch(`obtener_saldo.php?tarjeta=${tarjetaLimpia}`);
    const data=await res.json();
    if (data.status === 'success')
    {
      document.querySelector('.balance-amount').textContent=`$${data.saldo} MXN`;
    }
    else
    {
      document.querySelector('.balance-amount').textContent='$0.00 MXN';
    }
  }
  catch (e)
  {
    console.error('Error al consultar saldo bancario', e);
  }
}

document.addEventListener('DOMContentLoaded', () =>{
  const nameInput=document.getElementById('card_name');
  const numberInput=document.getElementById('card_number');
  const expiryInput=document.getElementById('card_expiry');

  nameInput.addEventListener('input', e => {
    document.getElementById('preview-name').textContent=e.target.value.toUpperCase() || 'NOMBRE Y APELLIDO';
  });

  numberInput.addEventListener('input', e => {
    let value=e.target.value.replace(/\D/g, '').replace(/(.{4})/g, '$1 ').trim();
    e.target.value=value;
    document.getElementById('preview-number').textContent=value || '•••• •••• •••• ••••';

    cargarSaldoBanco(value);
  });

  expiryInput.addEventListener('input', e => {
    let value=e.target.value.replace(/\D/g, '');
    if (value.length >= 2) value=value.substring(0, 2) + '/' + value.substring(2, 4);
    e.target.value=value;
    document.getElementById('preview-expiry').textContent=value || 'MM/AA';
  });
  document.getElementById('payment-form').addEventListener('submit', async function(e)
  {
    e.preventDefault();  
    const btn=document.getElementById('pay-button');
    btn.disabled=true;
    btn.textContent='Conectando con Banco Emisor...';
    const formData=new FormData(this);
    formData.append('monto', '5005.00'); // Donde se tiene que añadir el monto a pagar
    formData.append('comercio', 'PetCare Veterinaria');
    try
    {
      const response=await fetch('api_banco.php',
      {
        method: 'POST',
        body: formData
      });
      
      const text=await response.text();
      let result;
      try
      {
        result=JSON.parse(text);
      }
      catch (err)
      {
        console.error('Respuesta de PHP no es JSON válido:', text);
        alert('Error en el servidor de la banca.');
        return;
      }
      if (result && result.folio)
      {
        alert(`${result.mensaje}\n\nFolio de Operación: ${result.folio}`);
        if (result.archivo)
        {
          window.open(result.archivo, '_blank');
        }
      }
      else if (result && result.mensaje)
      {
        alert(result.mensaje);
      }
      else
      {
        alert('Respuesta inesperada del servidor bancario.');
      }
      if (result && result.status === 'success' && result.nuevo_saldo)
      {
        document.querySelector('.balance-amount').textContent=`$${result.nuevo_saldo} MXN`;
      }
    }
    catch (err)
    {
      console.error(err);
      alert('Error de comunicación con el nodo bancario.');
    }
    finally
    {
      btn.disabled=false;
      btn.textContent='Confirmar Pago de $505.00 MXN';
    }
  });
});