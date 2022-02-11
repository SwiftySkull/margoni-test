<?php

namespace App\Controller\Admin;

use App\Entity\Technique;
use App\Form\TechniqueType;
use App\Service\PagesNavigator;
use App\Repository\PaintingRepository;
use App\Repository\TechniqueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller which handles all the technique research,
 * and also handles the add/edit/delete of the techniques
 * 
 * Controlleur qui se charge de toutes les recherches par technique
 * ainsi que les ajout/modification/suppression des techniques
 * 
 * @Route("/technique", name="technique_")
 */
class TechniqueController extends AbstractController
{
    private $pagesNavigator;

    public function __construct(PagesNavigator $pagesNavigator)
    {
        $this->pagesNavigator = $pagesNavigator;
    }

    /**
     * Endpoint to show all the techniques
     * Route pour montrer toutes les techniques
     * 
     * @Route(
     *      "/browse",
     *      name="browse",
     *      methods={"GET"},
     * )
     */
    public function browse(TechniqueRepository $techniqueRepository): Response
    {
        $techniques = $techniqueRepository->findAllAsc();

        return $this->render('technique/browse.html.twig', [
            'techniques' => $techniques,
        ]);
    }

    /**
     * Endpoint to show all the paintings of one technique
     * Route pour montrer toutes les peintures d'une technique
     * 
     * @Route(
     *      "/read/{id<\d+>}/page/{page<\d+>}",
     *      name="read",
     *      methods={"GET"},
     * )
     */
    public function read(Technique $technique = null, PaintingRepository $paintingRepository, $page)
    {
        if (null === $technique) {
            throw $this->createNotFoundException('Oups ! Technique non trouvée.');
        }

        $this->pagesNavigator->setAllEntries($paintingRepository->countByTechnique($technique));

        $pageId = $this->pagesNavigator->getPageId($page);
        $slice = $this->pagesNavigator->getSlice($pageId);

        $paintings = $paintingRepository->findTechniqueLimited($technique, $slice);

        return $this->render('technique/read.html.twig', [
            'paintings' => $paintings,
            'technique' => $technique,
            'pages' => $this->pagesNavigator->getMinMax($pageId),
            'previousPage' => $this->pagesNavigator->getPreviousPage($pageId),
            'nextPage' => $this->pagesNavigator->getNextPage($pageId),
            'totalPages' => $this->pagesNavigator->getTotalPages(),
            'count' => $paintingRepository->countByTechnique($technique),
        ]);
    }

    /**
     * Endpoint to update the name of a technique
     * Route pour modifier le nom d'une technique
     * 
     * @Route(
     *      "/edit/{id<\d+>}",
     *      name="edit",
     *      methods={"GET", "POST"},
     * )
     */
    public function edit(Technique $technique = null, EntityManagerInterface $em, Request $request)
    {
        $submittedToken = $request->request->get('token');
        if (!$this->isCsrfTokenValid('add-edit-item', $submittedToken)) {
            throw $this->createAccessDeniedException('Action non autorisée !!!');
        }

        if (null === $technique) {
            throw $this->createNotFoundException('Oups ! Technique non trouvée.');
        }

        $form = $this->createForm(TechniqueType::class, $technique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
    
            $em->flush();

            return $this->redirectToRoute('technique_browse');
        }

        return $this->render('technique/edit.html.twig', [
            'technique' => $technique,
            'form' => $form->createView(),
            'method' => 'Modification',
        ]);
    }

    /**
     * Endpoint to add a new technique
     * Route pour ajouter une nouvelle technique
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

        $technique = new Technique();
    
        $form = $this->createForm(TechniqueType::class, $technique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($technique);
            $em->flush();

            return $this->redirectToRoute('technique_browse');
        }
    
        return $this->render('technique/edit.html.twig', [
            'form' => $form->createView(),
            'method' => 'Création',
        ]);
    }

    /**
     * Endpoint to delete a technique
     * Route pour supprimer une technique
     * 
     * @Route(
     *      "/delete/{id<\d+>}",
     *      name="delete",
     *      methods={"POST", "DELETE"},
     * )
     */
    public function delete(Technique $technique = null, EntityManagerInterface $em, Request $request)
    {
        if (null === $technique) {
            throw $this->createNotFoundException('Oups ! Technique non trouvée.');
        }
        
        $submittedToken = $request->request->get('token');

        if (!$this->isCsrfTokenValid('delete-item', $submittedToken)) {
            throw $this->createAccessDeniedException('Action non autorisée !!!');
        }

        $em->remove($technique);
        $em->flush();

        return $this->redirectToRoute('technique_browse');
    }
}
