<?php

namespace SV\IgnoreUserLimit\Job;

use XF\Entity\User;
use XF\Job\AbstractRebuildJob;

class UnignoreUser extends AbstractRebuildJob
{
    /** @var null \XF\Entity\User */
    protected $ignoredUser = null;

	protected function getNextIds($start, $batch)
	{
		if (empty($this->data['user_id']))
        {
            return null;
        }

		if ($this->ignoredUser === null)
        {
            /** @var  $user */
            $this->ignoredUser = $this->app->em()->find('XF:User', $this->data['user_id'], ['Profile']);
            if (!$this->ignoredUser === null)
            {
                return null;
            }
        }

        $db = $this->app->db();

		return $db->fetchAllColumn($db->limit(
			"
				SELECT user_id
				FROM xf_user_ignored
				WHERE user_id > ? and ignored_user_id = ?
				ORDER BY user_id
			", $batch
		), [$start, $this->data['user_id']]);
	}

	protected function rebuildById($id)
	{
		/** @var \XF\Entity\User $user */
		$user = $this->app->em()->find('XF:User', $id, ['Profile']);
		if (!$user)
		{
			return;
		}

		\XF::asVisitor($user, function() {
            $ignoreService = $this->setupIgnoreService($this->ignoredUser);
            $ignoreService->unignore();
        });
	}

    /**
     * @param User $ignoreUser
     * @return \XF\Service\AbstractService|\XF\Service\User\Ignore
     */
    protected function setupIgnoreService(\XF\Entity\User $ignoreUser)
    {
        return \XF::service('XF:User\Ignore', $ignoreUser);
    }

	protected function getStatusType()
	{
		return \XF::phrase('users');
	}
}