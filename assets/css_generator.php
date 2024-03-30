<?php
$tab = [];

function getFilesInDirectory($dir)
{
    $files = glob("$dir/*");
    global $tab;

    foreach ($files as $file) {
        if (!is_dir($file)) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'png') {
                array_push($tab, $file);
            }
        } else {
            getFilesInDirectory($file);
        }
    }
}

if ($argc < 2) {
    echo "Il faut un argument\n";
    exit;
}

$folder = end($argv);

$recursiveFlag = in_array("-r", $argv) || in_array("--recursive", $argv);

if ($recursiveFlag) {
    getFilesInDirectory($folder);
} else {
    $tab = glob($folder . "/*.png");
}

echo "Contenu du tableau \$tab :\n";
print_r($tab);

$finalWidth = 0;
$finalHeight = 0;

foreach ($tab as $tabs => $filename) {
    $size = getimagesize($filename);
    $finalWidth += $size[0];
    $finalHeight = max($finalHeight, $size[1]); // Use max to account for different image heights
}

$padding = 10; // Adjust the padding as needed

$finalWidth += (count($tab) - 1) * $padding; // Adjust for padding between images

$image = imagecreatetruecolor($finalWidth, $finalHeight);
$offsetX = 0;
$offsetY = 0;

$css_content = ''; // CSS content to be generated

foreach ($tab as $tabs => $filename) {
    $source = imagecreatefrompng($filename);
    $imageWidth = imagesx($source);
    $imageHeight = imagesy($source);

    imagecopy($image, $source, $tabs * ($imageWidth + $offsetX + $padding), $offsetY, 0, 0, $imageWidth, $imageHeight);

    // Generate CSS for each image
    $css_content .= ".image{$tabs} {\n";
    $css_content .= "  width: {$imageWidth}px;\n";
    $css_content .= "  height: {$imageHeight}px;\n";
    $css_content .= "  background: url('{$filename}') center center no-repeat;\n";
    $css_content .= "  background-size: cover;\n";
    $css_content .= "}\n";
}

// Save CSS to a file
$css_file = fopen("style.css", 'w');
fwrite($css_file, $css_content);
fclose($css_file);

// Save the sprite image
imagepng($image, "sprite.png");
?>
