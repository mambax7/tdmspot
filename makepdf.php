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

require_once __DIR__ . '/../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/header.php';
//require_once __DIR__ . '/fpdf/fpdf.php';
require_once XOOPS_ROOT_PATH . '/class/libraries/vendor/tecnickcom/tcpdf/tcpdf.php';

global $xoopsDB, $xoopsConfig, $xoopsModuleConfig;

$permHelper = new \Xmf\Module\Helper\Permission();
$myts = MyTextSanitizer:: getInstance(); // MyTextSanitizer object

$option = !empty($_REQUEST['option']) ? $_REQUEST['option'] : 'default';

if (!isset($_REQUEST['itemid'])) {
    redirect_header('index.php', 2, _MD_TDMSPOT_NOPERM);
}

switch ($option) {

    case 'default':
    default:
        //load class
        $itemHandler = new tdmspot\ItemHandler(); //xoops_getModuleHandler('tdmspot_item', 'tdmspot');
        //perm
        $gpermHandler = xoops_getHandler('groupperm');

        if (is_object($xoopsUser)) {
            $groups = $xoopsUser->getGroups();
            $user_uid = $xoopsUser->getVar('uid');
            $user_name = $xoopsUser->getVar('name');
            $user_uname = $xoopsUser->getVar('uname');
            $user_email = $xoopsUser->getVar('email');
        } else {
            $groups = XOOPS_GROUP_ANONYMOUS;
            $user_uid = 0;
            $user_name = XOOPS_GROUP_ANONYMOUS;
            $user_uname = XOOPS_GROUP_ANONYMOUS;
            $user_email = XOOPS_GROUP_ANONYMOUS;
        }

        //si pas le droit d'exporter
        if (!$permHelper->checkPermission('spot_view', 16)) {
            redirect_header('index.php', 2, _MD_TDMPICTURE_NOPERM);
        }

        $file = $itemHandler->get($_REQUEST['itemid']);

        $newsletter_title = utf8_decode(Chars($file->getVar('title')));
        //text
        $body = str_replace('{X_BREAK}', '<br>', $file->getVar('text'));
        $body = str_replace('{X_NAME}', $user_name, $body);
        $body = str_replace('{X_UNAME}', $user_uname, $body);
        $body = str_replace('{X_UEMAIL}', $user_email, $body);
        $body = str_replace('{X_ADMINMAIL}', $xoopsConfig['adminmail'], $body);
        $body = str_replace('{X_SITENAME}', $xoopsConfig['sitename'], $body);
        $body = str_replace('{X_SITEURL}', XOOPS_URL, $body);

        //$newsletter_text = utf8_decode(Chars($body));
        $newsletter_indate = formatTimestamp($file->getVar('indate'), 'm');
        $color = '#CCCCCC';
//        $pdf = new FPDF();

    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, _CHARSET, false);
/*

        $pdf->AddPage();
        //titre
        $pdf->SetFont('Arial', 'B', 15);
        $w = $pdf->GetStringWidth($xoopsConfig['sitename']) + 6;
        $pdf->SetX((210 - $w) / 2);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Cell($w, 8, Chars($xoopsConfig['sitename']), 0, 1, 'C', true);
        $pdf->Ln(1);
        $pdf->SetFont('Arial', 'B', 10);
        $w = $pdf->GetStringWidth($xoopsConfig['slogan']) + 6;
        $pdf->SetX((210 - $w) / 2);
        $pdf->SetLineWidth(0.2);
        $pdf->Cell($w, 8, Chars($xoopsConfig['slogan']), 0, 1, 'C', true);
        $pdf->Ln(6);

        $pdf->SetFont('Arial', 'B', 15);
        $w = $pdf->GetStringWidth($newsletter_title) + 6;
        $pdf->SetX((210 - $w) / 2);
        $pdf->SetDrawColor(204, 204, 204);
        $pdf->SetFillColor($color['r'], $color['v'], $color['b']);
        $pdf->SetLineWidth(0.2);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w, 8, Chars($newsletter_title), 1, 1, 'C', true);
        $pdf->Ln(6);
        //Sauvegarde de l'ordonn�e

        // date
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->MultiCell(50, 8, $newsletter_indate, 1, 1, 'L', true);
        $pdf->Ln(6);

        //content
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetFillColor(239, 239, 239);
        $pdf->MultiCell(190, 10, $newsletter_text, 1, 1, 'C', true);
        $pdf->Ln(4);
        $pdf->SetFont('Arial', 'B', 10);
        $w = $pdf->GetStringWidth(XOOPS_URL) + 6;
        $pdf->Cell($w, 8, Chars(XOOPS_URL), 0, 0, 'C', true);
        $pdf->Output();

        break;
*/

$content = $body;

    $lg                    = [];
    $lg['a_meta_charset']  = _CHARSET;
    $lg['a_meta_language'] = _LANGCODE;
    $lg['w_page']          = 'page';
    // set some language-dependent strings (optional)
    $pdf->setLanguageArray($lg);
    
    if (!defined('_RTL')) {
        define('_RTL', false);
    }
    
    $pdf->setRTL(_RTL);
    //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor(PDF_AUTHOR);
    //set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->setHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->setFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

    //2.5.8
    //set auto page breaks
    $pdf->SetAutoPageBreak(true, 25);
    $pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
    $pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);
    $pdf->setFooterData($tc = [0, 64, 0], $lc = [0, 64, 128]);
    //initialize document
    $pdf->Open();
    $pdf->AddPage();
    $pdf->SetFont('dejavusans', '', 10);
    $pdf->writeHTML($content, true, 0);
    $pdf->Output();
}
//

/**
 * @param $text
 * @return mixed
 */
function Chars($text)
{
    $myts = \MyTextSanitizer::getInstance();

    return preg_replace(['/&#039;/i', '/&#233;/i', '/&#232;/i', '/&#224;/i', '/&quot;/i', '/<br \>/i', '/&agrave;/i', '/&#8364;/i'], ["'", '�', '�', '�', '"', "\n", '�', '�'], $text);
}
