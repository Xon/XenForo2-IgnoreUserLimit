<?php

namespace SV\IgnoreUserLimit\XF\Entity;

class User extends XFCP_User
{
	public function canIgnoreUser(\XF\Entity\User $user, &$error = '')
	{
		$canIgnore = parent::canIgnoreUser($user, $error);

		if ($canIgnore) // We'll be changing it to can't, so if they already can't no point in doing pointless extra work.
		{
			if ($this->hasPermission('general', 'sv_userIgnoreDisabled'))
			{
				$canIgnore = false;
			}
			else
			{
				$ignoreLimit = $this->hasPermission('general', 'sv_userIgnoreLimit');

				$ignoring = $this->Profile->ignored;

				if ($ignoring)
				{
					$canIgnore = $ignoreLimit == -1 || count($ignoring) < $ignoreLimit;

					if (!$canIgnore)
					{
						$error = \XF::phrase('you_may_only_ignore_x_people', ['count' => $ignoreLimit]);
					}
				}
			}

			if (!$canIgnore && $this->isIgnoring($user->user_id))
			{
				$canIgnore = true;
			}
		}

		return $canIgnore;
	}

	public function isIgnoring($userId)
	{
		if ($this->hasPermission('general', 'sv_userIgnoreDisabled'))
		{
			return false;
		}

		return parent::isIgnoring($userId);
	}
}