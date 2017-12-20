<?php

namespace SV\IgnoreUserLimit;

use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Db\Schema\Alter;

class Setup extends AbstractSetup
{
	use StepRunnerInstallTrait;
	use StepRunnerUpgradeTrait;
	use StepRunnerUninstallTrait;

    public static $defaultGuestGroupId = 1;
    public static $defaultRegisteredGroupId = 2;
    public static $defaultAdminGroupId = 3;
    public static $defaultModeratorGroupId = 4;

	public function installStep1()
	{
        $this->db()->query("insert ignore into xf_permission_entry (user_group_id, user_id, permission_group_id, permission_id, permission_value, permission_value_int)
                select " . self::$defaultRegisteredGroupId . ", 0, 'general', 'sv_userIgnoreLimit', 'allow', '-1'
                from xf_permission_entry
            ");

        $this->app->jobManager()->enqueueUnique(
            'permissionRebuild',
            'XF:PermissionRebuild',
            [],
            false
        );
	}

    public function uninstallStep1()
    {
        $this->db()->query("delete from xf_permission_entry where permission_group_id = 'general' and permission_id = 'sv_userIgnoreLimit'");
        $this->db()->query("delete from xf_permission_entry where permission_group_id = 'general' and permission_id = 'sv_userIgnoreDisabled'");
        $this->app->jobManager()->enqueueUnique(
            'permissionRebuild',
            'XF:PermissionRebuild',
            [],
            false
        );
    }
}
