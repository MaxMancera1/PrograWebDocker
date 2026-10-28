<?php

include __DIR__ . '/models/sistema.class.php';

$app = new Sistema();
if($app->enviarCorreo(['manceragarciamax@gmail.com' , 'car252004@gmail.com'], 'NO ERA DESLACTOSADA', 'siempre pregunta de que era la leche o el yogurt antes de que te sirvan')){
    echo 'Correo enviado correctamente';
}else{
    echo 'Error al enviar el correo';
}
?>