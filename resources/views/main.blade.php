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
             $query =   "SELECT id, name, address, city " .
                        "FROM nyc-wifi-hotspot-locations " .
                        "WHERE city = 'New York' " .
                        "ORDER BY id " .
                        "LIMIT 10";
            ?>
            <textarea name="query"><?php echo http_build_query(array('query'=>$query)); ?></textarea>
            <input type="submit" value="Enviar">
        </form>

        <form method="post" action="daas">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <?php

            $query = "SELECT borough, location_1, hours, zip_code " .
                     "FROM vz8c-29aj" .
                     "WHERE zip_code > 11000" .
                     "LIMIT 2"; 
            ?>
            <textarea name="query"><?php echo http_build_query(array('query'=>$query)); ?></textarea>
            <input type="submit" value="Enviar">
        </form>

        <form method="post" action="daas">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <?php

            $query = "SELECT neighborhood, borough, address  " .
                     "FROM ntcm-2w4k";
            ?>
            <textarea name="query"><?php echo http_build_query(array('query'=>$query)); ?></textarea>
            <input type="submit" value="Enviar">
        </form>

        <form method="post" action="daas">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <?php

            $query = "SELECT facility_name.w7a6-9xrz, phone.vz8c-29aj " .
                     "FROM w7a6-9xrz" .
                     "INNER JOIN vz8c-29aj " .
                     "ON borough.w7a6-9xrz = borough.vz8c-29aj " .
                     "WHERE borough.w7a6-9xrz = 'Queens'"; 

            ?>
            <textarea name="query"><?php echo http_build_query(array('query'=>$query)); ?></textarea>
            <input type="submit" value="Enviar">
        </form>

        <form method="post" action="daas">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <?php

            $query =    "db.nyc-wifi-hotspot-locations.find({ " .
                "city': 'New York'".
                " }, { 'id': 1, 'name': 1, 'address': 1, 'city': 1 ".
                " }).limit(10).sort({  'id': 1 }) ";

            ?>
            <textarea name="query"><?php echo http_build_query(array('query'=>$query)); ?></textarea>
            <input type="submit" value="Enviar">
        </form>

    </body>
</html>
