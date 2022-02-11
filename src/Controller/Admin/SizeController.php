<?php

namespace App\Controller\Admin;

use App\Entity\Size;
use App\Form\SizeType;
use App\Service\PagesNavigator;
use App\Repository\SizeRepository;
use App\Repository\PaintingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller which handles all the size research,
 * and also handles the add/edit/delete of the size
 * 
 * Controlleur qui se charge de toutes les recherches par taille/format
 * ainsi que les ajout/modification/suppression des tailles/formats
 * 
 * @Route("/size", name="size_")
 */
class SizeController extends AbstractController
{
    private $pagesNavigator;

    public function __construct(PagesNavigator $pagesNavigator)
    {
        $this->pagesNavigator = $pagesNavigator;
    }

    /**
     * Endpoint to show all the sizes
     * Route pour montrer toutes les taille/format
     * 
     * @Route(
     *      "/browse",
     *      name="browse",
     *      methods={"GET"},
     * )
     */
    public function browse(SizeRepository $sizeRepository): Response
    {
        $sizes = $sizeRepository->findAll();

        return $this->render('size/browse.html.twig', [
            'sizes' => $sizes,
        ]);
    }

    /**
     * Endpoint to show all the paintings of one size
     * Route pour montrer toutes les peintures d'une taille/format
     * 
     * @Route(
     *      "/read/{id<\d+>}/page/{page<\d+>}",
     *      name="read",
     *      methods={"GET"},
     * )
     */
    public function read(Size $size = null, PaintingRepository $paintingRepository, $page)
    {
        if (null === $size) {
            throw $this->createNotFoundException('Oups ! Format non trouvé.');
        }

        $this->pagesNavigator->setAllEntries($paintingRepository->countBySize($size));

        $pageId = $this->pagesNavigator->getPageId($page);
        $slice = $this->pagesNavigator->getSlice($pageId);

        $paintings = $paintingRepository->findSizeLimited($size, $slice);

        return $this->render('size/read.html.twig', [
            'paintings' => $paintings,
            'size' => $size,
            'pages' => $this->pagesNavigator->getMinMax($pageId),
            'previousPage' => $this->pagesNavigator->getPreviousPage($pageId),
            'nextPage' => $this->pagesNavigator->getNextPage($pageId),
            'totalPages' => $this->pagesNavigator->getTotalPages(),
            'count' => $paintingRepository->countBySize($size),
        ]);
    }

    /**
     * Endpoint to update the name of a size
     * Route pour modifier le nom d'une taille/format
     * 
     * @Route(
     *      "/edit/{id<\d+>}",
     *      name="edit",
     *      methods={"GET", "POST"},
     * )
     */
    public function edit(Size $size = null, EntityManagerInterface $em, Request $request)
    {
        $submittedToken = $request->request->get('token');
        if (!$this->isCsrfTokenValid('add-edit-item', $submittedToken)) {
            throw $this->createAccessDeniedException('Action non autorisée !!!');
        }

        if (null === $size) {
            throw $this->createNotFoundException('Oups ! Format non trouvé.');
        }

        $form = $this->createForm(SizeType::class, $size);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
    
            $em->flush();

            return $this->redirectToRoute('size_browse');
        }

        return $this->render('size/edit.html.twig', [
            'size' => $size,
            'form' => $form->createView(),
            'method' => 'Modification',
        ]);
    }

    /**
     * Endpoint to add a new size
     * Route pour ajouter une nouvelle taille/format
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

        $size = new Size();
    
        $form = $this->createForm(SizeType::class, $size);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($size);
            $em->flush();

            return $this->redirectToRoute('size_browse');
        }
    
        return $this->render('size/edit.html.twig', [
            'form' => $form->createView(),
            'method' => 'Création',
        ]);
    }

    /**
     * Endpoint to delete a size
     * Route pour supprimer une taille/format
     * 
     * @Route(
     *      "/delete/{id<\d+>}",
     *      name="delete",
     *      methods={"POST", "DELETE"},
     * )
     */
    public function delete(Size $size = null, EntityManagerInterface $em, Request $request)
    {
        if (null === $size) {
            throw $this->createNotFoundException('Oups ! Format non trouvé.');
        }
        
        $submittedToken = $request->request->get('token');

        if (!$this->isCsrfTokenValid('delete-item', $submittedToken)) {
            throw $this->createAccessDeniedException('Action non autorisée !!!');
        }

        $em->remove($size);
        $em->flush();

        return $this->redirectToRoute('size_browse');
    }
}
