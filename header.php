<?php

use XoopsModules\Yogurt;

require __DIR__ . '/preloads/autoloader.php';

require dirname(dirname(__DIR__)) . '/mainfile.php';
require XOOPS_ROOT_PATH . '/header.php';

$moduleDirName = basename(__DIR__);

$helper = \XoopsModules\Yogurt\Helper::getInstance();

$modulePath = XOOPS_ROOT_PATH . '/modules/' . $moduleDirName;

$myts = \MyTextSanitizer::getInstance();

if (!isset($GLOBALS['xoTheme']) || !is_object($GLOBALS['xoTheme'])) {
    require $GLOBALS['xoops']->path('class/theme.php');
    $GLOBALS['xoTheme'] = new \xos_opal_Theme();
}

//Handlers
//$XXXHandler = xoops_getModuleHandler('XXX', $moduleDirName);

// Load language files
$helper->loadLanguage('main');
//$helper->loadLanguage('user');
xoops_loadLanguage('user');

if (!isset($GLOBALS['xoopsTpl']) || !($GLOBALS['xoopsTpl'] instanceof XoopsTpl)) {
    require $GLOBALS['xoops']->path('class/template.php');
    $xoopsTpl = new \XoopsTpl();
}

$albumFactory          = new Yogurt\ImageHandler($xoopsDB);
$visitorsFactory       = new Yogurt\VisitorsHandler($xoopsDB);
$videosFactory         = new Yogurt\VideoHandler($xoopsDB);
$friendpetitionFactory = new Yogurt\FriendpetitionHandler($xoopsDB);
$friendshipFactory     = new Yogurt\FriendshipHandler($xoopsDB);

$isOwner  = 0;
$isanonym = 1;
$isfriend = 0;

/**
 * If anonym and uid not set then redirect to admins profile
 * Else redirects to own profile
 */
if (empty($xoopsUser)) {
    $isanonym = 1;
    if (isset($_GET['uid'])) {
        $uid_owner = \Xmf\Request::getInt('uid', 0, 'GET');
    } else {
        $uid_owner = 1;
        $isOwner   = 0;
    }
} else {
    $isanonym = 0;
    if (isset($_GET['uid'])) {
        $uid_owner = \Xmf\Request::getInt('uid', 0, 'GET');
        $isOwner   = ($xoopsUser->getVar('uid') == $uid_owner) ? 1 : 0;
    } else {
        $uid_owner = (int)$xoopsUser->getVar('uid');
        $isOwner   = 1;
    }
}