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

        $ignoreLimit = (int)$this->hasPermission('general', 'sv_userIgnoreLimit');
        if ($ignoreLimit < 0)
        {
            return true;
        }

        $ignoring = $this->Profile->ignored;
        if (\count($ignoring) + 1 > $ignoreLimit)
        {
            $error = \XF::phrase('you_may_only_ignore_x_people', ['count' => $ignoreLimit]);

            return false;
        }

        return true;
    }

    protected function _postSave()
    {
        parent::_postSave();

        if ($this->isChanged('is_staff') && $this->is_staff)
        {
            // force unignore of staff
            $userId = $this->user_id;
            \XF::app()->jobManager()->enqueueUnique('svUnignoreUser.' . $userId, 'SV\IgnoreUserLimit:UnignoreUser', [
                'user_id' => $userId,
            ], false);
        }
    }
}
