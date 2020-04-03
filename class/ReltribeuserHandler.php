<?php

namespace XoopsModules\Yogurt;

// Reltribeuser.php,v 1
//  ---------------------------------------------------------------- //
// Author: Bruno Barthez                                               //
// ----------------------------------------------------------------- //

require_once XOOPS_ROOT_PATH . '/kernel/object.php';

// -------------------------------------------------------------------------
// ------------------Reltribeuser user handler class -------------------
// -------------------------------------------------------------------------

/**
 * yogurt_reltribeuserhandler class.
 * This class provides simple mecanisme for Reltribeuser object
 */
class ReltribeuserHandler extends \XoopsPersistableObjectHandler
{
    /**
     * @var Helper
     */
    public $helper;
    public $isAdmin;

    /**
     * Constructor
     * @param null|\XoopsDatabase              $db
     * @param null|\XoopsModules\Yogurt\Helper $helper
     */

    public function __construct(\XoopsDatabase $db = null, $helper = null)
    {
        /** @var \XoopsModules\Yogurt\Helper $this ->helper */
        if (null === $helper) {
            $this->helper = \XoopsModules\Yogurt\Helper::getInstance();
        } else {
            $this->helper = $helper;
        }
        $isAdmin = $this->helper->isUserAdmin();
        parent::__construct($db, 'yogurt_reltribeuser', Reltribeuser::class, 'rel_id', 'rel_id');
    }

    /**
     * create a new Tribes
     *
     * @param bool $isNew flag the new objects as "new"?
     * @return \XoopsObject Tribes
     */
    public function create($isNew = true)
    {
        {
            $obj = parent::create($isNew);
            //        if ($isNew) {
            //            $obj->setDefaultPermissions();
            //        }
            $obj->helper = $this->helper;

            return $obj;
        }
    }

    /**
     * retrieve a Reltribeuser
     *
     * @param int $id of the Reltribeuser
     * @return mixed reference to the {@link Reltribeuser} object, FALSE if failed
     */
    public function get($id = null, $fields = null)
    {
        $sql = 'SELECT * FROM ' . $this->db->prefix('yogurt_reltribeuser') . ' WHERE rel_id=' . $id;
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        $numrows = $this->db->getRowsNum($result);
        if (1 == $numrows) {
            $yogurt_reltribeuser = new Reltribeuser();
            $yogurt_reltribeuser->assignVars($this->db->fetchArray($result));

            return $yogurt_reltribeuser;
        }

        return false;
    }

    /**
     * insert a new Reltribeuser in the database
     *
     * @param \XoopsObject $yogurt_reltribeuser reference to the {@link Reltribeuser}
     *                                          object
     * @param bool         $force
     * @return bool FALSE if failed, TRUE if already present and unchanged or successful
     */
    public function insert(\XoopsObject $yogurt_reltribeuser, $force = false)
    {
        global $xoopsConfig;
        if (!$yogurt_reltribeuser instanceof Reltribeuser) {
            return false;
        }
        if (!$yogurt_reltribeuser->isDirty()) {
            return true;
        }
        if (!$yogurt_reltribeuser->cleanVars()) {
            return false;
        }
        foreach ($yogurt_reltribeuser->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        $now = 'date_add(now(), interval ' . $xoopsConfig['server_TZ'] . ' hour)';
        if ($yogurt_reltribeuser->isNew()) {
            // ajout/modification d'un Reltribeuser
            $yogurt_reltribeuser = new Reltribeuser();
            $format              = 'INSERT INTO %s (rel_id, rel_tribe_id, rel_user_uid)';
            $format              .= 'VALUES (%u, %u, %u)';
            $sql                 = sprintf($format, $this->db->prefix('yogurt_reltribeuser'), $rel_id, $rel_tribe_id, $rel_user_uid);
            $force               = true;
        } else {
            $format = 'UPDATE %s SET ';
            $format .= 'rel_id=%u, rel_tribe_id=%u, rel_user_uid=%u';
            $format .= ' WHERE rel_id = %u';
            $sql    = sprintf($format, $this->db->prefix('yogurt_reltribeuser'), $rel_id, $rel_tribe_id, $rel_user_uid, $rel_id);
        }
        if ($force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            return false;
        }
        if (empty($rel_id)) {
            $rel_id = $this->db->getInsertId();
        }
        $yogurt_reltribeuser->assignVar('rel_id', $rel_id);

        return true;
    }

    /**
     * delete a Reltribeuser from the database
     *
     * @param \XoopsObject $yogurt_reltribeuser reference to the Reltribeuser to delete
     * @param bool         $force
     * @return bool FALSE if failed.
     */
    public function delete(\XoopsObject $yogurt_reltribeuser, $force = false)
    {
        if (!$yogurt_reltribeuser instanceof Reltribeuser) {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE rel_id = %u', $this->db->prefix('yogurt_reltribeuser'), $yogurt_reltribeuser->getVar('rel_id'));
        if ($force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            return false;
        }

        return true;
    }

    /**
     * retrieve yogurt_reltribeusers from the database
     *
     * @param null|\CriteriaElement|\CriteriaCompo $criteria  {@link \CriteriaElement} conditions to be met
     * @param bool                                 $id_as_key use the UID as key for the array?
     * @return array array of {@link Reltribeuser} objects
     */
    public function &getObjects(\CriteriaElement $criteria = null, $id_as_key = false, $as_object = true)
    {
        $ret   = [];
        $limit = $start = 0;
        $sql   = 'SELECT * FROM ' . $this->db->prefix('yogurt_reltribeuser');
        if (isset($criteria) && $criteria instanceof \CriteriaElement) {
            $sql .= ' ' . $criteria->renderWhere();
            if ('' != $criteria->getSort()) {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $yogurt_reltribeuser = new Reltribeuser();
            $yogurt_reltribeuser->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] = &$yogurt_reltribeuser;
            } else {
                $ret[$myrow['rel_id']] = &$yogurt_reltribeuser;
            }
            unset($yogurt_reltribeuser);
        }

        return $ret;
    }

    /**
     * count yogurt_reltribeusers matching a condition
     *
     * @param null|\CriteriaElement|\CriteriaCompo $criteria {@link \CriteriaElement} to match
     * @return int count of yogurt_reltribeusers
     */
    public function getCount(\CriteriaElement $criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix('yogurt_reltribeuser');
        if (isset($criteria) && $criteria instanceof \CriteriaElement) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        $result = $this->db->query($sql);
        if (!$result) {
            return 0;
        }
        list($count) = $this->db->fetchRow($result);

        return $count;
    }

    /**
     * delete yogurt_reltribeusers matching a set of conditions
     *
     * @param null|\CriteriaElement|\CriteriaCompo $criteria {@link \CriteriaElement}
     * @return bool FALSE if deletion failed
     */
    public function deleteAll(\CriteriaElement $criteria = null, $force = true, $asObject = false)
    {
        $sql = 'DELETE FROM ' . $this->db->prefix('yogurt_reltribeuser');
        if (isset($criteria) && $criteria instanceof \CriteriaElement) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }

        return true;
    }

