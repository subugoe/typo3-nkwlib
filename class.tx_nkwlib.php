<?php
/***************************************************************
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
***************************************************************/
require_once(PATH_tslib . 'class.tslib_pibase.php');
class tx_nkwlib extends tslib_pibase {
	var $extKey;
	var $conf;
	var $language;
	function getFirstLetter($str) {
		$str = strtoupper(mb_substr($str, 0, 1, 'UTF-8'));
		return $str;
	}
	function geocodeAddress($str) {
		$str = ereg_replace(' ', '+', $str);
		$getThis = 'http://maps.google.com/maps/api/geocode/json?address=' . $str . '&sensor=false';
		$json = file_get_contents($getThis);
		$tmp = json_decode($json, true);
		$return = $tmp;
		return $return;
	}
	function getPageUrl($clean = FALSE) {
		$url = $GLOBALS['TSFE']->baseUrl . $GLOBALS['TSFE']->anchorPrefix;
		if ($clean) {
			$tmp = explode('?', $url);
			$url = $tmp[0];
		}
		return $url;
	}
	function setLanguage($str = FALSE) {
		if ($GLOBALS['TSFE']->sys_page->sys_language_uid = TRUE) {
			$this->language = $GLOBALS['TSFE']->sys_page->sys_language_uid;
		} else {
			$this->language = $str;
		}
	}
	function getLanguage() {
		$lang = $GLOBALS['TSFE']->sys_page->sys_language_uid;
		return $lang;
	}
	function getPageUID() {
		$pageUID = $GLOBALS['TSFE']->id;
		return $pageUID;
	}
	function getLanguageStr($lang) {
		if ($lang == 0) {
			return 'de';
		} else if ($lang == 1) {
			return 'en';
		}
	}
	function keywordsForPage($id, $lang, $mode = FALSE) {
		if ($lang == 0) {
			$sep = '_de';
		} else if ($lang == 1) {
			$sep = '_en';
		}
		$pageInfo = $this->pageInfo($id, $lang);
		if (!empty($pageInfo['tx_nkwkeywords_keywords'])) {
			if ($mode == 'header') {
				$tmp = explode(',', $pageInfo['tx_nkwkeywords_keywords']);
				foreach($tmp AS $key => $value) {
					$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'*', 
							'tx_nkwkeywords_keywords', 
							"uid = '" . $value . "'", 
							'', 
							'', 
							'');
					while($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
						$tmpList .= $row1['title' . $sep] . ',';
					}
				}
				$str .= substr($tmpList, 0, -1);
			} else if ($mode == 'infobox') {
				$tmp = explode(',', $pageInfo['tx_nkwkeywords_keywords']);
				foreach($tmp AS $key => $value) {
					$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'*', 
							'tx_nkwkeywords_keywords', 
							"uid = '" . $value . "'", 
							'', 
							'', 
							'');
					while($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
						$str .= '<li>';
						$str .= $this->pi_LinkToPage(
								$row1['title' . $sep], 
								$GLOBALS['TSFE']->tmpl->flatSetup['keywordslandingpage'], 
								'', 
								array('tx_nkwkeywords[id]' => $value));
						$str .= '</li>';
					}
				}
			}
		}
		return $str;
	}
	function getPageTitle($id, $lang = 0) {
		if ($lang > 0) {
			$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'title', 
				'pages_language_overlay', 
				'pid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($id), 
				'', 
				'', 
				'');
			while($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
				$title = $row1['title'];
			}
		}
		if ($lang == 0 || ($lang > 0 && !$title)) {
			$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'*', 
				'pages', 
				'uid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($id), 
				'', 
				'', 
				'');
			while($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
				$title = $row1['title'];
			}
		}
		return $title;
	}
	function pageInfo($id, $lang = FALSE) {
		if ($lang > 0) {
			$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'*', 
				'pages_language_overlay', 
				'pid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($id), 
				'', 
				'', 
				'');
		} else {
			$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'*', 
				'pages', 
				'uid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($id), 
				'', 
				'', 
				'');
		}
		while($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
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
					'uid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($pageInfo['pid']), 
					'', 
					'', 
					'');
			while($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
				$pageInfo['tx_nkwkeywords_keywords'] = $row1['tx_nkwkeywords_keywords'];
			}
		}
		return $pageInfo;
	}
	function knotID($id) {
		$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid, pid, tx_nkwsubmenu_knot', 
			'pages', 
			'uid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($id), 
			'', 
			'', 
			'');
		while($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
			if ($row1['tx_nkwsubmenu_knot']) {
				return $row1['uid'];
			} else if ($row1['pid'] != 3) {
				return $this->knotID($row1['pid']);
			}
		}
	}
	function getPageTreeIds($startId) {
		$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid', 
			'pages', 
			'pid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($startId) 
				. ' AND deleted = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(0) 
				. ' AND hidden = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(0), 
			'', 
			'sorting ASC', 
			'');
		while($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
			// $tree[$row1['uid']]['test'] = 1;
			$children = $this->getPageTreeIds($row1['uid']);
			if ($children) {
				$tree[$row1['uid']]['children'] = $this->getPageTreeIds($row1['uid']);
			} else {
				$tree[$row1['uid']]['children'] = 0;
			}
		}
		return $tree;
	}
	function getPageChildIds($id) {
		$i = 0;
		$arr = array();
		$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid', 
			'pages', 
			'pid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($id) 
				. ' AND deleted = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(0) 
				. ' AND hidden = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(0), 
			'', 
			'sorting ASC', 
			'');
		// $res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
		// 		'*', 
		// 		'pages', 
		// 		'pid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($id) . " AND deleted = '0' AND hidden = '0'", 
		// 		'', 
		// 		'sorting ASC', 
		// 		'');
		$arr = array();
		while($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
			$arr[$i]['uid'] = $row1['uid'];
			// $arr[$row1['uid']]['uid'] = $row1['uid'];
			// $arr[$row1['uid']];
			// array_push($arr, $row1['uid']);
			$i++;
		}
		if ($i > 0) {
			return $arr;
		} else {
			return FALSE;
		}
	}
	function pageHasChild($id, $lang = 0) {
		$i = 0;
		$arr = array();
		if ($lang > 0) {
			$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'*', 
				'pages_language_overlay', 
				'pid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($id) 
					. ' AND deleted = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(0) 
					. ' AND hidden = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(0) 
					. ' AND sys_language_uid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($lang), 
				'', 
				'', 
				'');
		} else {
			$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'*', 
				'pages', 
				'pid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($id) 
					. ' AND deleted = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(0) 
					. ' AND hidden = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(0), 
				'', 
				'sorting ASC', 
				'');
		}
		while($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
			$arr[$i]['uid'] = $row1['uid'];
			$arr[$i]['title'] = $row1['title'];
			$i++;
		}
		if ($i > 0) {
			return $arr;
		} else {
			return FALSE;
		}
	}
	function alphaListFromArray($arr) {
		$list = array();
		foreach($arr AS $key => $value) {
			if ($value) {
				$letter = strtoupper($value);
				array_push($list, $letter{0});
			}
		}
		$list = array_unique($list);
		return $list;
	}
	// return PID
	// check if a page uses the content of another page "content_from_pid"
	function checkForAlienContent($id) {
		$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'uid, content_from_pid', 
				'pages', 
				'uid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($id), 
				'', 
				'', 
				'');
		while($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
			$content_from_pid = $row1['content_from_pid'];
		}
		if ($content_from_pid) {
			$id = $content_from_pid;
		} else {
			return FALSE;
		}
		return $id;
	}
	function pageContent($id, $lang = FALSE) {
		$i = 0;
		$arr = array();
		if ($lang > 0) {
			$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'uid, header', 
				'tt_content', 
				'pid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($id) 
					. ' AND deleted = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(0) 
					. ' AND hidden = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(0) 
					. ' AND sys_language_uid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($lang) 
					. ' AND t3ver_wsid != ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(-1), 
				'', 
				'sorting ASC', 
				'');
		} else {
			$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid, header', 
			'tt_content', 
			'pid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($id) 
				. ' AND deleted = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(0) 
				. ' AND hidden = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(0) 
				. ' AND sys_language_uid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(0) 
				. ' AND t3ver_wsid != ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(-1), 
			'', 
			'sorting ASC', 
			'');
		}
		while($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
			$arr[$i]['uid'] = $row1['uid'];
			$arr[$i]['header'] = $row1['header'];
			$i++;
		}
		if ($i > 0) {
			return $arr;
		} else {
			return FALSE;
		}
	}
	function pageKeywordsList($id, $lang = FALSE) {
		$pageInfo = $this->pageInfo($id, $lang);
		$keywords = explode(',', $pageInfo['keywords']);
		if (is_array($keywords)) {
			return $keywords;
		} else {
			return FALSE;
		}
	}
	function formatString($str) {
		$str = ereg_replace('&', '&amp;', $str);
		return $str;
	}
	# retruns todays unix time stamp (day start)
	function hTime() {
		$time = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		return $time;
	}
	# returns a humanreadable date format
	function hReturnFormatDate($time, $lang = FALSE) {
		$date = date('d', $time) . '.' . date('m', $time) . '.' . date('Y', $time);
		if ($lang != 0) {
			$date = date('Y', $time) . '-' . date('m', $time) . '-' . date('d', $time);
		}
		return $date;
	}
	function hReturnFormatDateSortable($time) {
		$date = date('Y', $time) . '-' . date('m', $time) . '-' . date('d', $time);
		return $date;
	}
	function getPluginConf($pluginName) {
		$pluginName .= '.';
		$array = $GLOBALS['TSFE']->tmpl->setup['plugin.'][$pluginName];
		return $array;
	}
	# debug output
	function dPrint($str) {
		echo '<pre style="font-size: 11px; line-height: 0.8em; background-color: grey; color: white;">';
		print_r($str);
		echo '</pre>';
	}
}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/nkwlib/class.tx_nkwlib.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/nkwlib/class.tx_nkwlib.php']);
}
?>