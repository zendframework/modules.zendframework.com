<?php

namespace User\GitHub;

use Hybrid_User_Profile;
use User\Entity\User;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;

final class LoginListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $sharedEvents      = $events->getSharedManager();
        $this->listeners[] = $sharedEvents->attach(
            'ScnSocialAuth\Authentication\Adapter\HybridAuth',
            'registerViaProvider',
            [$this, 'onRegister']
        );
    }

    /**
     * @param EventInterface $event
     */
    public function onRegister(EventInterface $event)
    {
        if (!$this->isEventValid($event)) {
            return;
        }

        $localUser = $event->getParam('user');
        $userProfile = $event->getParam('userProfile');

        $this->updateLocalUser($localUser, $userProfile);
    }

    /**
     * @param EventInterface $event
     * @return bool
     */
    private function isEventValid(EventInterface $event)
    {
        if (!$event->getParam('provider')) {
            return false;
        }

        // apply only on GitHub login
        if ('github' !== $event->getParam('provider')) {
            return false;
        }

        // check if there is an User entity
        if (!$event->getParam('user') instanceof User) {
            return false;
        }

        // check if there is a Hybrid_User_Profile entity
        if (!$event->getParam('userProfile') instanceof Hybrid_User_Profile) {
            return false;
        }

        return true;
    }

    /**
     * @param User $user
     * @param Hybrid_User_Profile $profile
     * @return User
     */
    private function updateLocalUser(User $user, Hybrid_User_Profile $profile)
    {
        $user->setUsername($this->getUsernameFromProfileUrl($profile->profileURL));
        $user->setPhotoUrl($profile->photoURL);

        return $user;
    }

    /**
     * @param string $url GitHub profile URL
     * @return string
     */
    private function getUsernameFromProfileUrl($url)
    {
        return substr($url, (strrpos($url, '/') + 1));
    }
}
