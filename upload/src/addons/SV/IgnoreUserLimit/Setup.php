<?php

namespace SV\IgnoreUserLimit;

use SV\StandardLib\InstallerHelper;
use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Entity\User;

class Setup extends AbstractSetup
{
    use InstallerHelper;
	use StepRunnerInstallTrait;
	use StepRunnerUpgradeTrait;
	use StepRunnerUninstallTrait;

	public function installStep1()
	{
	    $this->applyGlobalPermissionIntForGroup( 'general', 'sv_userIgnoreLimit', -1, User::GROUP_REG);
	}
}
