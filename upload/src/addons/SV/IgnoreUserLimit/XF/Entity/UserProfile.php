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
        if (\XF::visitor()->hasPermission('general', 'sv_userIgnoreDisabled'))
        {
            return [];
        }

        return $this->ignored_;
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