    /**
     * @param      $nbtribes
     * @param null $criteria
     * @param int  $shuffle
     * @return array
     */
    public function getTribes($nbtribes, $criteria = null, $shuffle = 1)
    {
        $ret = [];

        $sql = 'SELECT rel_id, rel_tribe_id, rel_user_uid, tribe_title, tribe_desc, tribe_img, owner_uid FROM ' . $this->db->prefix('yogurt_tribes') . ', ' . $this->db->prefix('yogurt_reltribeuser');
        if (isset($criteria) && $criteria instanceof \CriteriaElement) {
            $sql .= ' ' . $criteria->renderWhere();
            //attention here this is kind of a hack
            $sql .= ' AND tribe_id = rel_tribe_id ';
            if ('' != $criteria->getSort()) {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();

            $result = $this->db->query($sql, $limit, $start);
            $vetor  = [];
            $i      = 0;

            while (false !== ($myrow = $this->db->fetchArray($result))) {
                $vetor[$i]['title']    = $myrow['tribe_title'];
                $vetor[$i]['desc']     = $myrow['tribe_desc'];
                $vetor[$i]['img']      = $myrow['tribe_img'];
                $vetor[$i]['id']       = $myrow['rel_id'];
                $vetor[$i]['uid']      = $myrow['owner_uid'];
                $vetor[$i]['tribe_id'] = $myrow['rel_tribe_id'];

                $i++;
            }

            if (1 == $shuffle) {
                shuffle($vetor);
                $vetor = array_slice($vetor, 0, $nbtribes);
            }

            return $vetor;
        }
    }

    /**
     * @param     $tribeId
     * @param     $start
     * @param     $nbUsers
     * @param int $isShuffle
     * @return array
     */
    public function getUsersFromTribe($tribeId, $start, $nbUsers, $isShuffle = 0)
    {
        $ret = [];

        $sql = 'SELECT rel_tribe_id, rel_user_uid, owner_uid, uname, user_avatar, uid FROM ' . $this->db->prefix('users') . ', ' . $this->db->prefix('yogurt_tribes') . ', ' . $this->db->prefix('yogurt_reltribeuser');
        $sql .= ' WHERE rel_user_uid = uid AND rel_tribe_id = tribe_id AND tribe_id =' . $tribeId . ' GROUP BY rel_user_uid ';

        $result = $this->db->query($sql, $nbUsers, $start);
        $ret    = [];
        $i      = 0;

        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $ret[$i]['uid']     = $myrow['uid'];
            $ret[$i]['uname']   = $myrow['uname'];
            $ret[$i]['avatar']  = $myrow['user_avatar'];
            $isOwner            = ($myrow['rel_user_uid'] == $myrow['owner_uid']) ? 1 : 0;
            $ret[$i]['isOwner'] = $isOwner;
            $i++;
        }

        if (1 == $isShuffle) {
            shuffle($ret);
            $ret = array_slice($ret, 0, $nbUsers);
        }

        return $ret;
    }
}