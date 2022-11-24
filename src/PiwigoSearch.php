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

use ApiBase;
use ApiMain;
use Wikimedia\ParamValidator\ParamValidator;

class PiwigoSearch extends ApiBase {

	/**
	 * @param ApiMain $main main module
	 * @param string $action name of this module
	 */
	public function __construct( $main, $action ) {
		parent::__construct( $main, $action );
	}

	/**
	 * execute the API request
	 */
	public function execute() {
		$params = $this->extractRequestParams();

		// 'tags': tags,
		// 'tags_multiple': tags_multiple,
		// 'category': category,
		// 'search': search,
		// 'count': count,
		// 'site': site
		$tags = $params['tags'];
		$tags_multiple = $params['tags_multiple'];
		$category = $params['category'];
		$search = $this->mb_rawurlencode($params['search']);
		$count = $params['count'];
		$site = $params['site'];

		if (empty($site))
			$piwigoRootURL = $GLOBALS['wgPiwigoURL'];
		else
			$piwigoRootURL = $site;

		$piwigoWSURL = $piwigoRootURL . "/ws.php?format=json";

		if (!empty($search))
		{
			$piwigoWSURL = $piwigoWSURL . "&method=pwg.images.search&query=" . $search;
		}
		else if (!empty($tags))
		{
			$piwigoWSURL = $piwigoWSURL . "&method=pwg.tags.getImages&tag_id=" . $tags;
		}
		else if (!empty($tags_multiple))
		{
			$parts = explode(',', $tags_multiple);
			$piwigoWSURL = $piwigoWSURL . "&method=pwg.tags.getImages&tag_id[]=" . implode("&tag_id[]=", $parts);
		}
		else if (!empty($category))
		{
			$piwigoWSURL = $piwigoWSURL . "&method=pwg.categories.getImages&cat_id=" . $category;
		}

		if ($count > 0)
			$piwigoWSURL = $piwigoWSURL . "&per_page=" . $count;

		$ch = curl_init($piwigoWSURL);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if ($GLOBALS['env'] == 'dev')
        {
			// Ignore self signed https
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        }

		$data = curl_exec($ch);
		$result = json_decode($data, true);

		if (curl_errno($ch)) {
			$r['error'] = curl_error($ch);
		}

		curl_close($ch);

		$r['result'] = $result;
		$r['ws_url'] = $piwigoWSURL;

        $apiResult = $this->getResult();
        $apiResult->addValue( null, $this->getModuleName(), $r );
	}

	private function mb_rawurlencode($url)
	{
		$encoded='';
		$length=mb_strlen($url);
		for($i=0;$i<$length;$i++){
			$encoded.='%'.wordwrap(bin2hex(mb_substr($url,$i,1)),2,'%',true);
		}
		return $encoded;
	}

	/**
	 * @return array allowed parameters
	 */
	public function getAllowedParams() {
		return [
			'search' => [
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => false
			],
			'tags' => [
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => false
			],
			'tags_multiple' => [
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => false
			],
			'category' => [
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => false
			],
			'site' => [
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => false
			],
			'count' => [
                ParamValidator::PARAM_TYPE => 'integer',
				ParamValidator::PARAM_REQUIRED => false
			]
		];
	}

	/**
	 * @return array examples of the use of this API module
	 */
	public function getExamplesMessages() {
		return [
			'action=' . $this->getModuleName() . '&search=clouds tags:sometag&count=20' =>
			'apihelp-' . $this->getModuleName() . '-example'
		];
	}

	/**
	 * @return string indicates that this API module does not require a CSRF toekn
	 */
	public function needsToken() {
		return false;
	}
}
