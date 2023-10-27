<?php

namespace App\Service;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Mailer\MailerInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;

class SerializerService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer) {
        $this->mailer = $mailer;
    }

    // cette fonction permet de normalizer les données après insertion
    public function serialize(mixed $data, string $group)
    {
        $context = (new ObjectNormalizerContextBuilder())->withGroups($group)->toArray();

        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer([$normalizer]);
        return $serializer->normalize($data, null, $context);
    }
}