<?php

namespace SV\IgnoreUserLimit;

use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Db\Schema\Alter;
use XF\Entity\User;

class Setup extends AbstractSetup
{
	use StepRunnerInstallTrait;
	use StepRunnerUpgradeTrait;
	use StepRunnerUninstallTrait;

	public function installStep1()
	{
        $this->db()->query("insert ignore into xf_permission_entry (user_group_id, user_id, permission_group_id, permission_id, permission_value, permission_value_int)
                select " . User::GROUP_REG . ", 0, 'general', 'sv_userIgnoreLimit', 'allow', '-1'
                from xf_permission_entry
                limit 1
            ");

        $this->app->jobManager()->enqueueUnique(
            'permissionRebuild',
            'XF:PermissionRebuild',
            [],
            false
        );
	}
}
