<?php
$file_path = "C:\\xampp\\htdocs\\prva\\words.json";

function saveToJSON($file_path, $data) {
    file_put_contents($file_path, json_encode($data, JSON_PRETTY_PRINT));
}

function loadFromJSON($file_path) {
    $json_data = file_get_contents($file_path);
    return json_decode($json_data, true);
}

// Inicijaliziraj podatke ako datoteka ne postoji
if (!file_exists($file_path)) {
    $initial_data = array(
        "rijeci" => array()
    );
    saveToJSON($file_path, $initial_data);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Provjeri je li forma poslala podatke
    if (isset($_POST["rijec"])) {
        // Dohvati postojeći JSON
        $data = loadFromJSON($file_path);

        // Postavi novu riječ
        $nova_rijec = $_POST["rijec"];
        
        // Izračunaj broj slova, suglasnika i samoglasnika
        $broj_slova = strlen($nova_rijec);
        $broj_suglasnika = preg_match_all("/[bcdfghjklmnpqrstvwxyz]/i", $nova_rijec);
        $broj_samoglasnika = preg_match_all("/[aeiou]/i", $nova_rijec);

        // Dodaj novu riječ u listu
        $data["rijeci"][] = array(
            "Rijec" => $nova_rijec,
            "BrojSlova" => $broj_slova,
            "BrojSuglasnika" => $broj_suglasnika,
            "BrojSamoglasnika" => $broj_samoglasnika
        );

        // Spremi promjene u JSON datoteku
        saveToJSON($file_path, $data);
    } elseif (isset($_POST["obrisi_rijec"])) {
        // Dohvati riječ koju želimo izbrisati
        $rijec_za_brisanje = $_POST["obrisi_rijec"];

        // Dohvati postojeći JSON
        $data = loadFromJSON($file_path);

        // Prouči sve riječi u listi i ukloni onu koja se treba obrisati
        foreach ($data["rijeci"] as $key => $rijec_data) {
            if ($rijec_data["Rijec"] === $rijec_za_brisanje) {
                unset($data["rijeci"][$key]);
                break;  // Prekini petlju kad pronađemo riječ za brisanje
            }
        }

        // Ponovno indeksiraj ključeve niza
        $data["rijeci"] = array_values($data["rijeci"]);

        // Spremi promjene u JSON datoteku
        saveToJSON($file_path, $data);
    }
}

// Dohvati podatke iz JSON-a
$data = loadFromJSON($file_path);
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unos Riječi</title>
</head>
<body>

    <h2>Unos Riječi</h2>
    <form method="POST" action="">
        <label for="rijec">Riječ:</label>
        <input type="text" name="rijec" required>
        <button type="submit">Unesi</button>
    </form>

    <h2>Podaci</h2>
    <table border="1">
        <tr>
            <th>Riječ</th>
            <th>Broj Slova</th>
            <th>Broj Suglasnika</th>
            <th>Broj Samoglasnika</th>
            <th>Akcija</th>
        </tr>
        <?php foreach ($data["rijeci"] as $rijec_data): ?>
            <tr>
                <td><?php echo $rijec_data["Rijec"]; ?></td>
                <td><?php echo $rijec_data["BrojSlova"]; ?></td>
                <td><?php echo $rijec_data["BrojSuglasnika"]; ?></td>
                <td><?php echo $rijec_data["BrojSamoglasnika"]; ?></td>
                <td>
                    <form method="POST" action="">
                        <input type="hidden" name="obrisi_rijec" value="<?php echo $rijec_data["Rijec"]; ?>">
                        <button type="submit">Obriši</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>

