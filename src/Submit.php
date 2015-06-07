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
 *	@category		Library
 *	@package		CeusMedia_Sitemap
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2013-2015 {@link http://ceusmedia.de/ Ceus Media}
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Sitemap
 */
namespace CeusMedia\Sitemap;
/**
 *	Submits sitemap URL to Google webmaster tools.
 *
 *	@category		Library
 *	@package		CeusMedia_Sitemap
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2013-2015 {@link http://ceusmedia.de/ Ceus Media}
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Sitemap
 */
class Submit{

	/**	@var		array		$providers		Map of search engine providers and their submit URLs */
	static public $providers	= array(
		'ask'		=> "http://submissions.ask.com/ping?sitemap=%s",
		'bing'		=> "http://www.bing.com/ping?sitemap=%s",
		'google'	=> "http://www.google.com/webmasters/tools/ping?sitemap=%s",
		'moreover'	=> "http://api.moreover.com/ping?u=%s",
	);

	/**
	 *	Submit sitemap to all registered search engine providers and returns results.
	 *	ATTENTION: Call may needs some seconds depending on number and performance of registered providers.
	 *	@static
	 *	@access		public
	 *	@param		string		$url			URL of sitemap to submit
	 *	@return		array		List of provider results
	 */
	static public function toAll( $url ){
		$list	= array();
		foreach( self::$providers as $key => $url )
			$list[$key]	= self::toProvider( $key, $url );
		return $list;
	}

	/**
	 *	Submit sitemap to search engine provider.
	 *	@static
	 *	@access		public
	 *	@param		string			$provider	Key of provider to submit to (google|bing)
	 *	@param		ADT_URL|string	$url		URL of sitemap to submit
	 *	@return		boolean			Result of request
	 *	@throws		\InvalidArgumentException	if provider key is invalid
	 */
	static public function toProvider( $provider, $url ){
		if( $url instanceof \ADT_URL )
			$url	= (string) $url;
		if( !is_string( $url ) )
			throw new \InvalidArgumentException( 'URL must be string or instance of ADT_URL' );
		if( !array_key_exists( strtolower( $provider ), self::$providers ) ){
			$providers	= join( ', ', array_keys( self::$providers ) );
			throw new \InvalidArgumentException( 'Invalid provider (must be one of '.$providers.')' );
		}
		$url	= sprintf( self::$providers[strtolower( $provider )], urlencode( $url ) );			//  ...
		try{
			$curl	= new \Net_CURL( $url );
			$curl->exec();
			if( (int) $curl->getInfo( \Net_CURL::INFO_HTTP_CODE ) === 200 )
				return TRUE;
		}
		catch( \Exception $e ){}
		return FALSE;
	}
}
?>
