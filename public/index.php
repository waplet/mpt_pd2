<?php

include __DIR__ . "/../kernel.php";

$mainTable = \BigF\Managers\Report::mainTable();

include "header.php";
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Turnīra tabula
            </div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Komanda</th>
                        <th>Punkti</th>
                        <th>Nospēlētas spēles</th>
                        <th>Rezultāts</th>
                        <th>Uzvarētas spēles (Pamatlaikā)</th>
                        <th>Zaudētas spēles (Pamatlaikā)</th>
                        <th>Uzvarētas spēles (OT)</th>
                        <th>Zaudētas spēles (OT)</th>
                        <th>Vārtu attiecība</th>
                    </tr>
                </thead>
                <?php foreach ($mainTable as $position => $row) { ?>
                    <tr>
                        <td><?=($position+1)?></td>
                        <td><strong><?=$row['Team'];?></strong></td>
                        <td><strong><?=$row['Points'];?></strong></td>
                        <td><?=$row['Games played'];?></td>
                        <td><?=$row['Games won'];?>:<?=$row['Games lost'];?></td>
                        <td><?=$row['Games won Main'];?></td>
                        <td><?=$row['Games lost Main'];?></td>
                        <td><?=$row['Games won OT'];?></td>
                        <td><?=$row['Games lost OT'];?></td>
                        <td><?=$row['Goals'];?>:<?=$row['Goals lost'];?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>


<?php
include "footer.php";
