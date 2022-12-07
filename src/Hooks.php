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

namespace MediaWiki\Extension\WikiSearchLink;

use HtmlArmor;

class Hooks
{

	/**
	 * A little hack to add parameters to a link to the Search page
	 */
	public static function onHtmlPageLinkRendererBegin($linkRenderer, $target, &$text, &$extraAttribs, &$query, &$ret ) {

		$targetedPage = $target->getText();
		if ($targetedPage == 'Search')
		{
			$textValue = HtmlArmor::getHtml($text);
			$matches = array();
			if (preg_match('@"([^"]+)"@', $textValue, $matches))
			{
				$filter = str_replace('=', '^^', $matches[1]);
				$text = preg_replace('@"[^"]+"@', '', $textValue);

				$query['filters'] = $filter;
				$query['order'] = 'desc';
				$query['ordertype'] = 'Modification date';
			}
		}

	}

}
