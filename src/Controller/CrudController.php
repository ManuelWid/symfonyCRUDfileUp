<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Rooms;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Form\CrudForm;
use App\Service\FileUploader;

class CrudController extends AbstractController
/**
 * @Route("/crud")
 */
{
    /**
     * @Route("/create", name="app_create")
     */
    public function createAction(ManagerRegistry $doctrine, Request $request, FileUploader $fileUploader): Response
    {
        $room = new Rooms();
        $form = $this->createForm(CrudForm::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFile = $form->get('picture')->getData();
            if ($pictureFile) {
                $pictureFileName = $fileUploader->upload($pictureFile);
                $room->setPicture($pictureFileName);
            }
            $room = $form->getData();
            $em = $doctrine->getManager();
            $em->persist($room);
            $em->flush();

            $this->addFlash('notice', 'Room added');
        
            return $this->redirectToRoute('app_home');
        }

        return $this->render('crud/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/edit/{id}", name="app_edit")
     */
    public function editAction(Request $request, ManagerRegistry $doctrine,FileUploader $fileUploader, $id): Response
    {
        $room = $doctrine->getRepository(Rooms::class)->find($id);
        $form = $this->createForm(CrudForm::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFile = $form->get('picture')->getData();
            if ($pictureFile) {
                $pictureFileName = $fileUploader->upload($pictureFile);
                $room->setPicture($pictureFileName);
            }
            $room = $form->getData();
            $em = $doctrine->getManager();
            $em->persist($room);
            $em->flush();

            $this->addFlash('notice', 'Room updated');
        
            return $this->redirectToRoute('app_home');
        }

        return $this->render('crud/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/details/{id}", name="app_details")
     */
    public function detailsAction(ManagerRegistry $doctrine, $id): Response
    {
        $room = $doctrine->getRepository(Rooms::class)->find($id);
        return $this->render("crud/details.html.twig", ['room' => $room]);
    }

    /**
     * @Route("/delete/{id}", name="app_delete")
     */
    public function deleteAction(ManagerRegistry $doctrine, $id): Response
    {
        $room = $doctrine->getRepository(Rooms::class)->find($id);
        $em = $doctrine->getManager();
        $em->remove($room);
        $em->flush();

        $this->addFlash('notice', 'Room removed');
        return $this->redirectToRoute('app_home');
    }

    // /**
    //  * @Route("/book/{id}", name="app_book")
    //  */
    // public function bookAction(Request $request, ManagerRegistry $doctrine,FileUploader $fileUploader, $id): Response
    // {
    //     $room = $doctrine->getRepository(Rooms::class)->find($id);

    //     $stat = $room->getFkStatus();
    //         // dd($stat);
    //         $stat->id=2;
    //         dd($stat);
    //         $room->setFkStatus($stat);
    //         $em = $doctrine->getManager();
    //         $em->persist($room);
    //         $em->flush();
        
    //         return $this->redirectToRoute('app_home');

    //     // return $this->render('crud/edit.html.twig', ['form' => $form->createView()]);
    // }
}