<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Services\FileUploader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/post", name="post.")
 */
class PostController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(PostRepository $repo)
    {
        $posts = $repo->findAll();


        return $this->render('post/index.html.twig', [
            
            'posts' => $posts 
        ]);
    }


    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, FileUploader $fileUploader){
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid() ){
            $file = $request->files->get('post')['image'];
            if($file){
                
                $post->setImage($fileUploader->uploadFile($file));
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush($post);

            $this->addFlash('success', 'Post was added');
            return $this->redirectToRoute('post.index');
           

        }

        return $this->render('post/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/show/{id}", name="show")
     */
    public function show(Post $post){

        

        return $this->render('post/show.html.twig', [
            'post'  => $post
        ]);

    }
    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function remove(Post $post){
        $em= $this->getDoctrine()->getManager();

        $em->remove($post);
        $em->flush();

        $this->addFlash('success', 'Post was removed');
        return $this->redirectToRoute('post.index');
    }
}
