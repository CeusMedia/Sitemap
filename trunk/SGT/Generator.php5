<?php
/**
 *	Generator for sitemaps and sitemap indices.
 *
 *	Copyright (c) 2010-2013 Christian Würker / {@link http://ceusmedia.de/ Ceus Media}
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
 *	@category		cmModules
 *	@package		SGT
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2010-2013 {@link http://ceusmedia.de/ Ceus Media}
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			http://code.google.com/p/cmmodules/
 *	@since			0.3.0
 *	@version		$Id$
 */
/**
 *	Generator for sitemaps and sitemap indices.
 *	@category		cmModules
 *	@package		SGT
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2010-2013 {@link http://ceusmedia.de/ Ceus Media}
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			http://code.google.com/p/cmmodules/
 *	@since			0.3.0
 *	@version		$Id$
 */
class CMM_SGT_Generator{

	static protected $maxUrls		= 50000;
	static protected $maxMegabytes	= 10;
	static protected $compression	= 0;
	
	/**
	 *	...
	 *	@static
	 *	@access		public
	 *	@param		CMM_SGT_Sitemap $sitemap
	 *	@return		type
	 *	@throws		OutOfBoundsException
	 *	@throws		OutOfRangeException
	 */
	static public function renderSitemap( CMM_SGT_Sitemap $sitemap, $compression = NULL ){
		if( self::$maxUrls && count( $sitemap ) > self::$maxUrls )
			throw new OutOfBoundsException( 'Sitemap has more than '.self::$maxUrls.' URLs and needs to be spitted' );

		$tree	= new XML_DOM_Node( 'urlset' );
		$tree->setAttribute( 'xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9' );
		foreach( $sitemap->getUrls() as $url ){
			$node	= new XML_DOM_Node( 'url' );
			$node->addChild( new XML_DOM_Node( 'loc', $url->getLocation() ) );
			if( ( $datetime = $url->getDatetime() ) )
				$node->addChild( new XML_DOM_Node( 'lastmod', $datetime ) );
			$tree->addChild( $node );
		}
		$xml		= XML_DOM_Builder::build( $tree );
		if( self::$maxMegabytes && strlen( $xml ) > self::$maxMegabytes * 1024 * 1024 )
			throw new OutOfBoundsException( 'Rendered sitemap is to large (max: '.self::$maxMegabytes.' MB)' );
		$compression	= is_null( $compression ) ? self::$compression : $compression;
		if( $compression )
			$xml	= CMM_SGT_Compressor::compressString( $xml, $compression );
		return $xml;
	}

	/**
	 *	Returns XML string of given sitemap index.
	 *	@static
	 *	@access		public
	 *	@param		CMM_SGT_Sitemap_Index	$index	Sitemap index data object
	 *	@param		integer		$compression		Compression method (see CMM_SGT_Compressor)
	 *	@return		string		XML string of sitemap index
	 */
	static public function renderSitemapIndex( CMM_SGT_Sitemap_Index $index, $compression = NULL ){
		$tree	= new XML_DOM_Node( 'sitemapindex' );										//  
		$tree->setAttribute( 'xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9' );		//  
		foreach( $index->getSitemaps() as $sitemap ){										//  
			$node	= new XML_DOM_Node( 'sitemap');											//  
			$node->addChild( new XML_DOM_Node( 'loc', $sitemap->getUrl() ) );				//  
			if( $sitemap->getDatetime() )													//  
				$node->addChild( new XML_DOM_Node( 'lastmod', $sitemap->getDatetime() ) );	//  
			$tree->addChild( $node );														//  
		}
		$xml		= XML_DOM_Builder::build( $tree );
		$compression	= is_null( $compression ) ? self::$compression : $compression;
		if( $compression )
			$xml	= CMM_SGT_Compressor::compressString( $xml, $compression );
		return $xml;
	}

	/**
	 *	Sets limit of URLs in sitemap.
	 *	Upper limit is 50000. Setting 0 means unlimited.
	 *	@static
	 *	@access		public
	 *	@param		integer		$number			Maximum number of URLs in sitemap.
	 *	@return		void
	 *	@throws		OutOfBoundsException		if number is lower than 0 or greater than 50000
	 */
	static public function setMaxUrls( $number ){
		if( $number < 0 || $number > 50000 )
			throw new OutOfBoundsException( 'URL limit must at least 0 (=unlimited) and atmost 50000' );
		self::$maxUrls	= $number;
	}

	/**
	 *	Sets size limit of uncompressed sitemap XML in megabytes.
	 *	Upper limit is 50 MB. Setting 0 means unlimited.
	 *	@access		public
	 *	@param		float|integer	$number		Maximum megabytes
	 *	@return		void
	 *	@throws		OutOfBoundsException		if given number is lower than 0 or greater than 50
	 */
	static public function setMaxMegabytes( $number ){
		if( $number < 0 || $number > 50 )
			throw new OutOfBoundsException( 'File size limit must at least 0 (=unlimited) and atmost 50' );
		self::$maxMegabytes	= $number;
	}
}
?>