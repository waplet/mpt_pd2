<?php

include __DIR__ . "/../kernel.php";

$mainTable = \BigF\Managers\Report::mostAggressive();

include "header.php";
?>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Rupjākie spēlētāji
                </div>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Vārds</th>
                        <th>Uzvārds</th>
                        <th>Sodi</th>
                        <th>Komanda</th>
                    </tr>
                    </thead>
                    <?php foreach ($mainTable as $position => $row) { ?>
                        <tr>
                            <td><?=($position+1)?></td>
                            <td><?=$row['vards'];?></td>
                            <td><?=$row['uzvards'];?></td>
                            <td><?=$row['Fouls'];?></td>
                            <td><?=$row['nosaukums'];?></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>


<?php
include "footer.php";
