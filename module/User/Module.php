<?php

namespace User;

use ZfcBase\Module\AbstractModule;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\ApplicationInterface;

class Module extends AbstractModule
{
    public function bootstrap(ModuleManager $moduleManager, ApplicationInterface $app)
    {
        $em = $app->getEventManager()->getSharedManager();

        $em->attach('ScnSocialAuth\Authentication\Adapter\HybridAuth','githubToLocalUser', function($e) {
            $localUser = $e->getTarget();
            $userProfile = $e->getParam('userProfile');
            $localUser->setPhotoUrl($userProfile->photoURL);
        });
    }

    public function getDir()
    {
        return __DIR__;
    }

    public function getNamespace()
    {
        return __NAMESPACE__;
    }
}