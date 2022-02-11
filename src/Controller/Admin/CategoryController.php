<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Service\PagesNavigator;
use App\Repository\CategoryRepository;
use App\Repository\PaintingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

/**
 * Controller which handles all the category research,
 * and also handles the add/edit/delete of the categories
 * 
 * Controlleur qui se charge de toutes les recherches par catégorie
 * ainsi que les ajout/modification/suppression des catégories
 * 
 * @Route("/category", name="category_")
 */
class CategoryController extends AbstractController
{
    private $pagesNavigator;

    public function __construct(PagesNavigator $pagesNavigator)
    {
        $this->pagesNavigator = $pagesNavigator;
    }

    /**
     * Endpoint to show all the categories
     * Route pour montrer toutes les catégories
     * 
     * @Route(
     *      "/browse",
     *      name="browse",
     *      methods={"GET"},
     * )
     */
    public function browse(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAllAsc();

        return $this->render('category/browse.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * Endpoint to show all the paintings of one category
     * Route pour montrer toutes les peintures d'une catégorie
     * 
     * @Route(
     *      "/read/{id<\d+>}/page/{page<\d+>}",
     *      name="read",
     *      methods={"GET"},
     * )
     */
    public function read(Category $category = null, PaintingRepository $paintingRepository, $page)
    {
        if (null === $category) {
            throw $this->createNotFoundException('Oups ! Catégorie non trouvée.');
        }
        $this->pagesNavigator->setAllEntries($paintingRepository->countByCateg($category));

        $pageId = $this->pagesNavigator->getPageId($page);
        $slice = $this->pagesNavigator->getSlice($pageId);

        $paintings = $paintingRepository->findCategLimited($category, $slice);

        return $this->render('category/read.html.twig', [
            'paintings' => $paintings,
            'category' => $category,
            'pages' => $this->pagesNavigator->getMinMax($pageId),
            'previousPage' => $this->pagesNavigator->getPreviousPage($pageId),
            'nextPage' => $this->pagesNavigator->getNextPage($pageId),
            'totalPages' => $this->pagesNavigator->getTotalPages(),
            'count' => $paintingRepository->countByCateg($category),
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
    public function edit(Category $category = null, EntityManagerInterface $em, Request $request)
    {
        $submittedToken = $request->request->get('token');
        if (!$this->isCsrfTokenValid('add-edit-item', $submittedToken)) {
            throw $this->createAccessDeniedException('Action non autorisée !!!');
        }

        if (null === $category) {
            throw $this->createNotFoundException('Oups ! Catégorie non trouvée.');
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
    
            $em->flush();

            return $this->redirectToRoute('category_browse');
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            'method' => 'Modification',
        ]);
    }

    /**
     * Endpoint to add a new category
     * Route pour ajouter une nouvelle catégorie
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

        $category = new Category();
    
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('category_browse');
        }
    
        return $this->render('category/edit.html.twig', [
            'form' => $form->createView(),
            'method' => 'Création',
        ]);
    }

    /**
     * Endpoint to delete a category
     * Route pour supprimer une catégorie
     * 
     * @Route(
     *      "/delete/{id<\d+>}",
     *      name="delete",
     *      methods={"POST", "DELETE"},
     * )
     */
    public function delete(Category $category = null, EntityManagerInterface $em, Request $request)
    {
        if (null === $category) {
            throw $this->createNotFoundException('Oups ! Catégorie non trouvée.');
        }
        
        $submittedToken = $request->request->get('token');

        if (!$this->isCsrfTokenValid('delete-item', $submittedToken)) {
            throw $this->createAccessDeniedException('Action non autorisée !!!');
        }

        $em->remove($category);
        $em->flush();

        return $this->redirectToRoute('category_browse');
    }
}
