<?php
require '../vendor/autoload.php';


ob_start();
try{
	$url	= new \CeusMedia\Sitemap\Model\Url( "http://example.com/#1" );
	$url->setDatetime( date( "Y-m-d" ) );
	$url->setPriority( "0.1" );
	$url->setFrequency( "weekly" );

	$sitemap1Url		= "http://example.com/sitemap-1.xml";
	$sitemap1Date	= "2015-01-01";
	$sitemap1		= new \CeusMedia\Sitemap\Model\Map( $sitemap1Url, $sitemap1Date );
	$sitemap1->addUrl( $url );
	$sitemap1->add( "http://example.com/#2", date( "Y-m-d" ), "daily", "0.2" );
	$sitemap1Xml	= \CeusMedia\Sitemap\Generator::renderSitemap( $sitemap1 );

	print '<h3>Generating a sitemap</h3>';
	print '<xmp>'.$sitemap1Xml.'</xmp>';

	print '<h3>Reading a sitemap</h3>';
	$sitemap2	= \CeusMedia\Sitemap\Reader::readSitemap( $sitemap1Xml );
	foreach( $sitemap2->getUrls() as $url ){
		print 'Item1: <br/>';
		print '- Location: '.$url->getLocation().'<br/>';
		print '- Datetime: '.$url->getDatetime().'<br/>';
		print '- Frequency: '.$url->getFrequency().'<br/>';
		print '- Priority: '.$url->getPriority().'<br/>';
	}
	$sitemap2->setUrl( "http://example.com/sitemap-2.xml" );

	$index		= new \CeusMedia\Sitemap\Model\Index();
	$index->addSitemap( $sitemap1 );
	$index->addSitemap( $sitemap2 );
	$indexXml	= \CeusMedia\Sitemap\Generator::renderSitemapIndex( $index );

	print '<h3>Generating an index</h3>';
	print '<xmp>'.$indexXml.'</xmp>';

}
catch( Exception $e ){
	UI_HTML_Exception_Page::display( $e );
	exit;
}

$body	= '
<div class="container">
	<h1 class="muted">CeusMedia Component Demo</h1>
	<h2>Sitemap</h2>
	'.ob_get_clean().'
</div>';

$page	= new UI_HTML_PageFrame();
$page->addStylesheet( "http://cdn.int1a.net/css/bootstrap.min.css" );
$page->addBody( $body );
print $page->build();

