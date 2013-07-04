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
class CMM_SGT_Robots{
	static public function renderMetaTag( $index = TRUE, $follow = TRUE ){
		$index	= $index ? "INDEX" : "NOINDEX";
		$follow	= $follow ? "FOLLOW" : "NOFOLLOW";
		$attributes	= array(
			'name'		=> 'robots',
			'content'	=> $index.', '.$follow,
		);
		return UI_HTML_Tag::create( 'meta', NULL, $attributes );
	}

	static public function renderText( $url ){
		return "Sitemap: ".$url;
	}

	static public function extendFile( $fileName, $url ){
		$editor	= new File_Editor( $fileName );
		$text	= $editor->readString();
		foreach( explode( "\n", $text ) as $line )
			if( preg_match( "/^Sitemap: /", $line ) )
				return FALSE;
		$text	.= "\n".self::renderText( $url )."\n";
		return (bool) $editor->writeString( $text );
	}
}
?>
