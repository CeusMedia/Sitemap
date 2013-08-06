<?php
/**
 *	Submits sitemap URL to Google and Bing webmaster tools.
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
 *	@category		cmModules
 *	@package		SGT
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2013 {@link http://ceusmedia.de/ Ceus Media}
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			http://code.google.com/p/cmmodules/
 *	@since			0.3.0
 *	@version		$Id$
 */
/**
 *	Submits sitemap URL to Google and Bing webmaster tools.
 *	@category		cmModules
 *	@package		SGT
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2013 {@link http://ceusmedia.de/ Ceus Media}
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			http://code.google.com/p/cmmodules/
 *	@since			0.3.0
 *	@version		$Id$
 */
class CMM_SGT_Reader{

	static protected function convertTreeToSitemapIndex( XML_DOM_Node $tree ){
		$index		= new CMM_SGT_Sitemap_Index();
		$attributes	= array( 'loc', 'lastmod' );
		if( $tree->getNodeName() !== "sitemapindex" )
			throw new Exception( 'Root node of sitemap index must be "sitemapindex"' );
		foreach( $tree->getChildren() as $child ){
			if( $child->getNodeName() !== "sitemap" )
				throw new Exception( 'Missing node of type "sitemap"' );
			$loc = $lastmod = NULL;
			foreach( $child->getChildren() as $node )
				if( in_array( $node->getNodeName(), $attributes ) )
					${$node->getNodeName()}	= $node->getContent();
			if( strlen( trim( $loc ) ) ){
				$sitemap	= self::readSitemapUrl( $loc );
				$sitemap->setUrl( $loc );
				if( strlen( trim( $lastmod ) ) )
					$sitemap->setDatetime ( $lastmod );
				$index->addSitemap( $sitemap );
			}
		}
		return $index;
	}

	static public function detectCompression( $fileName ){
		switch( pathinfo( $fileName, PATHINFO_EXTENSION ) ){
			case 'bz':	return CMM_SGT_Compressor::METHOD_BZIP;
			case 'gz':	return CMM_SGT_Compressor::METHOD_GZIP;
		}
		return CMM_SGT_Compressor::METHOD_NONE;
	}

	static public function readSitemap( $xml ){
		$parser		= new XML_DOM_Parser();
		$tree		= $parser->parse( $xml );
		$sitemap	= new CMM_SGT_Sitemap();
		$attributes	= array( 'loc', 'lastmod', 'frequency', 'priority' );
		if( $tree->getNodeName() !== "urlset" )
			throw new Exception( 'Root node of sitemap must be "urlset"' );
		foreach( $tree->getChildren() as $child ){
			if( $child->getNodeName() !== "url" )
				throw new Exception( 'Missing node of type "url"' );
			$loc = $lastmod = $frequency = $priority = NULL;
			foreach( $child->getChildren() as $node )
				if( in_array( $node->getNodeName(), $attributes ) )
					${$node->getNodeName()}	= $node->getContent();
			if( strlen( trim( $loc ) ) ){
				$url	= new CMM_SGT_Sitemap_URL( $loc );
				if( strlen( trim( $lastmod ) ) )
					$url->setDatetime ( $lastmod );
				if( strlen( trim( $frequency ) ) )
					$url->setFreqency ( $frequency );
				if( strlen( trim( $priority ) ) )
					$url->setDatetime ( $priority );
				$sitemap->addUrl( $url );
			}
		}
		return $sitemap;
	}

	static public function readSitemapFile( $fileName ){
		$xml	= File_Reader::load( $fileName );
		if( ( $method = self::detectCompression( $fileName ) ) )
			$xml	= self::uncompressXml ( $xml, $method );
		return self::readSitemap( $xml );
	}

	static public function readSitemapUrl( $url ){
		$xml		= Net_Reader::readUrl( $url );
		if( ( $method = self::detectCompression( $url ) ) )
			$xml	= self::uncompressXml ( $xml, $method );
		$sitemap	= self::readSitemap( $xml );
		$sitemap->setUrl( $url );
		return $sitemap;
	}

	static public function readIndex( $xml ){
		$parser	= new XML_DOM_Parser();
		$tree	= $parser->parse( $xml );
		return self::convertTreeToSitemapIndex( $tree );
	}

	static public function readIndexFile( $fileName ){
		$tree	= XML_DOM_FileReader::load( $fileName);
		return self::convertTreeToSitemapIndex( $tree );
	}

	static public function readIndexUrl( $url ){
		$tree	= XML_DOM_UrlReader::load( $url );
		return self::convertTreeToSitemapIndex( $tree );
	}
	
	static public function uncompressXml( $xml, $method = 0 ){
		switch( $method ){
			case CMM_SGT_Compressor::METHOD_BZIP:
				return bzdecompress( $xml );
			case CMM_SGT_Compressor::METHOD_GZIP:
				return gzdecode( $xml );
			case CMM_SGT_Compressor::METHOD_NONE:
				return $xml;
		}
	}
}
?>
