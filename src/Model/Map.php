<?php
/**
 *	Data model of sitemaps.
 *
 *	Copyright (c) 2013 Christian Würker / {@link http://ceusmedia.de/ Ceus Media}
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU General Public License as published by
 *	the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *
 *	You should have received a copy of the GNU General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *	@category		Library
 *	@package		CeusMedia_Sitemap_Model
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2013-2015 {@link http://ceusmedia.de/ Ceus Media}
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Sitemap
 */
namespace CeusMedia\Sitemap\Model;
/**
 *	Data model of sitemaps.
 *	@category		Library
 *	@package		CeusMedia_Sitemap_Model
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2013-2015 {@link http://ceusmedia.de/ Ceus Media}
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Sitemap
 */
class Map implements Countable{

	/**	@var		string		$url		URL of sitemap for sitemap index */
	protected $url				= NULL;
	/**	@var		array		$urls		List of mapped URLs */
	protected $urls				= array();
	/**	@var		string		$datetime	Timestamp of sitemap for sitemap index */
	protected $datetime			= NULL;

	/**
	 *	Constructor.
	 *	@access		public
	 *	@param		string		$url		URL of sitemap for sitemap index
	 *	@param		string		$datetime	Timestamp of sitemap for sitemap index
	 *	@return		void
	 */
	public function __construct( $url = NULL, $datetime = NULL ){
		if( $url )
			$this->setUrl( $url );
		if( $datetime )
			$this->setDatetime( $datetime );
	}

	/**
	 *	Add URL to sitemap.
	 *	@access		public
	 *	@param		string		$location		Location of sitemap URL
	 *	@param		string		$datetime		Timestamp
	 *	@param		string		$frequency		...
	 *	@param		string		$priority		...
	 *	@return		void
	 */
	public function add( $location, $datetime = NULL, $frequency = NULL, $priority = NULL ){
		$this->addUrl( new \CeusMedia\Sitemap\Model\Url( $location, $datetime, $frequency, $priority ) );	//
	}

	/**
	 *	Add URL to sitemap.
	 *	@access		public
	 *	@param		\CeusMedia\Sitemap\Model\Url	$url		URL to add to sitemap
	 *	@return		boolean
	 */
	public function addUrl( \CeusMedia\Sitemap\Model\Url $url ){
		foreach( $this->urls as $entry )
			if( $entry->getLocation() === $url->getLocation() )
				return FALSE;
		$this->urls[]	= $url;
		if( $url->getDatetime() ){
			if( !$this->datetime )
				$this->datetime	= $url->getDatetime();
			else{
				$timestamp	= strtotime( $url->getDatetime() );
				if( $timestamp > strtotime( $this->datetime ) )
					$this->datetime	= $url->getDatetime();
			}
		}
		return TRUE;
	}

	/**
	 *	Returns number of mapped URLs.
	 *	@return integer
	 */
	public function count(){
		return count( $this->urls );
	}

	/**
	 *	Return timestamp of sitemap.
	 *	@return		string
	 */
	public function getDatetime(){
		return $this->datetime;
	}

	/**
	 *	Returns URL of sitemap, if set.
	 *	@access		public
	 *	@return		string|NULL		URL of sitemap
	 */
	public function getUrl(){
		return $this->url;
	}

	/**
	 *	Returns list of mapped URLs.
	 *	@access		public
	 *	@return		array		List of mapped URLs
	 */
	public function getUrls(){
		return $this->urls;
	}

	/**
	 *	Returns XML of sitemap. Compression is available.
	 *	@access		public
	 *	@param		integer		$compression		Compression method (see \CeusMedia\Sitemap\Compressor)
	 *	@return		string
	 */
	public function render( $compression = NULL ){
		return \CeusMedia\Sitemap\Generator::renderSitemap( $this, $compression );
	}

	/**
	 *	Renders and saves sitemap as file. Compression is available.
	 *	Enabling compression automatically adds extension to file name.
	 *	@access		public
	 *	@param		string		$fileName			Name of sitemap file
	 *	@param		integer		$compression		Compression method (see \CeusMedia\Sitemap\Compressor)
	 *	@return		integer		Number of written bytes
	 */
	public function save( $fileName, $compression = NULL ){
		$number	= File_Writer::save( $fileName, $this->render() );
		if( $compression )
			$number	= \CeusMedia\Sitemap\Compressor::compressFile( $fileName, $compression );
		return $number;
	}

	/**
	 *	Sets timestamp of sitemap for sitemap index.
	 *	@access		public
	 *	@param		string		$datetime			Timestamp of sitemap (W3C Datetime)
	 *	@see		http://www.w3.org/TR/NOTE-datetime
	 *	@return		void
	 */
	public function setDatetime( $datetime ){
		$this->datetime	= $datetime;
	}

	/**
	 *	Sets URL of sitemap for sitemap index.
	 *	@access		public
	 *	@param		string		$url		URL of sitemap
	 *	@return		void
	 */
	public function setUrl( $url ){
		$this->url	= $url;
	}
}
?>
