<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * @copyright    XOOPS Project https://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author       Marcello Brandão aka  Suico
 * @author       XOOPS Development Team
 * @since
 */

use XoopsModules\Yogurt;

$GLOBALS['xoopsOption']['template_main'] = 'yogurt_album.tpl';
require __DIR__ . '/header.php';

$controller = new Yogurt\ControllerPhotos($xoopsDB, $xoopsUser);

/**
 * Fecthing numbers of tribes friends videos pictures etc...
 */
$nbSections = $controller->getNumbersSections();

/**
 * This variable define the beggining of the navigation must b
 * setted here so all calls to database will take this into account
 */
$start = isset($_GET['start']) ? (int)$_GET['start'] : 0;

/**
 * Filter for search pictures in database
 */
if (1 == $controller->isOwner) {
    $criteria_uid = new \Criteria('uid_owner', $controller->uidOwner);
} else {
    $criteria_private = new \Criteria('private', 0);
    $criteria_uid2    = new \Criteria('uid_owner', (int)$controller->uidOwner);
    $criteria_uid     = new \CriteriaCompo($criteria_uid2);
    $criteria_uid->add($criteria_private);
}
$criteria_uid->setLimit($xoopsModuleConfig['picturesperpage']);
$criteria_uid->setStart($start);
if (1 == $xoopsModuleConfig['images_order']) {
    $criteria_uid->setOrder('DESC');
    $criteria_uid->setSort('cod_img');
}
/**
 * Fetch pictures from factory
 */
$pictures_object_array = $controller->albumFactory->getObjects($criteria_uid);
$criteria_uid->setLimit('');
$criteria_uid->setStart(0);

/**
 * If there is no pictures in the album show in template lang_nopicyet
 */
if (0 == $nbSections['nbPhotos']) {
    $nopicturesyet = _MD_YOGURT_NOTHINGYET;
    $xoopsTpl->assign('lang_nopicyet', $nopicturesyet);
} else {
    /**
     * Lets populate an array with the dati from the pictures
     */
    $i = 0;
    foreach ($pictures_object_array as $picture) {
        $pictures_array[$i]['url']     = $picture->getVar('url', 's');
        $pictures_array[$i]['desc']    = $picture->getVar('title', 's');
        $pictures_array[$i]['cod_img'] = $picture->getVar('cod_img', 's');
        $pictures_array[$i]['private'] = $picture->getVar('private', 's');
        $xoopsTpl->assign('pics_array', $pictures_array);
        $i++;
    }
}

/**
 * Show the form if it is the owner and he can still upload pictures
 */
if (!empty($xoopsUser)) {
    if ((1 == $controller->isOwner) && $xoopsModuleConfig['nb_pict'] > $nbSections['nbPhotos']) {
        $maxfilebytes = $xoopsModuleConfig['maxfilesize'];
        $xoopsTpl->assign('maxfilebytes', $maxfilebytes);
        $xoopsTpl->assign('showForm', '1');
    }
}

/**
 * Let's get the user name of the owner of the album
 */
$owner      = new \XoopsUser($controller->uidOwner);
$identifier = $owner->getVar('uname');
$avatar     = $owner->getVar('user_avatar');

/**
 * Adding to the module js and css of the lightbox and new ones
 */
