<?php
include "html-template.php";
$line = "";
$recursivity = null;
$output_image = "sprite.png";
$output_style = "style.css";
$padding = null;
$override_size = null;
$columns_number = null;
$folder = '';


function cmd($argv)
{
    global $recursivity, $output_image, $output_style, $padding, $override_size, $columns_number, $folder;
    $options = getopt("i:s:p:o:c:");

    $cmd = ['-r', 'i', 's', 'p', 'o', 'c'];
    foreach ($options as $option => $value) {

        if (!in_array($option, $cmd)) {
            die("Erreur l'option -$option n'existe pas\n");
        }
    }
    if (count($argv) <= 1) {
        die("Aucun dossier selectionne\n");
    } else {
        array_shift($argv);
        if (is_dir(end($argv))) {
            $folder = end($argv);
        } else {
            die("Impossible d'ouvrir le dossier\n");
        }

        if (in_array($cmd[0], $argv)) {
            $recursivity = True;
        }
        foreach ($cmd as $val) {
            if (array_key_exists($val, $options)) {

                switch ($val) {

                    case 'i':
                        if ($options[$val] != $folder) {
                            $output_image = $options[$val] . ".png";
                        }
                        break;
                    case 's':
                        if ($options[$val] != $folder) {
                            $output_style = $options[$val] . ".css";
                        }
                        break;
                    case 'p':
                        if ($options[$val] != $folder && is_numeric($options[$val]) && $options[$val] > 0) {
                            $padding = $options[$val];
                        } else {
                            die("Le padding doit etre un nombre positif\n");
                        }
                        break;
                    case 'o':
                        if ($options[$val] != $folder && is_numeric($options[$val]) && $options[$val] > 0) {
                            $override_size = $options[$val];
                        } else {
                            die("Le override size doit etre un nombre positif non nul\n");
                        }
                        break;
                    case 'c':
                        if ($options[$val] != $folder && is_numeric($options[$val]) && $options[$val] > 0) {
                            $columns_number = $options[$val];
                        } else {
                            
                            die("Le nombre de colone doit etre un nombre  positif non nul\n");
                        }
                        break;

                    default:

                        break;
                }
            }
        }
    }
}

cmd($argv);

$spritePaths = [];
function glob_recursive($folder)
{
    global $spritePaths, $recursivity;
    $files = glob("$folder/*");
    foreach ($files as $value) {

        if (is_dir($value)) {
            if ($recursivity) {
                glob_recursive($value);
            }
        } else {

            $path = $value;
            $parts = explode('.', $path);
            $ext = end($parts);
            if ($ext == "png") {
                array_push($spritePaths, $path);
            }
        }
    }

    usort($spritePaths, function ($a, $b) {
        return strlen($a) - strlen($b) ?: strcmp($a, $b);
    });
}

glob_recursive($folder);
function custumSize($spritePaths, $override_size, $padding) {
    global $columns_number;
    if(!is_null($columns_number) && $columns_number > count($spritePaths)){
        $columns_number = count($spritePaths);
    }
    $imgTotalHeight = 0;
    $maxRowWidth = 0;
    $maxRowHeight = 0;
    $currentRowWidth = 0;
    $i = 1;

    foreach ($spritePaths as $key => $value) {
        $size = getimagesize($value);
        $imgWidth = $override_size != 0 ? $override_size : $size[0];
        $imgHeight = $override_size != 0 ? $override_size : $size[1];

        if ($imgHeight > $maxRowHeight) {
            $maxRowHeight = $imgHeight;
        }

        $currentRowWidth += $imgWidth;

        if ($i >= $columns_number && !is_null($columns_number)) {
            if ($currentRowWidth > $maxRowWidth) {
                $maxRowWidth = $currentRowWidth;
            }

            $imgTotalHeight += $maxRowHeight + $padding;
            $maxRowHeight = 0;
            $currentRowWidth = 0;
            $i = 0;
        } else {
            $currentRowWidth += $padding;
        }

        $i++;
    }

    
    if ($i > 1) {
        $currentRowWidth -= $padding;
    }

    if ($currentRowWidth > $maxRowWidth) {
        $maxRowWidth = $currentRowWidth;
    }

    $imgTotalHeight += $maxRowHeight ;

    return ["width" => $maxRowWidth, "height" => $imgTotalHeight];
}

function backimg($output_image, $imgTotalWidth, $imgTotalHeight)
{

    $bgimg = imagecreatetruecolor($imgTotalWidth, $imgTotalHeight);
    $trans = imagecolorallocatealpha($bgimg, 0, 0, 0, 127);
    imagecolortransparent($bgimg, $trans);
    imagesavealpha($bgimg, true);
    imagepng($bgimg, $output_image);
    return $bgimg;
}



