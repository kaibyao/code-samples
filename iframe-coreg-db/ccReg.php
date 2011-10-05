<?php
if (empty($_REQUEST['method'])) exit('ERROR: Method not set!');

session_start();
require_once '../../include/config.php';
require_once $GLOBALS['commonRoot'] . "db/db_new.php";

$dbConn = new Db_new();

switch ($_REQUEST['method']) {
	case 'addPageToPath':
		if (!isset($_REQUEST['page']) || !isset($_REQUEST['path']) || !isset($_REQUEST['position'])) exit('ERROR: Missing parameters!');
		
		$query = sprintf("INSERT INTO ccreg_path_order (ccreg_id, page_id, position) VALUES (%s, %s, %s);", $_REQUEST['path'], $_REQUEST['page'], $_REQUEST['position']);
		if ($dbConn->sqlQuery($query, false)) {
			$insertID = mysql_insert_id();
			
			$templateArr = array();
			$query = $dbConn->sqlQuery("SELECT template_id, template_name FROM ccreg_path_templates ORDER BY template_name ASC;", true, true);
			foreach ($query as $row)
				$templateArr[$row['template_id']] = $row['template_name'];
			
			$linkArr = array();
			$query = $dbConn->sqlQuery("SELECT link_id, link_name FROM ccreg_links ORDER BY link_name ASC;", true, true);
			foreach ($query as $row)
				$linkArr[$row['link_id']] = $row['link_name'];
			
			$ret = array(
				'insertID'  => $insertID,
				'templates' => $templateArr,
				'links'     => $linkArr
			);
			
			echo "SUCCESS:".json_encode($ret);
		}
		else echo "ERROR: Did not insert page to path. ". mysql_error();
		break;
	
	case 'updatePathOrder':
		if (!isset($_REQUEST['path']) || !isset($_REQUEST['pathOrder'])) exit('ERROR: Missing parameters!');
		
		$pathOrder = json_decode($_REQUEST['pathOrder'], true);
		
		$errors = array();
		foreach ($pathOrder as $position => $positionArr) {
			foreach ($positionArr as $pageArr) {
				$query = sprintf("UPDATE ccreg_path_order SET position=%s, template_id=%s, link_id=%s WHERE id=%s LIMIT 1;\n", $position, $pageArr[2], $pageArr[3], $pageArr[1]);
				$dbConn->sqlQuery($query, false);
				if (mysql_error()) $errors[] = "query: $query; error: ". mysql_error();
			}
		}
		if (empty($errors)) echo 'SUCCESS';
		else echo 'ERROR: '. var_export($errors, true);
		break;
	
	case 'deletePathPage':
		if (!isset($_REQUEST['orderID'])) exit('ERROR: Missing parameters!');
		
		$query = sprintf("DELETE FROM ccreg_path_order WHERE id=%s;", $_REQUEST['orderID']);
		$deleteQuery = $dbConn->sqlQuery($query, false);
		
		if ($deleteQuery) echo "SUCCESS";
		else echo 'ERROR: '. mysql_error();
		break;
	
	case 'newOwner':
		if (!isset($_REQUEST['tsid']) || !isset($_REQUEST['afid']) || !isset($_REQUEST['ownerName']) || !isset($_REQUEST['adminFee']) || !isset($_REQUEST['revShare'])) exit('ERROR: Missing parameters!');
		
		$capArr = $estArr = array();
		$coregQuery = $dbConn->sqlQuery("SELECT id FROM coreg_offers;", true, true);
		foreach ($coregQuery as $row) {
			$capArr[$row['id']] = (!empty($_REQUEST['coregCap_'. $row['id']])) ? $_REQUEST['coregCap_'. $row['id']] : 0;
			$estArr[$row['id']] = (!empty($_REQUEST['coregEst_'. $row['id']])) ? $_REQUEST['coregEst_'. $row['id']] : 0;
		}
		
		$query = sprintf("INSERT INTO ccreg_owners (tsid, af_key, owner_name, traffic_cap, coreg_payout_estimate, admin_fee_percent, rev_share_percent) VALUES(%s, %s, '%s', '%s', '%s', '%s', '%s');", $_REQUEST['tsid'], $_REQUEST['afid'], mysql_real_escape_string($_REQUEST['ownerName']), mysql_real_escape_string(serialize($capArr)), mysql_real_escape_string(serialize($estArr)), mysql_real_escape_string($_REQUEST['adminFee']), mysql_real_escape_string($_REQUEST['revShare']));
		$ownerQuery = $dbConn->sqlQuery($query, false);
		
		if ($ownerQuery) {
			if (!empty($_REQUEST['redirect'])) {
				header('Location: '. $_REQUEST['redirect']);
				exit;
			} else
				echo "SUCCESS";
		} else echo 'ERROR: Did not insert owner. '. mysql_error();
		break;
	
	case 'updateOwner':
		if (!isset($_REQUEST['tsid']) || !isset($_REQUEST['afid']) || !isset($_REQUEST['ownerName']) || !isset($_REQUEST['ownerID']) || !isset($_REQUEST['adminFee']) || !isset($_REQUEST['revShare'])) exit('ERROR: Missing parameters!');
		
		$capArr = $estArr = array();
		$coregQuery = $dbConn->sqlQuery("SELECT id FROM coreg_offers;", true, true);
		foreach ($coregQuery as $row) {
			$capArr[$row['id']] = (!empty($_REQUEST['coregCap_'. $row['id']])) ? $_REQUEST['coregCap_'. $row['id']] : 0;
			$estArr[$row['id']] = (!empty($_REQUEST['coregEst_'. $row['id']])) ? $_REQUEST['coregEst_'. $row['id']] : 0;
		}
		
		$query = sprintf("UPDATE ccreg_owners SET tsid='%s', af_key='%s', owner_name='%s', traffic_cap='%s', coreg_payout_estimate='%s', admin_fee_percent='%s', rev_share_percent='%s' WHERE owner_id=%s;", $_REQUEST['tsid'], $_REQUEST['afid'], mysql_real_escape_string($_REQUEST['ownerName']), mysql_real_escape_string(serialize($capArr)), mysql_real_escape_string(serialize($estArr)), mysql_real_escape_string($_REQUEST['adminFee']), mysql_real_escape_string($_REQUEST['revShare']), mysql_real_escape_string($_REQUEST['ownerID']));
		$ownerQuery = $dbConn->sqlQuery($query, false);
		
		if ($ownerQuery) {
			$query = sprintf("SELECT * FROM ccreg_paths WHERE owner_id = %s;", mysql_real_escape_string($_REQUEST['ownerID']));
			$moneyQuery = $dbConn->sqlQuery($query, true, true);
			if ($moneyQuery) {
				$moneyPathArr = array();
				foreach ($moneyQuery as $row) {
					if (!array_key_exists($row['ccreg_id'], $moneyPathArr))
						$moneyPathArr[$row['ccreg_id']] = array(
							'adminFeePercent'  => mysql_real_escape_string($_REQUEST['adminFee']),
							'revSharePercent'  => mysql_real_escape_string($_REQUEST['revShare']),
							'overwriteCoregOn' => $row['overwrite_coreg_estimate_payout'],
							'overwriteType'    => $row['overwrite_type'],
							'overwriteAmount'  => $row['overwrite_payout'],
							'trafficCap'       => mysql_real_escape_string(serialize($capArr)),
							'coregPayoutEst'   => mysql_real_escape_string(serialize($estArr))
						);
				}
				
				foreach ($moneyPathArr as $ccregID => $moneyPathInfo) {
					$query = sprintf("INSERT INTO ccreg_path_money (ccreg_id, admin_fee_percent, rev_share_percent, overwrite_coreg_estimate, overwrite_type, overwrite_payout, traffic_cap, coreg_payout_estimate) VALUES (%s, '%s','%s', %s, '%s', '%s', '%s', '%s');", $ccregID, $moneyPathInfo['adminFeePercent'], $moneyPathInfo['revSharePercent'], $moneyPathInfo['overwriteCoregOn'], $moneyPathInfo['overwriteType'], $moneyPathInfo['overwriteAmount'], $moneyPathInfo['trafficCap'], $moneyPathInfo['coregPayoutEst']);
					$insertQuery = $dbConn->sqlQuery($query, false);
				}
			} else {
				$query = sprintf("SELECT ccreg_id FROM ccreg_paths WHERE owner_id = %s;", mysql_real_escape_string($_REQUEST['ownerID']));
				$pathQuery = $dbConn->sqlQuery($query, true, true);
				if ($pathQuery) {
					foreach ($pathQuery as $row) {
						$query = sprintf("INSERT INTO ccreg_path_money (ccreg_id, admin_fee_percent, rev_share_percent, overwrite_coreg_estimate, overwrite_type, overwrite_payout, traffic_cap, coreg_payout_estimate) VALUES (%s, '%s','%s', %s, '%s', '%s', '%s', '%s');", $row['ccreg_id'], 0, 100, 0, '', 0, mysql_real_escape_string(serialize($capArr)), mysql_real_escape_string(serialize($estArr)));
						$insertQuery = $dbConn->sqlQuery($query, false);
					}
				}
			}
			
			if (!empty($_REQUEST['redirect'])) {
				header('Location: '. $_REQUEST['redirect']);
				exit;
			} else
				echo "SUCCESS";
		} else echo 'ERROR: Did not update owner. '. mysql_error();
		break;
	
	case 'deleteOwner':
		if (!isset($_REQUEST['id'])) exit('ERROR: Missing parameters!');
		
		$query = sprintf("DELETE FROM ccreg_owners where owner_id=%s;", $_REQUEST['id']);
		$ownerQuery = $dbConn->sqlQuery($query, false);
		
		if ($ownerQuery) {
			header('Location: /admins/ccReg_owners.php');
			exit;
		} else echo 'ERROR: Did not delete owner. '. mysql_error();
		break;
	
	case 'newPath':
		if (!isset($_REQUEST['ownerID']) || !isset($_REQUEST['pathName']) || !isset($_REQUEST['defaultRedirectURL']) || !isset($_REQUEST['pathActive']) || !isset($_REQUEST['overwriteCoregEst'])) exit('ERROR: Missing parameters!');
		
		$overwrite = ($_REQUEST['overwriteCoregEst'] == 'yes') ? 1 : 0;
		$overwriteType = mysql_real_escape_string($_REQUEST['overwriteType']);
		$overwritePayout = floatval($_REQUEST['overwritePayout']);
		
		$query = sprintf("INSERT INTO ccreg_paths (owner_id, path_name, active, default_redirect_url, overwrite_coreg_estimate_payout, overwrite_type, overwrite_payout) VALUES (%s, '%s', %s, '%s', %s, '%s', '%s');", mysql_real_escape_string($_REQUEST['ownerID']), mysql_real_escape_string($_REQUEST['pathName']), $_REQUEST['pathActive'], mysql_real_escape_string($_REQUEST['defaultRedirectURL']), $overwrite, $overwriteType, $overwritePayout);
		$pathQuery = $dbConn->sqlQuery($query, false);
		
		if ($pathQuery) {
			$query = sprintf("SELECT * FROM ccreg_owners WHERE owner_id = %s;", mysql_real_escape_string($_REQUEST['ownerID']));
			$ownerInfo = $dbConn->sqlQuery($query, true, false);
			
			$ccregID = mysql_insert_id();
			$query = sprintf("INSERT INTO ccreg_path_money (ccreg_id, admin_fee_percent, rev_share_percent, overwrite_coreg_estimate, overwrite_type, overwrite_payout, traffic_cap, coreg_payout_estimate) VALUES (%s, '%s','%s', %s, '%s', '%s', '%s', '%s');", $ccregID, $ownerInfo['admin_fee_percent'], $ownerInfo['rev_share_percent'], $overwrite, $overwriteType, $overwritePayout, $ownerInfo['traffic_cap'], $ownerInfo['coreg_payout_estimate']);
			$insertQuery = $dbConn->sqlQuery($query, false);
			
			if (!empty($_REQUEST['redirect'])) {
				header('Location: '. $_REQUEST['redirect']);
				exit;
			} else
				echo "SUCCESS";
		} else echo 'ERROR: Did not insert path. '. mysql_error();
		break;
	
	case 'updatePath':
		if (!isset($_REQUEST['ccregID']) || !isset($_REQUEST['ownerID']) || !isset($_REQUEST['pathName']) || !isset($_REQUEST['defaultRedirectURL']) || !isset($_REQUEST['pathActive']) || !isset($_REQUEST['overwriteCoregEst'])) exit('ERROR: Missing parameters!');
		
		$overwrite = ($_REQUEST['overwriteCoregEst'] == 'yes') ? 1 : 0;
		$overwriteType = mysql_real_escape_string($_REQUEST['overwriteType']);
		$overwritePayout = floatval($_REQUEST['overwritePayout']);
		
		$query = sprintf("UPDATE ccreg_paths SET owner_id=%s, path_name='%s', active=%s, default_redirect_url='%s', overwrite_coreg_estimate_payout=%s, overwrite_type='%s', overwrite_payout='%s' WHERE ccreg_id=%s;", mysql_real_escape_string($_REQUEST['ownerID']), mysql_real_escape_string($_REQUEST['pathName']), $_REQUEST['pathActive'], mysql_real_escape_string($_REQUEST['defaultRedirectURL']), $overwrite, $overwriteType, $overwritePayout, mysql_real_escape_string($_REQUEST['ccregID']));
		$pathQuery = $dbConn->sqlQuery($query, false);
		
		if ($pathQuery) {
			$query = sprintf("SELECT * FROM ccreg_owners WHERE owner_id = %s;", mysql_real_escape_string($_REQUEST['ownerID']));
			$ownerInfo = $dbConn->sqlQuery($query, true, false);
			
			$query = sprintf("INSERT INTO ccreg_path_money (ccreg_id, admin_fee_percent, rev_share_percent, overwrite_coreg_estimate, overwrite_type, overwrite_payout, traffic_cap, coreg_payout_estimate) VALUES (%s, '%s','%s', %s, '%s', '%s', '%s', '%s');", mysql_real_escape_string($_REQUEST['ccregID']), $ownerInfo['admin_fee_percent'], $ownerInfo['rev_share_percent'], $overwrite, $overwriteType, $overwritePayout, $ownerInfo['traffic_cap'], $ownerInfo['coreg_payout_estimate']);
			$insertQuery = $dbConn->sqlQuery($query, false);
			
			if (!empty($_REQUEST['redirect'])) {
				header('Location: '. $_REQUEST['redirect']);
				exit;
			} else
				echo "SUCCESS";
		} else echo 'ERROR: Did not update path. '. mysql_error();
		break;
	
	case 'deletePath':
		if (!isset($_REQUEST['id'])) exit('ERROR: Missing parameters!');
		
		$query = sprintf("DELETE FROM ccreg_paths where ccreg_id=%s;", $_REQUEST['id']);
		$pathQuery = $dbConn->sqlQuery($query, false);
		
		if ($pathQuery) {
			header('Location: /admins/ccReg_paths.php');
			exit;
		} else echo 'ERROR: Did not delete path. '. mysql_error();
		break;
	
	case 'newPage':
		if (!isset($_REQUEST['pageTypeID']) || !isset($_REQUEST['pageName']) || !isset($_REQUEST['fileName']) || !isset($_REQUEST['pageTitle'])) exit('ERROR: Missing parameters!');
		
		$query = sprintf("INSERT INTO ccreg_pages (page_type_id, file_name, page_name, page_title) VALUES (%s, '%s', '%s', '%s');", $_REQUEST['pageTypeID'], mysql_real_escape_string($_REQUEST['fileName']), mysql_real_escape_string($_REQUEST['pageName']), mysql_real_escape_string($_REQUEST['pageTitle']));
		$pageQuery = $dbConn->sqlQuery($query, false);
		
		if ($pageQuery) {
			if (!empty($_REQUEST['redirect'])) {
				header('Location: '. $_REQUEST['redirect']);
				exit;
			} else
				echo "SUCCESS";
		} else echo 'ERROR: Did not insert page. '. mysql_error();
		break;
	
	case 'updatePage':
		if (!isset($_REQUEST['pageID']) || !isset($_REQUEST['pageTypeID']) || !isset($_REQUEST['pageName']) || !isset($_REQUEST['fileName']) || !isset($_REQUEST['pageTitle'])) exit('ERROR: Missing parameters!');
		
		$query = sprintf("UPDATE ccreg_pages SET page_type_id=%s, file_name='%s', page_name='%s', page_title='%s' WHERE page_id=%s;", $_REQUEST['pageTypeID'], mysql_real_escape_string($_REQUEST['fileName']), mysql_real_escape_string($_REQUEST['pageName']), mysql_real_escape_string($_REQUEST['pageTitle']), $_REQUEST['pageID']);
		$pageQuery = $dbConn->sqlQuery($query, false);
		
		if ($pageQuery) {
			if (!empty($_REQUEST['redirect'])) {
				header('Location: '. $_REQUEST['redirect']);
				exit;
			} else
				echo "SUCCESS";
		} else echo 'ERROR: Did not update page. '. mysql_error();
		break;
	
	case 'deletePage':
		if (!isset($_REQUEST['id'])) exit('ERROR: Missing parameters!');
		
		$query = sprintf("DELETE FROM ccreg_pages where page_id=%s;", $_REQUEST['id']);
		$pageQuery = $dbConn->sqlQuery($query, false);
		
		if ($pageQuery) {
			header('Location: /admins/ccReg_pages.php');
			exit;
		} else echo 'ERROR: Did not delete page. '. mysql_error();
		break;
	
	case 'newPageType':
		if (!isset($_REQUEST['typeName']) || !isset($_REQUEST['folderName'])) exit('ERROR: Missing parameters!');
		
		$query = sprintf("INSERT INTO ccreg_page_types (type_name, folder_name) VALUES ('%s', '%s');", mysql_real_escape_string($_REQUEST['typeName']), mysql_real_escape_string($_REQUEST['folderName']));
		$typeQuery = $dbConn->sqlQuery($query, false);
		
		if ($typeQuery) {
			if (!empty($_REQUEST['redirect'])) {
				header('Location: '. $_REQUEST['redirect']);
				exit;
			} else
				echo "SUCCESS";
		} else echo 'ERROR: Did not insert type. '. mysql_error();
		break;
	
	case 'updatePageType':
		if (!isset($_REQUEST['typeID']) || !isset($_REQUEST['typeName']) || !isset($_REQUEST['folderName'])) exit('ERROR: Missing parameters!');
		
		$query = sprintf("UPDATE ccreg_page_types SET type_name='%s', folder_name='%s' WHERE type_id=%s;", mysql_real_escape_string($_REQUEST['typeName']), mysql_real_escape_string($_REQUEST['folderName']), $_REQUEST['typeID']);
		$typeQuery = $dbConn->sqlQuery($query, false);
		
		if ($typeQuery) {
			if (!empty($_REQUEST['redirect'])) {
				header('Location: '. $_REQUEST['redirect']);
				exit;
			} else
				echo "SUCCESS";
		} else echo 'ERROR: Did not update type. '. mysql_error();
		break;
	
	case 'deletePageType':
		if (!isset($_REQUEST['id'])) exit('ERROR: Missing parameters!');
		
		$query = sprintf("DELETE FROM ccreg_page_types where type_id=%s;", $_REQUEST['id']);
		$typeQuery = $dbConn->sqlQuery($query, false);
		
		if ($typeQuery) {
			header('Location: /admins/ccReg_pageTypes.php');
			exit;
		} else echo 'ERROR: Did not delete type. '. mysql_error();
		break;
	
	case 'newTemplate':
		if (!isset($_REQUEST['templateName']) || !isset($_REQUEST['folderName'])) exit('ERROR: Missing parameters!');
		
		$query = sprintf("INSERT INTO ccreg_path_templates (template_name, template_folder) VALUES ('%s', '%s');", mysql_real_escape_string($_REQUEST['templateName']), mysql_real_escape_string($_REQUEST['folderName']));
		$typeQuery = $dbConn->sqlQuery($query, false);
		
		if ($typeQuery) {
			if (!empty($_REQUEST['redirect'])) {
				header('Location: '. $_REQUEST['redirect']);
				exit;
			} else
				echo "SUCCESS";
		} else echo 'ERROR: Did not insert template. '. mysql_error();
		break;
	
	case 'updateTemplate':
		if (!isset($_REQUEST['templateID']) || !isset($_REQUEST['templateName']) || !isset($_REQUEST['folderName'])) exit('ERROR: Missing parameters!');
		
		$query = sprintf("UPDATE ccreg_path_templates SET template_name='%s', template_folder='%s' WHERE template_id=%s;", mysql_real_escape_string($_REQUEST['templateName']), mysql_real_escape_string($_REQUEST['folderName']), $_REQUEST['templateID']);
		$typeQuery = $dbConn->sqlQuery($query, false);
		
		if ($typeQuery) {
			if (!empty($_REQUEST['redirect'])) {
				header('Location: '. $_REQUEST['redirect']);
				exit;
			} else
				echo "SUCCESS";
		} else echo 'ERROR: Did not update template. '. mysql_error();
		break;
	
	case 'deleteTemplate':
		if (!isset($_REQUEST['id'])) exit('ERROR: Missing parameters!');
		
		$query = sprintf("DELETE FROM ccreg_path_templates where template_id=%s;", $_REQUEST['id']);
		$typeQuery = $dbConn->sqlQuery($query, false);
		
		if ($typeQuery) {
			header('Location: /admins/ccReg_templates.php');
			exit;
		} else echo 'ERROR: Did not delete template. '. mysql_error();
		break;
	
	case 'newLink':
		if (!isset($_REQUEST['linkName']) || !isset($_REQUEST['url']) || !isset($_REQUEST['tsid']) || !isset($_REQUEST['affiliate'])) exit('ERROR: Missing parameters!');
		
		$query = sprintf("INSERT INTO ccreg_links (link_name, link_url, af_key, tsid) VALUES ('%s', '%s', %s, %s);", mysql_real_escape_string($_REQUEST['linkName']), mysql_real_escape_string($_REQUEST['url']), $_REQUEST['affiliate'], $_REQUEST['tsid']);
		$pageQuery = $dbConn->sqlQuery($query, false);
		
		if ($pageQuery) {
			if (!empty($_REQUEST['redirect'])) {
				header('Location: '. $_REQUEST['redirect']);
				exit;
			} else
				echo "SUCCESS";
		} else echo 'ERROR: Did not insert link. '. mysql_error();
		break;
	
	case 'updateLink':
		if (!isset($_REQUEST['linkID']) || !isset($_REQUEST['linkName']) || !isset($_REQUEST['url']) || !isset($_REQUEST['tsid']) || !isset($_REQUEST['affiliate'])) exit('ERROR: Missing parameters!');
		
		$query = sprintf("UPDATE ccreg_links SET link_name='%s', link_url='%s', af_key=%s, tsid=%s WHERE link_id=%s;", mysql_real_escape_string($_REQUEST['linkName']), mysql_real_escape_string($_REQUEST['url']), $_REQUEST['affiliate'], $_REQUEST['tsid'], $_REQUEST['linkID']);
		$pageQuery = $dbConn->sqlQuery($query, false);
		
		if ($pageQuery) {
			if (!empty($_REQUEST['redirect'])) {
				header('Location: '. $_REQUEST['redirect']);
				exit;
			} else
				echo "SUCCESS";
		} else echo 'ERROR: Did not update link. '. mysql_error();
		break;
	
	case 'deleteLink':
		if (!isset($_REQUEST['id'])) exit('ERROR: Missing parameters!');
		
		$query = sprintf("DELETE FROM ccreg_links where link_id=%s;", $_REQUEST['id']);
		$pageQuery = $dbConn->sqlQuery($query, false);
		
		if ($pageQuery) {
			header('Location: /admins/ccReg_links.php');
			exit;
		} else echo 'ERROR: Did not delete link. '. mysql_error();
		break;
	
	case 'pixelPageView':
		if (!isset($_REQUEST['ccregID']) || !isset($_REQUEST['pageID']) || !isset($_REQUEST['templateID']) || !isset($_REQUEST['position']) || !isset($_REQUEST['ip']) || !isset($_REQUEST['sessionID']) || !isset($_REQUEST['tsid']) || !isset($_REQUEST['ofid']) || !isset($_REQUEST['opid']) || !isset($_REQUEST['opid2'])) exit('ERROR: Missing parameters!');
		
		$referer = (isset($_REQUEST['ref'])) ? rawurldecode($_REQUEST['ref']) : '';
		
		$query = sprintf("INSERT INTO ccreg_pageviews (ccreg_id, page_id, template_id, position, ip, session_id, tsid, ofid, opid, opid2, url_referer) VALUES (%s, %s, %s, %s, '%s', '%s', '%s', '%s', '%s', '%s', '%s');", $_REQUEST['ccregID'], $_REQUEST['pageID'], $_REQUEST['templateID'], $_REQUEST['position'], $_REQUEST['ip'], $_REQUEST['sessionID'], $_REQUEST['tsid'], $_REQUEST['ofid'], mysql_real_escape_string($_REQUEST['opid']), mysql_real_escape_string($_REQUEST['opid2']), mysql_real_escape_string($referer));
		
		$pixelQuery = $dbConn->sqlQuery($query, false);
		if ($pixelQuery) echo 'SUCCESS';
		else echo 'ERROR: '. mysql_error();
		break;
	
	case 'pixelJoin':
		if (!isset($_REQUEST['ccregID']) || !isset($_REQUEST['pageID']) || !isset($_REQUEST['templateID']) || !isset($_REQUEST['position']) || !isset($_REQUEST['ip']) || !isset($_REQUEST['sessionID']) || !isset($_REQUEST['tsid']) || !isset($_REQUEST['ofid']) || !isset($_REQUEST['opid']) || !isset($_REQUEST['opid2']) || !isset($_REQUEST['type'])) exit('ERROR: Missing parameters!');
		
		$referer = (isset($_REQUEST['ref'])) ? rawurldecode($_REQUEST['ref']) : '';
		
		$query = sprintf("INSERT INTO ccreg_joins (ccreg_id, page_id, template_id, offer_type, position, ip, session_id, tsid, ofid, opid, opid2, url_referer) VALUES (%s, %s, %s, %s, %s, '%s', '%s', '%s', '%s', '%s', '%s', '%s');", $_REQUEST['ccregID'], $_REQUEST['pageID'], $_REQUEST['templateID'], $_REQUEST['type'], $_REQUEST['position'], $_REQUEST['ip'], $_REQUEST['sessionID'], $_REQUEST['tsid'], $_REQUEST['ofid'], mysql_real_escape_string($_REQUEST['opid']), mysql_real_escape_string($_REQUEST['opid2']), mysql_real_escape_string($referer));
		$pixelQuery = $dbConn->sqlQuery($query, false);
		if ($pixelQuery) echo mysql_insert_id();
		else echo 'ERROR: '. mysql_error();
		break;
		
	case 'pixelPathClick':
		if (!isset($_REQUEST['clickParams'])) exit('ERROR: Missing parameters!');
		
		$params = json_decode(base64_decode($_REQUEST['clickParams']));
		
		$query = sprintf("INSERT INTO ccreg_path_clicks (ccreg_id, page_id, link_id, template_id, position, ip, session_id, tsid, ofid, opid, opid2) VALUES (%s, %s, %s, %s, %s, '%s', '%s', '%s', '%s', '%s', '%s');", $params->ccregID, $params->pageID, $params->linkID, $params->templateID, $params->position, $params->ip, $params->sessionID, $params->tsid, $params->ofid, mysql_real_escape_string($params->opid), mysql_real_escape_string($params->opid2));
		$pixelQuery = $dbConn->sqlQuery($query, false);
		if ($pixelQuery) echo 'SUCCESS';
		else echo 'ERROR: '. mysql_error();
		break;
		
	case 'initPath': // grabs a bunch of info from separate DB queries, puts the results into an array that chreg.com init() can accept, then spits the array out in serialized format.
		if (!isset($_REQUEST['ccregID']))  exit('ERROR: Missing parameters!');
		
		$ret = array();
		
		// check if path is active + grab traffic cap of owner
		$query = sprintf("SELECT p.active, o.traffic_cap, p.owner_id FROM ccreg_paths p LEFT JOIN ccreg_owners o ON p.owner_id = o.owner_id WHERE ccreg_id = %s;", $_REQUEST['ccregID']);
		$active = $dbConn->sqlQuery($query, true, false);
		$ret['active'] = $active['active'];
		$ret['trafficCap'] = base64_encode($active['traffic_cap']);
		$ownerID = $active['owner_id'];
		
		// get number of joins per offer type per owner
		$numJoins = array();
		$query = sprintf("SELECT count(j.id) AS num_joins, j.offer_type FROM ccreg_joins j LEFT JOIN ccreg_paths p ON j.ccreg_id = p.ccreg_id WHERE (p.owner_id = %s AND DATE(date_created) = DATE(NOW())) GROUP BY offer_type ORDER BY offer_type ASC;", $ownerID);
		$joinQuery = $dbConn->sqlQuery($query, true, true);
		foreach ($joinQuery as $row)
			$numJoins[$row['offer_type']] = $row['num_joins'];
		$ret['numJoins'] = base64_encode(serialize($numJoins));
		
		// get path order
		$query = sprintf("SELECT po.page_id, po.position, p.page_name, p.page_title, l.link_id, l.link_url, pt.folder_name, p.file_name, t.template_id, t.template_name, t.template_folder FROM ccreg_path_order po LEFT JOIN ccreg_pages p ON po.page_id = p.page_id LEFT JOIN ccreg_page_types pt ON pt.type_id = p.page_type_id LEFT JOIN ccreg_path_templates t ON po.template_id = t.template_id LEFT JOIN ccreg_links l ON po.link_id = l.link_id WHERE ccreg_id=%s ORDER BY position ASC;", $_REQUEST['ccregID']);
		$orderQuery = $dbConn->sqlQuery($query, true, true);
		
		$countPagesPerPosition = array();
		if ($orderQuery) {
			$orderArr = array();
			foreach ($orderQuery as $row) {
				$rand = 0; // determines whether this new page overwrites the original one for a given position
				if (empty($countPagesPerPosition[$row['position']])) $countPagesPerPosition[$row['position']] = 1;
				else $countPagesPerPosition[$row['position']]++;
				
				if ($countPagesPerPosition[$row['position']] > 1) $rand = rand(0, 1);
				if ($rand == 0)
					$orderArr[$row['position']] = array('pageID' => $row['page_id'], 'pageTitle' => $row['page_title'], 'folderName' => $row['folder_name'], 'fileName' => $row['file_name'], 'templateFolder' => $row['template_folder'], 'templateID' => $row['template_id'], 'linkURL' => $row['link_url'], 'linkID' => $row['link_id']);
			}
			$ret['pathOrder'] = base64_encode(serialize($orderArr));
		} else exit('ERROR: '. mysql_error());
		
		// get path redirect URL
		$query = sprintf("SELECT default_redirect_url FROM ccreg_paths WHERE ccreg_id=%s;", $_REQUEST['ccregID']);
		$urlQuery = $dbConn->sqlQuery($query, true, false);
		
		if ($urlQuery) {
			$ret['redirectURL'] = $urlQuery['default_redirect_url'];
		} else exit('ERROR: '. mysql_error());
		
		echo serialize($ret);
		break;
	
	case 'getPathActive':
		if (!isset($_REQUEST['ccregID']))  exit('ERROR: Missing parameters!');
		
		$query = sprintf("SELECT active FROM ccreg_paths WHERE ccreg_id=%s;", $_REQUEST['ccregID']);
		$active = $dbConn->sqlQuery($query, true, false);
		
		echo $active['active'];
		break;
	
	case 'getPathOrder':
		if (!isset($_REQUEST['ccregID']))  exit('ERROR: Missing parameters!');
		
		$query = sprintf("SELECT po.page_id, po.position, p.page_name, p.page_title, pt.folder_name, p.file_name, t.template_id, t.template_name, t.template_folder FROM ccreg_path_order po LEFT JOIN ccreg_pages p ON po.page_id = p.page_id LEFT JOIN ccreg_page_types pt ON pt.type_id = p.page_type_id LEFT JOIN ccreg_path_templates t ON po.template_id = t.template_id WHERE ccreg_id=%s ORDER BY position ASC;", $_REQUEST['ccregID']);
		$orderQuery = $dbConn->sqlQuery($query, true, true);
		
		$countPagesPerPosition = array();
		if ($orderQuery) {
			$orderArr = array();
			foreach ($orderQuery as $row) {
				$rand = 0; // determines whether this new page overwrites the original one for a given position
				if (empty($countPagesPerPosition[$row['position']])) $countPagesPerPosition[$row['position']] = 1;
				else $countPagesPerPosition[$row['position']]++;
				
				if ($countPagesPerPosition[$row['position']] > 1) $rand = rand(0, 1);
				if ($rand == 0)
					$orderArr[$row['position']] = array('pageID' => $row['page_id'], 'pageTitle' => $row['page_title'], 'folderName' => $row['folder_name'], 'fileName' => $row['file_name'], 'templateFolder' => $row['template_folder'], 'templateID' => $row['template_id']);
			}
			
			echo base64_encode(serialize($orderArr));
		} else echo 'ERROR: '. mysql_error();
		break;
	
	case 'getPathRedirectURL':
		if (!isset($_REQUEST['ccregID']))  exit('ERROR: Missing parameters!');
		
		$query = sprintf("SELECT default_redirect_url FROM ccreg_paths WHERE ccreg_id=%s;", $_REQUEST['ccregID']);
		$urlQuery = $dbConn->sqlQuery($query, true, false);
		
		if ($urlQuery) {
			echo $urlQuery['default_redirect_url'];
		} else echo 'ERROR: '. mysql_error();
		break;
	
	default:
		break;
}
?>