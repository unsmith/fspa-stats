<?php
namespace FSPA;

require_once('/var/www/lib/const.php');
require_once('/var/www/lib/DataParser.php');

$infile = DATA_INFILE;
$outfile = '';
$parser = new DataParser($infile);

echo "Creating SQL to import from data file: " . $parser->infile . "\n";
$parser->createSQL();
if ($parser->ok) {
    echo "Parsing successful; output is in: " . $parser->outfile . "\n";
}
else {
    echo "Parsing NOT successful; message(s):\n";
    foreach ($parser->messages as $message) {
        echo "    $message\n";
    }
    echo "Output would have been at: " . $parser->outfile . "\n";
}

?>
