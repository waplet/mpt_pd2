<?php

include __DIR__ . "/../kernel.php";

$mainTable = \BigF\Managers\Report::top10Best();
// die(dump($mainTable));

include "header.php";
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Top 10 rezultatīvākie spēlētaji
            </div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Vārds</th>
                        <th>Uzvārds</th>
                        <th>Vārti</th>
                        <th>Piespēles</th>
                        <th>Komanda</th>
                    </tr>
                </thead>
                <?php foreach ($mainTable as $position => $row) { ?>
                    <tr>
                        <td><?=($position+1)?></td>
                        <td><?=$row['vards'];?></td>
                        <td><?=$row['uzvards'];?></td>
                        <td><?=$row['Goals'];?></td>
                        <td><?=$row['Passes'];?></td>
                        <td><?=$row['nosaukums'];?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>


<?php
include "footer.php";
