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
 * Module: Yogurt
 *
 * @category        Module
 * @package         yogurt
 * @author          XOOPS Development Team <https://xoops.org>
 * @copyright       {@link https://xoops.org/ XOOPS Project}
 * @license         GPL 2.0 or later
 * @link            https://xoops.org/
 * @since           1.0.0
 */

use Xmf\Request;

require __DIR__ . '/admin_header.php';
xoops_cp_header();
//It recovered the value of argument op in URL$
$op    = \Xmf\Request::getString('op', 'list');
$order = \Xmf\Request::getString('order', 'desc');
$sort  = \Xmf\Request::getString('sort', '');

$adminObject->displayNavigation(basename(__FILE__));
/** @var \Xmf\Module\Helper\Permission $permHelper */
$permHelper = new \Xmf\Module\Helper\Permission();
$uploadDir  = XOOPS_UPLOAD_PATH . '/yogurt/images/';
$uploadUrl  = XOOPS_UPLOAD_URL . '/yogurt/images/';

switch ($op) {
    case 'new':
        $adminObject->addItemButton(AM_YOGURT_IMAGES_LIST, 'images.php', 'list');
        $adminObject->displayButton('left');

        $imagesObject = $imagesHandler->create();
        $form         = $imagesObject->getForm();
        $form->display();
        break;

    case 'save':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('images.php', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        if (0 !== \Xmf\Request::getInt('cod_img', 0)) {
            $imagesObject = $imagesHandler->get(Request::getInt('cod_img', 0));
        } else {
            $imagesObject = $imagesHandler->create();
        }
        // Form save fields
        $imagesObject->setVar('title', Request::getVar('title', ''));
        $imagesObject->setVar('data_creation', $_REQUEST['data_creation']);
        $imagesObject->setVar('data_update', $_REQUEST['data_update']);
        $imagesObject->setVar('uid_owner', Request::getVar('uid_owner', ''));
        $imagesObject->setVar('url', Request::getVar('url', ''));
        $imagesObject->setVar('private', Request::getVar('private', ''));
        if ($imagesHandler->insert($imagesObject)) {
            redirect_header('images.php?op=list', 2, AM_YOGURT_FORMOK);
        }

        echo $imagesObject->getHtmlErrors();
        $form = $imagesObject->getForm();
        $form->display();
        break;

    case 'edit':
        $adminObject->addItemButton(AM_YOGURT_ADD_IMAGES, 'images.php?op=new', 'add');
        $adminObject->addItemButton(AM_YOGURT_IMAGES_LIST, 'images.php', 'list');
        $adminObject->displayButton('left');
        $imagesObject = $imagesHandler->get(Request::getString('cod_img', ''));
        $form         = $imagesObject->getForm();
        $form->display();
        break;

    case 'delete':
        $imagesObject = $imagesHandler->get(Request::getString('cod_img', ''));
        if (1 == \Xmf\Request::getInt('ok', 0)) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header('images.php', 3, implode(', ', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($imagesHandler->delete($imagesObject)) {
                redirect_header('images.php', 3, AM_YOGURT_FORMDELOK);
            } else {
                echo $imagesObject->getHtmlErrors();
            }
        } else {
            xoops_confirm(['ok' => 1, 'cod_img' => Request::getString('cod_img', ''), 'op' => 'delete',], Request::getUrl('REQUEST_URI', '', 'SERVER'), sprintf(AM_YOGURT_FORMSUREDEL, $imagesObject->getVar('title')));
        }
        break;

    case 'clone':

        $id_field = \Xmf\Request::getString('cod_img', '');

        if ($utility::cloneRecord('yogurt_images', 'cod_img', $id_field)) {
            redirect_header('images.php', 3, AM_YOGURT_CLONED_OK);
        } else {
            redirect_header('images.php', 3, AM_YOGURT_CLONED_FAILED);
        }

        break;
    case 'list':
    default:
        $adminObject->addItemButton(AM_YOGURT_ADD_IMAGES, 'images.php?op=new', 'add');
        $adminObject->displayButton('left');
        $start                 = \Xmf\Request::getInt('start', 0);
        $imagesPaginationLimit = $helper->getConfig('userpager');

        $criteria = new \CriteriaCompo();
        $criteria->setSort('cod_img ASC, title');
        $criteria->setOrder('ASC');
        $criteria->setLimit($imagesPaginationLimit);
        $criteria->setStart($start);
        $imagesTempRows  = $imagesHandler->getCount();
        $imagesTempArray = $imagesHandler->getAll($criteria);
        /*
        //
        //
                            <th class='center width5'>".AM_YOGURT_FORM_ACTION."</th>
        //                    </tr>";
        //            $class = "odd";
        */

        // Display Page Navigation
        if ($imagesTempRows > $imagesPaginationLimit) {
            xoops_load('XoopsPageNav');

            $pagenav = new \XoopsPageNav(
                $imagesTempRows, $imagesPaginationLimit, $start, 'start', 'op=list' . '&sort=' . $sort . '&order=' . $order . ''
            );
            $GLOBALS['xoopsTpl']->assign('pagenav', null === $pagenav ? $pagenav->renderNav() : '');
        }

        $GLOBALS['xoopsTpl']->assign('imagesRows', $imagesTempRows);
        $imagesArray = [];

        //    $fields = explode('|', cod_img:int:11::NOT NULL::primary:cod_img|title:varchar:255::NOT NULL:::title|data_creation:date:0::NOT NULL:::data_creation|data_update:date:0::NOT NULL:::data_update|uid_owner:varchar:50::NOT NULL:::uid_owner|url:text:0::NOT NULL:::url|private:varchar:1::NOT NULL:::private);
        //    $fieldsCount    = count($fields);

        $criteria = new \CriteriaCompo();

        //$criteria->setOrder('DESC');
        $criteria->setSort($sort);
        $criteria->setOrder($order);
        $criteria->setLimit($imagesPaginationLimit);
        $criteria->setStart($start);

        $imagesCount     = $imagesHandler->getCount($criteria);
        $imagesTempArray = $imagesHandler->getAll($criteria);

        //    for ($i = 0; $i < $fieldsCount; ++$i) {
        if ($imagesCount > 0) {
            foreach (array_keys($imagesTempArray) as $i) {
                //        $field = explode(':', $fields[$i]);

                $GLOBALS['xoopsTpl']->assign('selectorcod_img', AM_YOGURT_IMAGES_COD_IMG);
                $imagesArray['cod_img'] = $imagesTempArray[$i]->getVar('cod_img');

                $GLOBALS['xoopsTpl']->assign('selectortitle', AM_YOGURT_IMAGES_TITLE);
                $imagesArray['title'] = $imagesTempArray[$i]->getVar('title');

                $GLOBALS['xoopsTpl']->assign('selectordata_creation', AM_YOGURT_IMAGES_DATA_CREATION);
                $imagesArray['data_creation'] = date(_SHORTDATESTRING, strtotime((string)$imagesTempArray[$i]->getVar('data_creation')));

                $GLOBALS['xoopsTpl']->assign('selectordata_update', AM_YOGURT_IMAGES_DATA_UPDATE);
                $imagesArray['data_update'] = date(_SHORTDATESTRING, strtotime((string)$imagesTempArray[$i]->getVar('data_update')));

                $GLOBALS['xoopsTpl']->assign('selectoruid_owner', AM_YOGURT_IMAGES_UID_OWNER);
                $imagesArray['uid_owner'] = strip_tags(\XoopsUser::getUnameFromId($imagesTempArray[$i]->getVar('uid_owner')));

                $GLOBALS['xoopsTpl']->assign('selectorurl', AM_YOGURT_IMAGES_URL);
                $imagesArray['url'] = strip_tags($imagesTempArray[$i]->getVar('url'));

                $GLOBALS['xoopsTpl']->assign('selectorprivate', AM_YOGURT_IMAGES_PRIVATE);
                $imagesArray['private']     = $imagesTempArray[$i]->getVar('private');
                $imagesArray['edit_delete'] = "<a href='images.php?op=edit&cod_img=" . $i . "'><img src=" . $pathIcon16 . "/edit.png alt='" . _EDIT . "' title='" . _EDIT . "'></a>
               <a href='images.php?op=delete&cod_img=" . $i . "'><img src=" . $pathIcon16 . "/delete.png alt='" . _DELETE . "' title='" . _DELETE . "'></a>
               <a href='images.php?op=clone&cod_img=" . $i . "'><img src=" . $pathIcon16 . "/editcopy.png alt='" . _CLONE . "' title='" . _CLONE . "'></a>";

                $GLOBALS['xoopsTpl']->append_by_ref('imagesArrays', $imagesArray);
                unset($imagesArray);
            }
            unset($imagesTempArray);
            // Display Navigation
            if ($imagesCount > $imagesPaginationLimit) {
                xoops_load('XoopsPageNav');
                $pagenav = new \XoopsPageNav(
                    $imagesCount, $imagesPaginationLimit, $start, 'start', 'op=list' . '&sort=' . $sort . '&order=' . $order . ''
                );
                $GLOBALS['xoopsTpl']->assign('pagenav', $pagenav->renderNav(4));
            }

            //                     echo "<td class='center width5'>

            //                    <a href='images.php?op=edit&cod_img=".$i."'><img src=".$pathIcon16."/edit.png alt='"._EDIT."' title='"._EDIT."'></a>
            //                    <a href='images.php?op=delete&cod_img=".$i."'><img src=".$pathIcon16."/delete.png alt='"._DELETE."' title='"._DELETE."'></a>
            //                    </td>";

            //                echo "</tr>";

            //            }

            //            echo "</table><br><br>";

            //        } else {

            //            echo "<table width='100%' cellspacing='1' class='outer'>

            //                    <tr>

            //                     <th class='center width5'>".AM_YOGURT_FORM_ACTION."XXX</th>
            //                    </tr><tr><td class='errorMsg' colspan='8'>There are noXXX images</td></tr>";
            //            echo "</table><br><br>";

            //-------------------------------------------

            echo $GLOBALS['xoopsTpl']->fetch(
                XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar('dirname') . '/templates/admin/yogurt_admin_images.tpl'
            );
        }

        break;
}
require __DIR__ . '/admin_footer.php';