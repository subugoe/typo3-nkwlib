<?php
/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Nils K. Windisch <windisch@sub.uni-goettingen.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

/**
 * Helper for some specific SUB internal helper functions
 * 
 * @author Nils K. Windisch <windisch@sub.uni-goettingen.de>
 * @author Ingo Pfennigstorf <pfennigstorf@sub.uni-goettingen.de>
 */
class tx_nkwlib {

	public $extKey;
	public $conf;
	public $language;

	/**
	 * Returns the first letter of a String parameter
	 * 
	 * @param string $str
	 * @return char
	 */
	public static function getFirstLetter($str) {
		$str = strtoupper(mb_substr($str, 0, 1, 'UTF-8'));
		return $str;
	}

	/**
	 * Geocode an address using the Google Maps API
	 * 
	 * @param string $str
	 * @return string
	 */
	public static function geocodeAddress($str) {
		$getThis = 'http://maps.google.com/maps/api/geocode/json?address=' . urlencode($str) . '&sensor=false';
		$json = file_get_contents($getThis);
		$tmp = json_decode($json, TRUE);
		$return = $tmp;
		return $return;
	}

	/**
	 * Extracts and returns the URL of the current Installation
	 * 
	 * @param string $clean
	 * @return string
	 */
	public static function getPageUrl($clean = FALSE) {
		$url = $GLOBALS['TSFE']->baseUrl . $GLOBALS['TSFE']->anchorPrefix;
		if ($clean) {
			$tmp = explode('?', $url);
			$url = $tmp[0];
		}
		return $url;
	}

	/**
	 * Sets the language
	 * 
	 * @param boolean $str
	 * @return void
	 */
	public static function setLanguage($str = FALSE) {
		if ($GLOBALS['TSFE']->sys_page->sys_language_uid === TRUE) {
			self::$language = $GLOBALS['TSFE']->sys_page->sys_language_uid;
		} else {
			self::$language = $str;
		}
	}

	/**
	 * Returns the Syslanguage UID
	 * 
	 * @return int
	 */
	public static function getLanguage() {
		$lang = $GLOBALS['TSFE']->sys_page->sys_language_uid;
		return $lang;
	}

	/**
	 * Returns the current page UID
	 * 
	 * @return int
	 */
	public static function getPageUid() {
		$pageUid = $GLOBALS['TSFE']->id;
		return $pageUid;
	}

	/**
	 * Returns a language String
	 * Convention is that language UID = 0 is german and UID = 1 english
	 * 
	 * @param int $lang
	 * @return string
	 */
	public static function getLanguageStr($lang) {
		$lang = intval($lang);
		$return = '';
		if ($lang === 0) {
			$return = 'de';
		} elseif ($lang === 1) {
			$return = 'en';
		}
		return $return;
	}

