<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\SecurityService;
use App\Form\RegistrationFormType;
use App\Service\SerializerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

#[Route('/api', name: 'api_register')]
class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request,SerializerService $serializerService, SecurityService $securityService)
    {
        $normalizer = new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter());
        
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $data = json_decode($request->getContent(), true);
        $user = $normalizer->denormalize($data, User::class);
        $form->submit($data, false);
        
        if ($form->isSubmitted()) {
            try {
                //enregistrement d'un nouveau utilisateur

                if (count($user->getRoles()) == 0) {
                    return new JsonResponse(["error" => "Ajouter le role SVP!!"], Response::HTTP_INTERNAL_SERVER_ERROR);
                }

                $user = $securityService->register($user);
                
            } catch (\Exception $e) {
                return new JsonResponse(["error" => $e->getMessage(), "Description" => "Erreur sur la creation d'utilisateur"], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return new JsonResponse($serializerService->serialize($user, "user:read"), Response::HTTP_CREATED);
        }
        
        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}