<?php
function htmlgen($line,$output_style){
    $html = "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Document</title>
        <link rel='stylesheet' href='$output_style'>
    </head>
    <body>
        <div id='container'>
            $line
        </div>
    </body>
    </html>";

    return $html;
}
function createHtml($line,$output_style){
    $html_file = fopen("index.html", 'w+');
    fwrite($html_file, htmlgen($line,$output_style));
    fclose($html_file);

}

