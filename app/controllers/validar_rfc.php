<?php
function validarRfc(string $palabra)
{
    //Debe ser entre 12 o 13
    $errores=[];
    $largo=strlen($palabra);
    if($largo<12||$largo>13)
        {
            $errores[]="El rfc debe tener entre 12 o 13";
            
        }
    
    //Persona Moral
    if($largo==12)
    {
        //Comprueba si es letra las primeras 3
        if(!ctype_alpha($palabra[0])||!ctype_alpha($palabra[1])||!ctype_alpha($palabra[2]))
            {
                $errores[]="Los 3 primeros caracteres deben ser letras en persona moral";

            }
        $inicioNum=3;
    }
    //Persona Fisica
    if ($largo==13)
    {
        if(!ctype_alpha($palabra[0])||!ctype_alpha($palabra[1])||!ctype_alpha($palabra[2])||!ctype_alpha($palabra[3]))
            {
                $errores[]="Los 4 primeros caracteres deben ser letras en persona fisica";
                
            }
        $inicioNum=4;
    }
    
    for($i=0;$i<6;$i++)
        {
            //Si de los siguientes alguno no es numero falla
            if(!ctype_digit($palabra[$i+$inicioNum]))
                {
                    $errores[]="Los caracteres despues de las letras deben ser numeros en formato AAMMDD";
                    
                }
        }

    //Se extrae la fecha
    $fecha=substr($palabra,$inicioNum,6);
    //echo $fecha;

    $año=(int) substr($fecha,0,2);
    $mes=(int) substr($fecha,2,2);
    $dia=(int) substr($fecha,4,2);

    //Comprueba mes 
    if ($mes>13||$mes<1)
        {
            $errores[]="Los caracteres de mes del rfc debe ser menor que 13";
            
        }
    if ($dia>31)
        {
            $errores[]="Los caracteres de dia del rfc debe ser menor que 31";
            
        }

    $ultimo=substr($palabra,-3);
    if (!ctype_alnum($ultimo)) {
        $errores[]= "Los ultimos 3 digitos no deben tener caracteres especiales";
        
    }

    return $errores;
}

$rfc="GDOA341130GR8";
if(validarRfc($rfc)){
    echo "<br>Valido";
}else{
    echo "<br>No valido";
}
?> 