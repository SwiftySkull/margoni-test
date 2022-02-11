<?php

namespace App\Controller\Admin;

use App\Entity\Frame;
use App\Form\FrameType;
use App\Service\PagesNavigator;
use App\Repository\FrameRepository;
use App\Repository\PaintingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller which handles all the frames research,
 * and also handles the add/edit/delete of the framing
 * 
 * Controlleur qui se charge de toutes les recherches par encadrement
 * ainsi que les ajout/modification/suppression des encadrements
 * 
 * @Route("/frame", name="frame_")
 */
class FrameController extends AbstractController
{
    private $pagesNavigator;

    public function __construct(PagesNavigator $pagesNavigator)
    {
        $this->pagesNavigator = $pagesNavigator;
    }

    /**
     * Endpoint to show all the frames
     * Route pour montrer tous les encadrements
     * 
     * @Route(
     *      "/browse",
     *      name="browse",
     *      methods={"GET"},
     * )
     */
    public function browse(FrameRepository $frameRepository): Response
    {
        $frames = $frameRepository->findAllAsc();

        return $this->render('frame/browse.html.twig', [
            'frames' => $frames,
        ]);
    }

    /**
     * Endpoint to show all the paintings of one type of framing
     * Route pour montrer toutes les peintures d'un type d'encadrement
     * 
     * @Route(
     *      "/read/{id<\d+>}/page/{page<\d+>}",
     *      name="read",
     *      methods={"GET"},
     * )
     */
    public function read(Frame $frame = null, PaintingRepository $paintingRepository, $page)
    {
        if (null === $frame) {
            throw $this->createNotFoundException('Oups ! Type d\'encadrement non trouvé.');
        }
    
        $this->pagesNavigator->setAllEntries($paintingRepository->countByFrame($frame));

        $pageId = $this->pagesNavigator->getPageId($page);
        $slice = $this->pagesNavigator->getSlice($pageId);

        $paintings = $paintingRepository->findFrameLimited($frame, $slice);


        return $this->render('frame/read.html.twig', [
            'paintings' => $paintings,
            'frame' => $frame,
            'pages' => $this->pagesNavigator->getMinMax($pageId),
            'previousPage' => $this->pagesNavigator->getPreviousPage($pageId),
            'nextPage' => $this->pagesNavigator->getNextPage($pageId),
            'totalPages' => $this->pagesNavigator->getTotalPages(),
            'count' => $paintingRepository->countByFrame($frame),
        ]);
    }

    /**
     * Endpoint to update the name of a category
     * Route pour modifier le nom d'une catégorie
     * 
     * @Route(
     *      "/edit/{id<\d+>}",
     *      name="edit",
     *      methods={"GET", "POST"},
     * )
     */
    public function edit(Frame $frame = null, EntityManagerInterface $em, Request $request)
    {
        $submittedToken = $request->request->get('token');
        if (!$this->isCsrfTokenValid('add-edit-item', $submittedToken)) {
            throw $this->createAccessDeniedException('Action non autorisée !!!');
        }

        if (null === $frame) {
            throw $this->createNotFoundException('Oups ! Type d\'encadrement non trouvé.');
        }

        $form = $this->createForm(FrameType::class, $frame);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
    
            $em->flush();

            return $this->redirectToRoute('frame_browse');
        }

        return $this->render('frame/edit.html.twig', [
            'frame' => $frame,
            'form' => $form->createView(),
            'method' => 'Modification',
        ]);
    }

    /**
     * Endpoint to add a new framing type
     * Route pour ajouter un nouveau type d'encadrement
     * 
     * @Route(
     *      "/add",
     *      name="add",
     *      methods={"GET", "POST"},
     * )
     */
    public function add(EntityManagerInterface $em, Request $request)
    {
        $submittedToken = $request->request->get('token');
    
        if (!$this->isCsrfTokenValid('add-edit-item', $submittedToken)) {
            throw $this->createAccessDeniedException('Action non autorisée !!!');
        }

        $frame = new Frame();
    
        $form = $this->createForm(FrameType::class, $frame);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($frame);
            $em->flush();

            return $this->redirectToRoute('frame_browse');
        }
    
        return $this->render('frame/edit.html.twig', [
            'form' => $form->createView(),
            'method' => 'Création',
        ]);
    }

    /**
     * Endpoint to delete a frame
     * Route pour supprimer un type d'encadrement
     * 
     * @Route(
     *      "/delete/{id<\d+>}",
     *      name="delete",
     *      methods={"POST", "DELETE"},
     * )
     */
    public function delete(Frame $frame = null, EntityManagerInterface $em, Request $request)
    {
        if (null === $frame) {
            throw $this->createNotFoundException('Oups ! Type d\'encadrement non trouvé.');
        }
        
        $submittedToken = $request->request->get('token');

        if (!$this->isCsrfTokenValid('delete-item', $submittedToken)) {
            throw $this->createAccessDeniedException('Action non autorisée !!!');
        }

        $em->remove($frame);
        $em->flush();

        return $this->redirectToRoute('frame_browse');
    }
}