function generator($spritePaths, $bgimg, $output_image, $output_style, $override_size, $padding) {
    global $columns_number, $line,$imgTotalHeight;
    $imgTotalWidth = 0;
    $imgTotalHeight = 0;
    $maxRowHeight = 0; 
    $i = 0;
    $tours = 0;
    $css_content = '';
    $totalRowsHeight = 0; 
    $rows=1;
    $completed="";

    foreach ($spritePaths as $key => $value) {
        $size = getimagesize($value);
        $imgWidth = $size[0];
        $imgHeight = $size[1];
        $imgTmp = imagecreatefrompng($value);

        if ($override_size != 0) {
            $imgWidth = $override_size;
            $imgHeight = $override_size;
            $imgTmp = imagescale($imgTmp, $imgWidth, $imgHeight);
        }

        imagesavealpha($imgTmp, true);
        imagealphablending($imgTmp, false);

        
        if ($i > 0 && !is_null($padding)) {
            $imgTotalWidth += $padding;
        }

       
        if ($imgHeight > $maxRowHeight) {
            $maxRowHeight = $imgHeight;
        }

        imagecopy($bgimg, $imgTmp, $imgTotalWidth, $imgTotalHeight, 0, 0, $imgWidth, $imgHeight);

        $i++;
        $imgTotalWidth += $imgWidth;

        if ($i >= $columns_number && !is_null($columns_number)) {
           
            $imgTotalHeight += $maxRowHeight + $padding;
            $totalRowsHeight += $maxRowHeight + $padding;
            $maxRowHeight = 0; 
            $imgTotalWidth = 0;
            $i = 0;
        }

        if(!is_null($columns_number) && ceil(count($spritePaths)/$columns_number)>= $rows){
            $rows =ceil(count($spritePaths)/$columns_number);
        }
        
        $css_content .= ".image{$key}{\n width: {$imgWidth}px;\n height: {$imgHeight}px;\n background: url('{$value}') center center no-repeat;\n background-size: cover;\n}\n";
    }

   
    $imgTotalHeight = $totalRowsHeight;
    if(is_null($columns_number) || $columns_number==0){
    
        $columns_number = count($spritePaths);
    }
    if(is_null($padding)){
        $padding=0;
    }
    
    $position="";
    for ($i=1; $i <=$rows ; $i++) { 
        $position .=".position$i{\norder:$i\n}\n";
    }

    $css_base = "*{\n padding:0;\n margin: 0;\nbox-sizing: border-box;\n}\nbody{\ndisplay: flex;\nwidth: 100%;\n height: 100vh;\njustify-content: center;\nalign-items:center;\nbackground: teal;\n}\n #container{\ndisplay: flex;\nflex-direction: column;\ngrid-gap: {$padding}px;\n}\n.containerimg{\ndisplay:flex;\nflex-direction: row;\ngrid-gap: {$padding}px;\n}\n ";
    $css_file = fopen($output_style, 'a+');
    $final_css = $css_base . $css_content . $position;
    fwrite($css_file, $final_css);
    fclose($css_file);
    
    return $bgimg; 
}

function image($spritePaths, $output_image, $output_style, $override_size, $padding)
{

    if (file_exists($output_style))
        unlink($output_style);

    $data = custumSize($spritePaths, $override_size, $padding);
    $imgTotalWidth = $data["width"];
    $imgTotalHeight = $data["height"];

    $blankImage = backimg($output_image, $imgTotalWidth, $imgTotalHeight);

    $filledImage = generator($spritePaths, $blankImage, $output_image, $output_style, $override_size, $padding);


    imagepng($filledImage, $output_image);
}
image($spritePaths, $output_image, $output_style, $override_size, $padding);
$rows=0;
$mini='';
$parts='';
if(ceil(count($spritePaths)/$columns_number)>= $rows){
    $rows =ceil(count($spritePaths)/$columns_number);
}
$j=0;
$col=1;
$pos=1;
foreach ($spritePaths as $key => $value) {
    if($col<=$columns_number){
            $mini .= "<div class='image$key'></div>\n";
            $col++;
    }else{
        $parts .="<div class='containerimg position$pos'>$mini</div>"; 
        $pos++;
        $mini="";
        $col=1;
        $mini .= "<div class='image$key'></div>\n";
        $col++;
        
    }
}
$parts .="<div class='containerimg position$pos'>$mini</div>";



createHtml($parts,$output_style);

