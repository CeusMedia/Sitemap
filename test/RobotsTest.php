<?php
require_once 'prepareTest.php';
class RobotsTest extends PHPUnit_Framework_TestCase{

	public function setUp(){
		$this->path	= dirname( __FILE__ ).DIRECTORY_SEPARATOR;
		$this->fileNone		= $this->path."robots.empty.txt";
		$this->fileMany		= $this->path."robots.many.txt";
		$this->fileWith		= $this->path."robots.with.txt";
		$this->fileWork		= $this->path."robots.work.txt";
		$this->fileMiss		= $this->path."robots.notexisting.txt";
		$this->sitemapUrl	= "http://example.org/sitemap.xml";
		$this->sitemapUrl1	= str_replace( ".xml", "1.xml", $this->sitemapUrl );
		$this->sitemapUrl2	= str_replace( ".xml", "2.xml", $this->sitemapUrl );
		$this->sitemapUrl3	= str_replace( ".xml", "3.xml", $this->sitemapUrl );
	}

	public function tearDown(){
		@unlink( $this->fileWork );
	}

	public function testHas_Found(){
		$assertion	= TRUE;
		$creation	= CMM_SGT_Robots::has( $this->fileWith );
		$this->assertEquals( $assertion, $creation );
	}

	public function testHas_FoundInMany(){
		$assertion	= TRUE;
		$creation	= CMM_SGT_Robots::has( $this->fileMany );
		$this->assertEquals( $assertion, $creation );
	}

	public function testHas_FoundUrl(){
		$assertion	= TRUE;
		$creation	= CMM_SGT_Robots::has( $this->fileWith, $this->sitemapUrl );
		$this->assertEquals( $assertion, $creation );
	}

	public function testHas_NotFound(){
		$assertion	= FALSE;
		$creation	= CMM_SGT_Robots::has( $this->fileNone );
		$this->assertEquals( $assertion, $creation );
	}

	public function testHas_ExceptionNotExisting(){
		try{
			CMM_SGT_Robots::has( $this->fileMiss );
			$this->fail('An expected exception has not been raised.');
		}
		catch( Exception $e ){}
	}

	public function testSet(){
		copy( $this->fileNone, $this->fileWork );
		CMM_SGT_Robots::set( $this->fileWork, $this->sitemapUrl );

		$assertion	= TRUE;
		$creation	= CMM_SGT_Robots::has( $this->fileWork );
		$this->assertEquals( $assertion, $creation );
		
		$assertion	= TRUE;
		$creation	= CMM_SGT_Robots::has( $this->fileWork, $this->sitemapUrl );
		$this->assertEquals( $assertion, $creation );

		$assertion	= File_Reader::load( $this->fileWith );
		$creation	= File_Reader::load( $this->fileWork );
		$this->assertEquals( $assertion, $creation );
	}

	public function testSet_Many(){
		copy( $this->fileNone, $this->fileWork );
		CMM_SGT_Robots::set( $this->fileWork, $this->sitemapUrl1 );
		CMM_SGT_Robots::set( $this->fileWork, $this->sitemapUrl2 );
		CMM_SGT_Robots::set( $this->fileWork, $this->sitemapUrl3 );

		$assertion	= TRUE;
		$creation	= CMM_SGT_Robots::has( $this->fileWork );
		$this->assertEquals( $assertion, $creation );
		
		$assertion	= TRUE;
		$creation	= CMM_SGT_Robots::has( $this->fileWork, $this->sitemapUrl1 );
		$this->assertEquals( $assertion, $creation );
		
		$assertion	= TRUE;
		$creation	= CMM_SGT_Robots::has( $this->fileWork, $this->sitemapUrl2 );
		$this->assertEquals( $assertion, $creation );
		
		$assertion	= TRUE;
		$creation	= CMM_SGT_Robots::has( $this->fileWork, $this->sitemapUrl3 );
		$this->assertEquals( $assertion, $creation );

		$assertion	= File_Reader::load( $this->fileMany );
		$creation	= File_Reader::load( $this->fileWork );
		$this->assertEquals( $assertion, $creation );
	}

	public function testRenderMetaTag(){
		$assertion	= '';
		$creation	= CMM_SGT_Robots::renderMetaTag();
		$this->assertEquals( $assertion, $creation );

		$assertion	= '<meta name="robots" content="NOINDEX"/>';
		$creation	= CMM_SGT_Robots::renderMetaTag( TRUE );
		$this->assertEquals( $assertion, $creation );

		$assertion	= '<meta name="robots" content="NOFOLLOW"/>';
		$creation	= CMM_SGT_Robots::renderMetaTag( FALSE, TRUE );
		$this->assertEquals( $assertion, $creation );

		$assertion	= '<meta name="robots" content="NOINDEX, NOFOLLOW"/>';
		$creation	= CMM_SGT_Robots::renderMetaTag( TRUE, TRUE );
		$this->assertEquals( $assertion, $creation );
	}
}
