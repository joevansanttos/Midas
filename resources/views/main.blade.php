<?php
/**
 * Created by GT-Nuvem.
 * User: maires
 * Date: 04/12/16
 * Time: 02:10
 */

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Teste do Midas</title>
    </head>
    <body>
        <form method="post" action="daas">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <?php
            $query =    "SELECT id, name, address, city " .
                        "FROM nyc-wifi-hotspot-locations " .
                        "WHERE city = 'New York' " .
                        "ORDER BY id " .
                        "LIMIT 10";
            ?>
            <textarea name="query"><?php echo http_build_query(array('query'=>$query)); ?></textarea>
            <input type="submit" value="Enviar">
        </form>
    </body>
</html>
