<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostFormType;
use App\Service\MailService;
use App\Service\PosteService;
use App\Service\SecurityService;
use App\Service\SerializerService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;

#[Route('/api', name: 'api_')]
class PostController extends AbstractController
{
    
    #[Route('/posts', name: 'post_index', methods: ['get'])]
    public function index(PosteService $service, SerializerService $serializerService): JsonResponse
    {
        $posts = $service->getAllPosts();
        
        return new JsonResponse($serializerService->serialize($posts, "post:read"));
    }
    

    #[Route('/posts/{id}', name: 'post_show', methods: ['get'])] 
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function show(PosteService $service, SerializerService $serializerService, int $id): JsonResponse
    {
        $post = $service->getPost($id);

        return new JsonResponse($serializerService->serialize($post, "post:read"));
    }



    #[Route('/posts', name: 'post_create', methods: ['post'])]
    public function create(Request $request, PosteService $service, MailService $serviceMailService, Security $security, SerializerService $serializerService)
    {
        $normalizer = new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter());
        
        $post = new Post();

        $form = $this->createForm(PostFormType::class, $post);
        $form->handleRequest($request);
        $data = json_decode($request->getContent(), true);
        $poste = $normalizer->denormalize($data, Post::class);
        
        $form->submit($data, false);
        
        if ($form->isSubmitted() && $form->isValid()){
            
            $poste = $service->save($form->getData());
            
            if ($poste) {
                try {
                    // Envoie de mail après une insertion reussie
                    $serviceMailService->sendMail($security->getToken()->getUser());
                    
                } catch (TransportExceptionInterface $e) {
                    return new JsonResponse(["error" => $e->getMessage(), "description" => "Poste crée mais le mail n'a pas été envoyé "], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }
            return new JsonResponse($serializerService->serialize($poste, "post:read"), Response::HTTP_CREATED);
        }
        
        return $this->render('post/index.html.twig', [
            'form' => $form->createView(),
        ]);
        
    }
}