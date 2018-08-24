<?php

namespace SV\IgnoreUserLimit\XF\Entity;

use XF\Mvc\Entity\Structure;

/**
 * @property array ignored_
 */
class UserProfile extends XFCP_UserProfile
{
    /**
     * @return array
     */
    protected function getIgnored()
    {
        $ignored = $this->ignored_;
        if (!is_array($ignored) ||
            \XF::visitor()->hasPermission('general', 'sv_userIgnoreDisabled'))
        {
            return [];
        }

        return $ignored;
    }

    /**
     * @param Structure $structure
     * @return Structure
     */
    public static function getStructure(Structure $structure)
    {
        $structure = parent::getStructure($structure);

        $structure->getters['ignored'] = true;

        return $structure;
    }
}