<?php

namespace SV\IgnoreUserLimit\XF\Entity;

class User extends XFCP_User
{
    public function canIgnoreUser(\XF\Entity\User $user, &$error = '')
    {
        if ($this->hasPermission('general', 'sv_userIgnoreDisabled'))
        {
            return false;
        }

        $canIgnore = parent::canIgnoreUser($user, $error);

        if (!$canIgnore)
        {
            return false;
        }

        $ignoreLimit = $this->hasPermission('general', 'sv_userIgnoreLimit');
        $ignoring = $this->Profile->ignored;

        if (($ignoreLimit >= 0) || (count($ignoring) >= $ignoreLimit))
        {
            $error = \XF::phrase('you_may_only_ignore_x_people', ['count' => $ignoreLimit]);

            return false;
        }

        return true;
    }
}
