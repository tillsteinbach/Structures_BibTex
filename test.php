<?php
require_once 'Structures/BibTex.php';
require_once 'Structures/BibTex_Formater.php';
$bibtex = new Structures_BibTex();
$bibtex->setOption('validate', true);
$bibtex->setOption('removeCurlyBraces', true);
$bibtex->setOption('removeTeXSymbols', true);
$bibtex->setOption('parseMonth', true);
$bibtex->setOption('unwrap', true);
$bibtex->setOption('wordWrapWidth', 80);
$bibtex->setOption('wordWrapBreak', "\n\t\t");


$ret    = $bibtex->loadFile('eigene.bib');
//if (PEAR::isError($ret)) {
//    die($ret->getMessage());
//}
$bibtex->parse();

if ($bibtex->hasWarning()) {
    foreach ($bibtex->warnings as $warning) {
        echo 'Warning: '.$warning['warning'].'<br />';
        echo 'Line: '.$warning['entry'].'<hr />';
    }
}

$formater = new Structures_BibTex_Formater();

foreach($bibtex->data as $entry){
	echo "\n";
	echo $formater->formatByStyle($entry, 'mystyle');
	echo "\n";
}


?>
