<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 *
 * @file
 */

namespace MediaWiki\Extension\Piwigo;

use FormatJson;
use Parser;
use PPFrame;

class Hooks implements
	\MediaWiki\Hook\BeforePageDisplayHook,
	\MediaWiki\Hook\ParserFirstCallInitHook
{

	/**
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/BeforePageDisplay
	 * @param \OutputPage $out
	 * @param \Skin $skin
	 */
	public function onBeforePageDisplay( $out, $skin ): void {
		$config = $out->getConfig();


		if ( $config->get( 'PiwigoURL' ) ) {
			$out->addModules( 'oojs-ui-core' );
			$out->addHTML( \Html::element( 'p', [], 'Piwigo was here: ' . $config->get( 'PiwigoURL' ) ) );
		}
	}

	/**
	 * Register parser hooks to add the piwigo keyword
	 *
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/ParserFirstCallInit
	 * @see https://www.mediawiki.org/wiki/Manual:Parser_functions
	 * @param Parser $parser
	 * @throws \MWException
	 */
	public function onParserFirstCallInit( $parser ) {

		// Add the following to a wiki page to see how it works:
		// <piwigo foo="bar" baz="quux" />
		$parser->setHook( 'piwigo', [ self::class, 'parserKeywordPiwigo' ] );


		// Add the following to a wiki page to see how it works:
		// {{#piwigo: hello | hi | there }}
		$parser->setFunctionHook( 'piwigo', [ self::class, 'parserFunctionPiwigo' ] );

		return true;
	}

	/**
	 * Implements tag function, <piwigo/>, which enables
	 * the piwigo gallery on a page.
	 *
	 * @param string $input input between the tags (ignored)
	 * @param array $args tag arguments
	 * @param Parser $parser the parser
	 * @param PPFrame $frame the parent frame
	 * @return string to replace tag with
	 */
	public static function parserKeywordPiwigo(
		$input,
		array $args,
		Parser $parser,
		PPFrame $frame
	) {
		// $parser->getOutput()->updateCacheExpiry( 0 );
		// $cs = CommentStreams::singleton();
		// $cs->enableCommentsOnPage();
		$parser->getOutput()->addModules( 'ext.piwigo' );
		$parser->getOutput()->addModules( 'ext.baguetteBox' );

		$ret = '<div>This is the gallery</div>';

		if ( isset( $args['tags'] ) ) {
			$ret = '<div>This is the gallery for tag: '.$args['tags'].'</div>';
		}
		return $ret;
	}

	/**
	 * Parser function handler for {{#piwigo: .. | .. }}
	 *
	 * @param Parser $parser
	 * @param string $value
	 * @param string ...$args
	 * @return string HTML to insert in the page.
	 */
	public static function parserFunctionPiwigo( Parser $parser, string $value, ...$args ) {
		$piwigo = [
			'value' => $value,
			'arguments' => $args,
		];
		$parser->getOutput()->addModules( 'ext.piwigo' );
		$parser->getOutput()->addModules( 'ext.baguetteBox' );

		return '<pre>piwigo Function: '
			. htmlspecialchars( FormatJson::encode( $piwigo, /*prettyPrint=*/true ) )
			. '</pre>';
	}
}
