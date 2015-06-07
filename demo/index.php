<?php
require '../vendor/autoload.php';

$sitemap	= new \CeusMedia\Sitemap\Model\Map();
$sitemap->add( "http://example.com/#1" );

$xml	= \CeusMedia\Sitemap\Generator::renderSitemap( $sitemap );

print '<xmp>'.$xml;
