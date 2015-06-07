<?php
require '../vendor/autoload.php';

$url	= new \CeusMedia\Sitemap\Model\Url( "http://example.com/#1" );
//$url->setDatetime( "2014-01-01" );
$url->setPriority( "0.1" );
$url->setFrequency( "weekly" );

$sitemapUrl		= "http://example.com/sitemap.xml";
$sitemapDate	= date( "Y-m-d" );
$sitemap		= new \CeusMedia\Sitemap\Model\Map( $sitemaprl, $sitemapDate );
$sitemap->addUrl( $url );
$sitemap->add( "http://example.com/#2", date( "Y-m-d" ), "daily", "0.2" );
$xml	= \CeusMedia\Sitemap\Generator::renderSitemap( $sitemap );

print '<xmp>'.$xml;


$sitemap	= \CeusMedia\Sitemap\Reader::readSitemap( $xml );
$url		= array_shift( $sitemap->getUrls() );
print_r( $url->getLocation() );