	/**
	 * Get Keywords for a page
	 * 
	 * @param int $id
	 * @param int $lang
	 * @param boolean $mode
	 * @param boolean $landingpage
	 * @return string 
	 */
	public static function keywordsForPage($id, $lang, $mode = FALSE, $landingpage = FALSE) {

		$cObj = t3lib_div::makeInstance('tslib_cObj');

		if ($lang == 0) {
			$sep = '_de';
		} elseif ($lang == 1) {
			$sep = '_en';
		}
		$pageInfo = self::pageInfo($id, $lang);
		if (!empty($pageInfo['tx_nkwkeywords_keywords'])) {
			if ($mode == 'header') {
				$tmp = explode(',', $pageInfo['tx_nkwkeywords_keywords']);
				foreach ($tmp AS $key => $value) {
					$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
									'*',
									'tx_nkwkeywords_keywords',
									"uid = '" . $value . "'",
									'',
									'',
									'');
					while ($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
						$tmpList .= $row1['title' . $sep] . ',';
					}
				}
				$str .= substr($tmpList, 0, -1);
			} elseif ($mode == 'infobox') {
				$tmp = explode(',', $pageInfo['tx_nkwkeywords_keywords']);
				foreach ($tmp AS $key => $value) {
					$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
									'*',
									'tx_nkwkeywords_keywords',
									'uid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($value, 'tx_nkwkeywords_keywords'),
									'',
									'',
									'');
					while ($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
						$str .= '<li>';

						$cObj->typoLink(
							$row1['title' . $sep],
							array(
								'parameter' => $landingpage,
								'useCacheHash' => TRUE,
								'additionalParams' => '&tx_nkwkeywords[id]=' . $value
								)
							);
						$str .= '<a title="' . $row1['title' . $sep] . '" href="' . $cObj->lastTypoLinkUrl . '">' . $row1['title' . $sep] . '</a>';
						$str .= '</li>';
					}
				}
			}
		}
		return $str;
	}

	/**
	 * Get the page title of a page in your desired language
	 * 
	 * @param int $id
	 * @param int $lang
	 * @return string 
	 */
	public static function getPageTitle($id, $lang = 0) {
		if ($lang > 0) {
			$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'title',
							'pages_language_overlay',
							'pid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($id, 'pages_language_overlay'),
							'',
							'',
							'');
			while ($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
				$title = $row1['title'];
			}
		}
		if ($lang == 0 || ($lang > 0 && !$title)) {
			$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'*',
							'pages',
							'uid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($id, 'pages'),
							'',
							'',
							'');
			while ($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
				$title = $row1['title'];
			}
		}
		return $title;
	}

	/**
	 * Get page informations about uid, pid, title, keywords, ...
	 * 
	 * @param int $id
	 * @param int $lang
	 * @return array
	 */
	public static function pageInfo($id, $lang = FALSE) {
		if ($lang > 0) {
			$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'*',
							'pages_language_overlay',
							'pid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($id, 'pages_language_overlay'),
							'',
							'',
							'');
		} else {
			$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'*',
							'pages',
							'uid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($id, 'pages'),
							'',
							'',
							'');
		}
		while ($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
			$pageInfo['uid'] = $row1['uid'];
			$pageInfo['pid'] = $row1['pid'];
			$pageInfo['title'] = $row1['title'];
			$pageInfo['keywords'] = $row1['keywords'];
			$pageInfo['tx_nkwsubmenu_knot'] = $row1['tx_nkwsubmenu_knot'];
			$pageInfo['tx_nkwkeywords_keywords'] = $row1['tx_nkwkeywords_keywords'];
		}
		if ($lang > 0) {
			$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'tx_nkwkeywords_keywords',
							'pages',
							'uid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($pageInfo['pid'], 'pages'),
							'',
							'',
							'');
			while ($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
				$pageInfo['tx_nkwkeywords_keywords'] = $row1['tx_nkwkeywords_keywords'];
			}
		}
		return $pageInfo;
	}

	/**
	 * Get the Node ID
	 *
	 * @param int $id
	 * @return type 
	 */
	public static function knotId($id) {

		$return = '';
		$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'uid, pid, tx_nkwsubmenu_knot',
						'pages',
						'uid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($id, 'pages'),
						'',
						'',
						'');
		while ($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
			if ($row1['tx_nkwsubmenu_knot']) {
				$return = $row1['uid'];
			} elseif ($row1['pid'] != 3) {
				$return = self::knotID($row1['pid']);
			}
		}
		return $return;
	}

	/**
	 * Get the IDds of a pagetree as Array
	 * 
	 * @param int $startId
	 * @return array
	 */
	public static function getPageTreeIds($startId) {
		$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'uid',
						'pages',
						'pid = ' . $startId . ' AND deleted = 0 AND hidden = 0',
						'',
						'sorting ASC',
						'');
		while ($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
			$children = self::getPageTreeIds($row1['uid']);
			if ($children) {
				$tree[$row1['uid']]['children'] = self::getPageTreeIds($row1['uid']);
			} else {
				$tree[$row1['uid']]['children'] = 0;
			}
		}
		return $tree;
	}

	/**
	 * Get child page of a page UID
	 * 
	 * @param int $id
	 * @todo Should maybe only return true or false and not false or Array
	 * @return array
	 */
	public static function getPageChildIds($id) {
		$i = 0;
		$arr = array();
		$return = array();

		$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'uid',
						'pages',
						'pid = ' . $id . ' AND deleted = 0 AND hidden = 0',
						'',
						'sorting ASC',
						'');

		while ($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
			$arr[$i]['uid'] = $row1['uid'];
			$i++;
		}
		if ($i > 0) {
			$return = $arr;
		} else {
			$return = FALSE;
		}

		return $return;
	}

	/**
	 * Checks if a page has child records
	 * 
	 * @param int $id
	 * @param int $lang
	 * @todo Should maybe only return true or false and not false or Array
	 * @return <boolean or Array>
	 */
	public static function pageHasChild($id, $lang = 0) {
		$i = 0;
		$arr = array();
		$res = $GLOBALS['TYPO3_DB']->SELECTquery(
							'*',
							'pages LEFT JOIN pages_language_overlay ON pages.uid=pages_language_overlay.pid',
							'pages.pid = ' . $id . ' AND pages.deleted = 0 AND pages.hidden = 0 AND sys_language_uid = ' . $lang,
							'',
							'pages.sorting ASC',
							'');
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			t3lib_div::devLog ('ab', 'nkwlib', 0, $row);

			if ($lang > 0) {
				$arr[$i]['uid'] = $row['pid'];
			} else {
				$arr[$i]['uid'] = $row['pages.uid'];
			}
				$arr[$i]['title'] = $row['title'];
			$arr[$i]['tx_nkwsubmenu_in_menu'] = $row['tx_nkwsubmenu_in_menu'];
			$i++;
		}
		if ($i > 0) {
			$return = $arr;
		} else {
			$return = FALSE;
		}
	return $return;
	}

	/**
	 * Needs a key-value pair and extracts the first Letter of the value, checks
	 * if it is unique and sorts it alphabetically
	 * 
	 * @param array $arr
	 * @return array
	 */
	public static function alphaListFromArray($arr) {
		$list = array();
		foreach ($arr AS $key => $value) {
			if ($value) {
				$letter = strtoupper($value);
				array_push($list, $letter{0});
			}
		}
		$list = array_unique($list);
		return $list;
	}

	/**
	 * check if a page uses the content of another page "content_from_pid"
	 * 
	 * @param int $id
	 * @todo check if we should only return true or false and not false or an id
	 * @return int 
	 */
	public static function checkForAlienContent($id) {

		$return = '';

		$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'uid, content_from_pid',
						'pages',
						'uid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($id, 'pages'),
						'',
						'',
						'');
		while ($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
			$contentFromPid = $row1['content_from_pid'];
		}
		if ($contentFromPid) {
			$return = $contentFromPid;
		} else {
			$return = FALSE;
		}
		return $return;
	}

	/**
	 * Fragment of an SQL String to determine something is visible at the moment
	 * 
	 * @return string 
	 */
	public static function queryStartEndTime() {
		$str = '((starttime < ' . time() . ' AND endtime > ' . time() . ')' .
			' OR (starttime = 0 AND endtime = 0))';

		return $str;
	}

	/**
	 * Returns an Array Containing the UID and header field of content elements 
	 * of a page
	 * If no content element it returns false @todo maybe improve return values
	 * 
	 * @param int $id
	 * @param int $lang
	 * @todo check if we should only return true or false and not false or an id
	 * @return array
	 */
	public static function pageContent($id, $lang = FALSE) {
		$i = 0;
		$arr = array();
		$return = '';

		if ($lang > 0) {
			$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'uid, header, colPos',
							'tt_content',
							'pid = ' . $id . ' AND deleted = 0 AND hidden = 0 AND sys_language_uid = ' . $lang . ' AND t3ver_wsid != "-1"',
							'',
							'sorting ASC',
							'');
		} else {
			$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'uid, header, colPos',
							'tt_content',
							'pid = ' . $id . ' AND deleted = 0 AND hidden = 0' .
								' AND sys_language_uid = 0 AND t3ver_wsid != -1' .
								' AND ' . self::queryStartEndTime(),
							'',
							'sorting ASC',
							'');
		}
		while ($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
			$arr[$i]['uid'] = $row1['uid'];
			$arr[$i]['header'] = $row1['header'];
			$arr[$i]['colPos'] = $row1['colPos'];
			$i++;
		}
		if (count($arr) > 0) {
			$return = $arr;
		} else {
			$return = FALSE;
		}
		return $return;
	}

	/**
	 * Get an array of all Keywords added in the TYPO3 page field of a page
	 * 
	 * @param int $id
	 * @param int $lang
	 * @todo check if we should only return true or false and not false or an id
	 * @return array
	 */
	public static function pageKeywordsList($id, $lang = FALSE) {

		$return = '';
		$pageInfo = self::pageInfo($id, $lang);
		$keywords = explode(',', $pageInfo['keywords']);
		if (is_array($keywords)) {
			$return = $keywords;
		} else {
			$return = FALSE;
		}

		return $return;
	}

	/**
	 * Replace Ampersand by equivalent HTML entity
	 * 
	 * @param string $str
	 * @return string
	 */
	public static function formatString($str) {
		$str = preg_replace('/&/', '&amp;', $str);
		return $str;
	}

	/**
	 * Returns today's unix time stamp (day start)
	 * 
	 * @return string
	 */
	public static function hTime() {
		$time = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		return $time;
	}

	/**
	 * Get a humanreadable date format
	 * 
	 * @param string $time
	 * @param int $lang
	 * @return string
	 */
	public static function hReturnFormatDate($time, $lang = FALSE) {
		$date = date('d', $time) . '.' . date('m', $time) . '.' . date('Y', $time);
		if ($lang != 0) {
			$date = date('Y', $time) . '-' . date('m', $time) . '-' . date('d', $time);
		}
		return $date;
	}

	/**
	 * Returns ISO compatible Date
	 * 
	 * @param string $time
	 * @return string
	 */
	public static function hReturnFormatDateSortable($time) {
		$date = date('Y', $time) . '-' . date('m', $time) . '-' . date('d', $time);
		return $date;
	}

	/**
	 * Get the Plugin Configuration for a Plugin
	 * 
	 * @param string $pluginName
	 * @return Array
	 */
	public static function getPluginConf($pluginName) {
		$pluginName .= '.';
		$array = $GLOBALS['TSFE']->tmpl->setup['plugin.'][$pluginName];
		return $array;
	}

	/**
	 * Generates a marker from a flexform field
	 * this marker is used as a subpart in the template
	 * 
	 * @param string $wert
	 * @return string
	 */
	public static function generateMarker($wert) {

			// Configuration of the stdWrap object
		$wrapper = array(
			'case' => 'upper',
			'stripHtml' => '1',
			'wrap' => '###|###'
		);
		$marker = self::$cObj->stdWrap($wert, $wrapper);

		return $marker;
	}
}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/nkwlib/class.tx_nkwlib.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/nkwlib/class.tx_nkwlib.php']);
}
?>