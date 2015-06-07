<?php
/**
 *	Generator for sitemaps and sitemap indices.
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
 *	Generator for sitemaps and sitemap indices.
 *	@category		cmModules
 *	@package		SGT
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2013 {@link http://ceusmedia.de/ Ceus Media}
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			http://code.google.com/p/cmmodules/
 *	@since			0.3.0
 *	@version		$Id$
 */
class CMM_SGT_Robots{
	
	static $regexKey		= "/^(Sitemap|sitemap|SITEMAP):/";
	static $regexComplete	= "/^(Sitemap|sitemap|SITEMAP):(\s*)(%s)$/";

	/**
	 *	Extends existing robots.txt by sitemap entry if not existing.
	 *	Alias for set().
	 *	@static
	 *	@access		public
	 *	@param		string		$filePath	Path to robots.txt
	 *	@param		string		$url		Sitemap URL
	 *	@return		boolean
	 */
	static public function add( $filePath, $url ){
		return self::set( $filePath, $url );
	}

	/**
	 *	Removes all sitemap entries from robots.txt file.
	 *	Alias for remove() without seconds argument (URL).
	 *	@static
	 *	@access		public
	 *	@param		string		$filePath	Path to robots.txt
	 *	@return		boolean
	 */
	static public function clear( $filePath ){
		return (bool) self::remove( $filePath );
	}

	/**
	 *	Indicates whether a sitemap entry is noted in existing robots.txt file.
	 *	Looks for entry with given URL, otherwise for any entry at all.
	 *	@static
	 *	@access		public
	 *	@param		string		$filePath	Path to robots.txt
	 *	@param		string		$url		Sitemap URL (optional)
	 *	@return		boolean		
	 */
	static public function has( $filePath, $url = NULL ){
		$pattern	= self::$regexKey;
		if( $url !== NULL ){
			$pattern	= sprintf( self::$regexComplete, preg_quote( trim( $url ), '/' ) );
		}
		foreach( File_Reader::loadArray( $filePath ) as $line ){
			if( preg_match( $pattern, trim( $line ) ) ){
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 *	Removes sitemap entries from robots.txt file.
	 *	Looks for entry with given URL, otherwise for all entries.
	 *	@static
	 *	@access		public
	 *	@param		string		$filePath	Path to robots.txt
	 *	@param		string		$url		Sitemap URL (optional)
	 *	@return		integer		Number of removed sitemap entries
	 */
	static public function remove( $filePath, $url = NULL ){
		$editor		= new File_Editor( $filePath );
		$lines		= $editor->readArray();

		$list		= array();
		$pattern	= self::$regexKey;
		if( $url !== NULL ){
			$pattern	= sprintf( self::$regexComplete, preg_quote( trim( $url ), '/' ) );
		}
		foreach( $lines as $line ){
			if( !preg_match( $pattern, trim( $line ) ) ){
				$list[]	= $line;
			}
		}
		$nrDiff	= count( $lines ) - count( $list );
		if( $nrDiff === 0 )
			$editor->writeArray( $lines );
		return $nrDiff;
	}

	/**
	 *	Render HTML meta tag for robot rules.
	 *	@static
	 *	@access		public
	 *	@param		boolean		$noIndex		Flag: 
	 *	@param		boolean		$noFollow		Flag: search engine robot should not follow links within this HTML page
	 *	@return		string|NULL					Rendered HTML meta tag or NULL if no rules where activated
	 */
	static public function renderMetaTag( $noIndex = FALSE, $noFollow = FALSE ){
		$rules	= array();
		if( $noIndex )
			$rules[]	= "NOINDEX";
		if( $noFollow )
			$rules[]	= "NOFOLLOW";
		if( !count( $rules ) )
			return NULL;
		$attributes	= array(
			'name'		=> 'robots',
			'content'	=> join( ', ', $rules ),
		);
		return UI_HTML_Tag::create( 'meta', NULL, $attributes );
	}

	/**
	 *	Renders sitemap entry for robots.txt.
	 *	@static
	 *	@access		public
	 *	@param		string		$url		Sitemap URL
	 *	@return		string
	 */
	static public function renderText( $url ){
		return "Sitemap: ".trim( $url );
	}

	/**
	 *	Extends existing robots.txt by sitemap entry if not existing.
	 *	@static
	 *	@access		public
	 *	@param		string		$filePath	Path to robots.txt
	 *	@param		string		$url		Sitemap URL
	 *	@return		boolean
	 */
	static public function set( $filePath, $url ){
		$editor		= new File_Editor( $filePath );
		$lines		= explode( PHP_EOL, trim( $editor->readString() ) );
		$pattern	= sprintf( self::$regexComplete, preg_quote( trim( $url ), '/' ) );
		foreach( $lines as $line ){
			if( preg_match( $pattern, trim( $line ) ) ){
				return FALSE;
			}
		}
		if( !self::has( $filePath ) )
			$lines[]	= "";
		$lines[]	= self::renderText( $url ).PHP_EOL;
		return (bool) $editor->writeArray( $lines );
	}
}
?>
