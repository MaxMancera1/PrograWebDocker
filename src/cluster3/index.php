<doctype html>
<html>
<head>
    <title>Hola mundo</title>
</head>
<body>
    <?php 
    
    for($i = 0; $i < 500; $i++) {
        $numero=rand(1,16000000);
        $numero=str_pad(dechex($numero), 6, '0', STR_PAD_LEFT);
        echo '<font style="color: #'.$numero.'">Hola Mundo</font><br>';
    }
    ?>
</body>
</html>