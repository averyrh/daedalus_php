<?php include_once 'daedalus.php'; ?>
<link rel="stylesheet" href="global.css">
<html lang="en">
    <body>
        <?php
            try {
                $daedalus = new daedalus(50);
                $daedalus->generate();
                $daedalus->print();
            } catch (Exception $e) {
                echo '<p>ERROR: '.$e->getMessage().'</p>';
            }
        ?>
    </body>
</html>