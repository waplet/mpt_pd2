<?php
include __DIR__ . "/../kernel.php";

function decode($data) {
    $decoded = json_decode($data, true);

    if ($decoded == null) {
        $data = mb_convert_encoding($data, "UTF-8", "Windows-1252");
        $decoded = json_decode($data, true);
    }

    return $decoded;
}

if (!empty($_POST)) {
    if (isset($_POST['load_json'])) {
        $files = glob(__DIR__ . "../load_dir/*.json");
        foreach ($files as $file) {
            $loader = new \BigF\Managers\Loaders\JsonLoader($file);
            new \BigF\Managers\Importer($loader->load());
        }

        header('Location: /import.php?imported=1');
    } else {
        // Check if text
        $possibleJson = "";
        if (!empty($_POST['textarea_json'])) {
            $possibleJson = $_POST['textarea_json'];
        } else if (!empty($_FILES['upload_json'])) {
            $possibleJson = file_get_contents($_FILES['upload_json']['tmp_name']);
        }

        if (empty($possibleJson)) {
            // No data received
        } else {
            $decoded = decode($possibleJson);

            if ($decoded) {
                new \BigF\Managers\Importer($decoded);
                header('Location: /import.php?imported=1');
                exit;
            }

            header('Location: /import.php?imported=0');
            exit;

        }
    }
}

include "header.php";
?>

<div class="row">
    <div class="col-sm-12">
        <?php if (array_key_exists('imported', $_GET)) {
            if ($_GET['imported'] == 1) { ?>
                <div class="alert alert-success">
                    Dati importēti. Doties uz turnīta tabulas pārskatu - <a href="/index.php">Turnīra tabula</a>
                </div>
            <?php } else { ?>
                <div class="alert alert-danger">
                    Kļūda importējot datus
                </div>
            <?php }
            }?>

        <h2>JSON Importer</h2>
        <form method="POST" enctype="multipart/form-data" action="/import.php">
            <div class="form-group">
                <label for="textarea_json">JSON formatted text</label>
                <textarea title="Textarea for JSON text" name="textarea_json" rows="10" class="form-control"><?=isset($_POST['textarea_json']) ? $_POST['textarea_json'] : ''?></textarea>
            </div>
            <div class="form-group">
                <label for="upload_json">JSON file uploader</label>
                <input type="file" name="upload_json"/>
            </div>
            <div class="form-group">
                <label for="load_json">JSON from dir (./load_dir/*.json)</label>
                <input type="checkbox" name="load_json" class="checkbox" value="1"/>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-success" value="Process"/>
            </div>
        </form>
    </div>
</div>


<?php
include "footer.php";