$xoTheme->addStylesheet(XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/include/yogurt.css');
$xoTheme->addStylesheet(XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/css/jquery.tabs.css');
// what browser they use if IE then add corrective script.
if (preg_match('/msie/', strtolower($_SERVER['HTTP_USER_AGENT']))) {
    $xoTheme->addStylesheet(XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/css/jquery.tabs-ie.css');
}
//$xoTheme->addStylesheet(XOOPS_URL.'/modules/'.$xoopsModule->getVar('dirname').'/lightbox/css/lightbox.css');
//$xoTheme->addScript(XOOPS_URL.'/modules/'.$xoopsModule->getVar('dirname').'/lightbox/js/prototype.js');
//$xoTheme->addScript(XOOPS_URL.'/modules/'.$xoopsModule->getVar('dirname').'/lightbox/js/scriptaculous.js?load=effects');
//$xoTheme->addScript(XOOPS_URL.'/modules/'.$xoopsModule->getVar('dirname').'/lightbox/js/lightbox.js');
$xoTheme->addStylesheet(XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/include/jquery.lightbox-0.3.css');
$xoTheme->addScript(XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/include/jquery.js');
$xoTheme->addScript(XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/include/jquery.lightbox-0.3.js');
$xoTheme->addScript(XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/include/yogurt.js');

/**
 * Criando a barra de navegao caso tenha muitos amigos
 */
$barra_navegacao = new \XoopsPageNav($nbSections['nbPhotos'], $xoopsModuleConfig['picturesperpage'], $start, 'start', 'uid=' . (int)$controller->uidOwner);
$navegacao       = $barra_navegacao->renderImageNav(2);

/**
 * Assigning smarty variables
 */
//permissions
$xoopsTpl->assign('allow_Notes', $controller->checkPrivilegeBySection('Notes'));
$xoopsTpl->assign('allow_friends', $controller->checkPrivilegeBySection('friends'));
$xoopsTpl->assign('allow_tribes', $controller->checkPrivilegeBySection('tribes'));
$xoopsTpl->assign('allow_pictures', $controller->checkPrivilegeBySection('pictures'));
$xoopsTpl->assign('allow_videos', $controller->checkPrivilegeBySection('videos'));
$xoopsTpl->assign('allow_audios', $controller->checkPrivilegeBySection('audio'));

//Owner data
$xoopsTpl->assign('uid_owner', $controller->uidOwner);
$xoopsTpl->assign('owner_uname', $controller->nameOwner);
$xoopsTpl->assign('isOwner', $controller->isOwner);

//numbers
$xoopsTpl->assign('nb_tribes', $nbSections['nbTribes']);
$xoopsTpl->assign('nb_photos', $nbSections['nbPhotos']);
$xoopsTpl->assign('nb_videos', $nbSections['nbVideos']);
$xoopsTpl->assign('nb_Notes', $nbSections['nbNotes']);
$xoopsTpl->assign('nb_friends', $nbSections['nbFriends']);
$xoopsTpl->assign('nb_audio', $nbSections['nbAudio']);

//navbar
$xoopsTpl->assign('module_name', $xoopsModule->getVar('name'));
$xoopsTpl->assign('lang_mysection', _MD_YOGURT_MYPHOTOS);
$xoopsTpl->assign('section_name', _MD_YOGURT_PHOTOS);
$xoopsTpl->assign('lang_home', _MD_YOGURT_HOME);
$xoopsTpl->assign('lang_photos', _MD_YOGURT_PHOTOS);
$xoopsTpl->assign('lang_friends', _MD_YOGURT_FRIENDS);
$xoopsTpl->assign('lang_audio', _MD_YOGURT_AUDIOS);
$xoopsTpl->assign('lang_videos', _MD_YOGURT_VIDEOS);
$xoopsTpl->assign('lang_Notebook', _MD_YOGURT_NOTEBOOK);
$xoopsTpl->assign('lang_profile', _MD_YOGURT_PROFILE);
$xoopsTpl->assign('lang_tribes', _MD_YOGURT_TRIBES);
$xoopsTpl->assign('lang_configs', _MD_YOGURT_CONFIGSTITLE);

//page atributes
$xoopsTpl->assign('xoops_pagetitle', sprintf(_MD_YOGURT_PAGETITLE, $xoopsModule->getVar('name'), $controller->nameOwner));
$xoopsTpl->assign('isanonym', $controller->isAnonym);

//form
$xoopsTpl->assign('lang_formtitle', _MD_YOGURT_SUBMIT_PIC_TITLE);
$xoopsTpl->assign('lang_selectphoto', _MD_YOGURT_SELECT_PHOTO);
$xoopsTpl->assign('lang_caption', _MD_YOGURT_CAPTION);
$xoopsTpl->assign('lang_uploadpicture', _MD_YOGURT_UPLOADPICTURE);
$xoopsTpl->assign('lang_youcanupload', sprintf(_MD_YOGURT_YOUCANUPLOAD, $maxfilebytes / 1024));

//$xoopsTpl->assign('path_yogurt_uploads',$xoopsModuleConfig['link_path_upload']);
$xoopsTpl->assign('lang_max_nb_pict', sprintf(_MD_YOGURT_YOUCANHAVE, $xoopsModuleConfig['nb_pict']));
$xoopsTpl->assign('lang_delete', _MD_YOGURT_DELETE);
$xoopsTpl->assign('lang_editdesc', _MD_YOGURT_EDITDESC);
$xoopsTpl->assign('lang_nb_pict', sprintf(_MD_YOGURT_YOUHAVE, $nbSections['nbPhotos']));

$xoopsTpl->assign('token', $GLOBALS['xoopsSecurity']->getTokenHTML());
$xoopsTpl->assign('navegacao', $navegacao);
$xoopsTpl->assign('lang_avatarchange', _MD_YOGURT_AVATARCHANGE);
$xoopsTpl->assign('avatar_url', $avatar);

$xoopsTpl->assign('lang_setprivate', _MD_YOGURT_PRIVATIZE);
$xoopsTpl->assign('lang_unsetprivate', _MD_YOGURT_UNPRIVATIZE);

include XOOPS_ROOT_PATH . '/include/comment_view.php';

include __DIR__ . '/../../footer.php';
