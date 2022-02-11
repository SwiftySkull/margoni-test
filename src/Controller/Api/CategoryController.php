<?php

namespace App\Controller\Api;

use App\Entity\Category;
use App\Service\PagesNavigator;
use App\Repository\CategoryRepository;
use App\Repository\PaintingRepository;
use App\Repository\PaintingRepositoryWeb;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    private $pagesNavigator;

    public function __construct(PagesNavigator $pagesNavigator)
    {
        $this->pagesNavigator = $pagesNavigator;
    }

    /**
     * @Route("/api/categories", name="api_categories_browse", methods={"GET"})
     */
    public function browse(CategoryRepository $categoryRepository, PaintingRepositoryWeb $prw): Response
    {
        $categories = $categoryRepository->findAllAsc();

        $sendCategories = [];

        foreach ($categories as $key => $value) {
            if ([] !== $prw->findByCategory($value)) {
                $sendCategories[] = $value;
            }
        }
        
        return $this->json($sendCategories, 200, [], ['groups' => 'categories_browse']);
    }

    /**
     * @Route("/api/category/{id<\d+>}", name="api_category_read_main", methods={"GET"})
     * @Route("/api/category/{id<\d+>}/page/{page<\d+>}", name="api_category_read", methods={"GET"})
     */
    public function read(Category $category = null, PaintingRepositoryWeb $paintingRepository, $page = 0)
    {
        if (null === $category) {
            $message = [
                'status' => Response::HTTP_NOT_FOUND,
                'error' => 'Catégorie non trouvée.',
            ];

            return $this->json($message, Response::HTTP_NOT_FOUND);
        }

        $this->pagesNavigator->setAllEntries($paintingRepository->countByCateg($category));

        $total = $paintingRepository->countByCateg($category);

        $pageId = $this->pagesNavigator->getPageId($page);
        $slice = $this->pagesNavigator->getSlice($pageId);

        $paintings = $paintingRepository->findCategLimited($category, $slice);

        $results = [$category, ['total results' => $total], $paintings];

        return $this->json($results, 200, [], ['groups' => ['paintings_browse', 'categories_browse']]);
    }

    /**
     * @Route("/api/getone/category", name="api_category_get_one_element", methods={"GET"})
     */
    public function getOneFromCategory(CategoryRepository $categoryRepository, PaintingRepositoryWeb $paintingRepository)
    {
        $categories = $categoryRepository->findAll();

        $sendCategories = [];

        foreach ($categories as $key => $value) {
            if ([] !== $paintingRepository->findByCategory($value)) {
                $sendCategories[] = $value;
            }
        }

        $shuffledPictures = [];

        foreach ($sendCategories as $key => $value) {
            $random = $paintingRepository->getOneFromCategory($value->getId());

            shuffle($random);
            
            if (count($random) > 0) {
                $shuffledPictures[] = ['id' => $value->getId(), 'painting' => $random[0]];
            } else {
                $shuffledPictures[] = ['id' => $value->getId(), 'painting' => null];
            }
        }

        return $this->json($shuffledPictures, 200, [], ['groups' => ['paintings_browse', 'categories_browse']]);
    }

    /**
     * @Route("/api/categbyname/{name}", name="api_category_by_name", methods={"GET"})
     */
    public function getCategoryByName(Category $category = null, $name, CategoryRepository $cr)
    {
        if (null !== $name) {
            $stringFromUrl = str_replace(['-l-', '-'], [' l\'', ' '], $name);
        }

        $category = $cr->getPaintingByCategoryName($stringFromUrl);

        if (null === $category) {
            $message = [
                'status' => Response::HTTP_NOT_FOUND,
                'error' => 'Catégorie non trouvée.',
            ];

            return $this->json($message, Response::HTTP_NOT_FOUND);
        }

        return $this->json($category, 200, [], ['groups' => ['categories_browse']]);
    }
}
