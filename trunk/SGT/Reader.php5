<?php
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
