<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    {@link https://xoops.org/ XOOPS Project}
 * @license      {@link http://www.gnu.org/licenses/gpl-2.0.html GNU GPL 2 or later}
 * @package       tdmspot
 * @since
 * @author       TDM   - TEAM DEV MODULE FOR XOOPS
 * @author       XOOPS Development Team
 */

use Xoopsmodules\tdmspot;

require_once __DIR__ . '/../../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/header.php';

if (!defined('XOOPS_ROOT_PATH')) {
    die('XOOPS root path not defined');
}

$permHelper = new \Xmf\Module\Helper\Permission();

$op = isset($_REQUEST['op']) ? $_REQUEST['op'] : 'list';

$itemHandler = new tdmspot\ItemHandler(); //xoops_getModuleHandler('tdmspot_item', 'tdmspot');
$voteHandler = new tdmspot\VoteHandler(); //xoops_getModuleHandler('tdmspot_vote', 'tdmspot');

$myts = \MyTextSanitizer::getInstance();

$moduleHandler = xoops_getHandler('module');
$xoopsModule = $moduleHandler->getByDirname('tdmspot');
$gpermHandler = xoops_getHandler('groupperm');

if (!isset($xoopsModuleConfig)) {
    $configHandler = xoops_getHandler('config');
    $xoopsModuleConfig = &$configHandler->getConfigsByCat(0, $xoopsModule->getVar('mid'));
}

//inclus les langues
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/language/' . $xoopsConfig['language'] . '/main.php';

//permission
if (is_object($xoopsUser)) {
    $groups = $xoopsUser->getGroups();
    $xd_uid = $xoopsUser->getVar('uid');
} else {
    $groups = XOOPS_GROUP_ANONYMOUS;
    $xd_uid = 0;
}

switch ($op) {

    case 'addvote':

        //interdit au non membre
//        if (empty($xoopsUser)) {
        if(!$permHelper->checkPermission('spot_view', _AM_TDMSPOT_PERM_32)) {
            echo _MD_TDMSPOT_QUERYNOREGISTER;
            exit();
        }

        //permission d'afficher
        if(!$permHelper->checkPermission('spot_view', 32)) {
            echo _MD_TDMSPOT_NOPERM;
            exit();
        }

        if ($_REQUEST['vote_id']) {
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('vote_file', $_REQUEST['vote_id']));
            $criteria->add(new Criteria('vote_ip', $_SERVER['REMOTE_ADDR']));
            $numvote = $voteHandler->getCount($criteria);

            if ($numvote > 0) {
                echo _MD_TDMSPOT_VOTENOOK;
                exit();
            } else {
                $obj = $voteHandler->create();
                $obj->setVar('vote_file', $_REQUEST['vote_id']);
                $obj->setVar('vote_ip', $_SERVER['REMOTE_ADDR']);
                $erreur = $voteHandler->insert($obj);

                $item = $itemHandler->get($_REQUEST['vote_id']);
                $count = $item->getVar('file_counts');
                $vote = $item->getVar('file_votes');
                ++$count;
                ++$vote;
                $item->setVar('counts', $count);
                $item->setVar('votes', $vote);
                $erreur .= $itemHandler->insert($item);
            }

            if ($erreur) {
                echo _MD_TDMSPOT_VOTEOK;
                exit();
            } else {
                echo _MD_TDMSPOT_BASEERROR;
                exit();
            }
        }
        break;

    case 'removevote':

        //interdit au non membre
        if (empty($xoopsUser)) {
            echo _MD_TDMSPOT_QUERYNOREGISTER;
            exit();
        }

        //permission d'afficher
        if(!$permHelper->checkPermission('spot_view', 32)) {
            echo _MD_TDMSPOT_NOPERM;
            exit();
        }

        if ($_REQUEST['vote_id']) {
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('vote_file', $_REQUEST['vote_id']));
            $criteria->add(new Criteria('vote_ip', $_SERVER['REMOTE_ADDR']));
            $numvote = $voteHandler->getCount($criteria);

            if ($numvote > 0) {
                echo _MD_TDMSPOT_VOTENOOK;
                exit();
            } else {
                $obj = $voteHandler->create();
                $obj->setVar('vote_file', $_REQUEST['vote_id']);
                $obj->setVar('vote_ip', $_SERVER['REMOTE_ADDR']);
                $erreur = $voteHandler->insert($obj);

                $item = $itemHandler->get($_REQUEST['vote_id']);
                $count = $item->getVar('file_counts');
                $vote = $item->getVar('file_votes');
                --$count;
                ++$vote;
                $item->setVar('counts', $count);
                $item->setVar('votes', $vote);
                $erreur .= $itemHandler->insert($item);
            }
            if ($erreur) {
                echo _MD_TDMSPOT_VOTEOK;
                exit();
            } else {
                echo _MD_TDMSPOT_BASEERROR;
                exit();
            }
        }

        break;

}
