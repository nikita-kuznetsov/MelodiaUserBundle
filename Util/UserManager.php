<?php

namespace Melodia\UserBundle\Util;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Melodia\UserBundle\Entity\User;

class UserManager
{
    protected $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    public function getEncoder(User $user)
    {
        return $this->encoderFactory->getEncoder($user);
    }

    public function updateUser(User $user)
    {
        $plainPassword = $user->getPlainPassword();

        if (!empty($plainPassword)) {
            $encoder = $this->getEncoder($user);
            $user->setPassword($encoder->encodePassword($plainPassword, $user->getSalt()));
            $user->eraseCredentials();
        }
    }

    public function preUpdate(PreUpdateEventArgs $event)
    {
        $user = $event->getEntity();

        if (!$user instanceof User) {
            return;
        }

        $this->updateUser($user);
        if ($user->getPlainPassword()) {
            $event->setNewValue('password', $user->getPassword());
        }
    }

    public function prePersist(LifecycleEventArgs $event)
    {
        $user = $event->getEntity();

        if (!$user instanceof User) {
            return;
        }

        $this->updateUser($user);
    }
}
