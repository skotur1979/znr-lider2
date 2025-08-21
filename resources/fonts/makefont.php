<?php

require('ttfparser.php');

// ✔️ Dohvati argumente iz CLI (putanja do .ttf i encoding)
$file = $argv[1] ?? null;
$enc = $argv[2] ?? 'cp1250'; // zadano cp1250 ako nije proslijeđeno

if (!$file || !file_exists($file)) {
    die("❌ Font file nije pronađen: $file\n");
}

// Funkcija za generiranje fonta
function MakeFont($fontfile, $enc = 'cp1250', $embed = true)
{
    $ttf = new TTFParser($fontfile);
    $ttf->Parse();

    $basename = basename($fontfile, '.ttf');
    $phpFile = $basename . '.php';
    $zFile = $basename . '.z';

    // Spremi z-komprimirani font
    $compressed = gzcompress(file_get_contents($fontfile));
    file_put_contents($zFile, $compressed);

    // Generiraj PHP font definiciju
    $fp = fopen($phpFile, 'w');
    fwrite($fp, "<?php\n");
    fwrite($fp, "\$type='TrueType';\n");
    fwrite($fp, "\$name='" . $ttf->name . "';\n");
    fwrite($fp, "\$desc=" . var_export($ttf->desc, true) . ";\n");
    fwrite($fp, "\$up=" . $ttf->up . ";\n");
    fwrite($fp, "\$ut=" . $ttf->ut . ";\n");
    fwrite($fp, "\$cw=" . var_export($ttf->charWidths, true) . ";\n");
    fwrite($fp, "\$enc='" . $enc . "';\n");
    fwrite($fp, "\$file='" . $zFile . "';\n");
    fwrite($fp, "\$originalsize=" . filesize($fontfile) . ";\n");
    fwrite($fp, "?>");
    fclose($fp);

    echo "✅ Generirano: $phpFile i $zFile\n";
}

// Pokreni
MakeFont($file, $enc);
