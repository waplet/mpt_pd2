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
                        <th>Nospēlētas spēles</th>
                        <th>Vārti</th>
                        <th>Ielaisti vārti</th>
                        <th>Uzvarētas spēles</th>
                        <th>Zaudētas spēles</th>
                        <th>Uzvarētas spēles (Pamatlaikā)</th>
                        <th>Zaudētas spēles (Pamatlaikā)</th>
                        <th>Uzvarētas spēles (OT)</th>
                        <th>Zaudētas spēles (OT)</th>
                        <th>Punkti</th>
                    </tr>
                </thead>
                <?php foreach ($mainTable as $row) { ?>
                    <tr>
                        <td>#</td>
                        <td><strong><?=$row['Team'];?></strong></td>
                        <td><?=$row['Games played'];?></td>
                        <td><?=$row['Goals'];?></td>
                        <td><?=$row['Goals lost'];?></td>
                        <td><?=$row['Games won'];?></td>
                        <td><?=$row['Games lost'];?></td>
                        <td><?=$row['Games won Main'];?></td>
                        <td><?=$row['Games lost Main'];?></td>
                        <td><?=$row['Games won OT'];?></td>
                        <td><?=$row['Games lost OT'];?></td>
                        <td><strong><?=$row['Points'];?></strong></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>


<?php
include "footer.php";
