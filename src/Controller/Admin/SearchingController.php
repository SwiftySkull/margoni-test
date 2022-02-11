<?php

namespace App\Controller\Admin;

use App\Service\PagesNavigator;
use App\Repository\PaintingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchingController extends AbstractController
{
    private $pagesNavigator;

    public function __construct(PagesNavigator $pagesNavigator)
    {
        $this->pagesNavigator = $pagesNavigator;
    }

    /**
     * @Route(
     *      "/search/{search}",
     *      name="search",
     *      methods={"POST", "GET"},
     * )
     */
    public function search(Request $request, PaintingRepository $paintingRepository, $search = null)
    {
        $searchbar = $request->request->get('search');

        if (null == $search && null == $searchbar) {
            return $this->redirectToRoute('home');
        }

        if (null != $search && null == $searchbar) {
            $research = $search;
        }

        if (null == $search && null != $searchbar) {
            $research = $searchbar;
        }

        $totalSearch = $paintingRepository->findBySearch($research);
        $this->pagesNavigator->setAllEntries(count($totalSearch));

        $paintings = $paintingRepository->findBySearchLimited($research);

        $totalPages = $this->pagesNavigator->getTotalPages();

        dump($paintings);
        return $this->render('main/search.html.twig', [
            'paintings' => $paintings,
            'pages' => $this->pagesNavigator->getMinMax(),
            'totalPages' => $totalPages,
            'previousPage' => $this->pagesNavigator->getPreviousPage(),
            'nextPage' => $this->pagesNavigator->getNextPage(),
            'search' => $research,
            'count' => count($totalSearch),
        ]);
    }

    /**
     * @Route("/search/{search}/page/{page<\d+>}", name="search_plus", methods={"GET", "POST"})
     */
    public function searchPlus(PaintingRepository $paintingRepository, $page, $search)
    {
        $totalSearch = $paintingRepository->findBySearch($search);
        $this->pagesNavigator->setAllEntries(count($totalSearch));

        $pageId = $this->pagesNavigator->getPageId($page);
        $slice = $this->pagesNavigator->getSlice($pageId);

        $paintings = $paintingRepository->findBySearchLimited($search, $slice);

        return $this->render('main/search.html.twig', [
            'paintings' => $paintings,
            'pages' => $this->pagesNavigator->getMinMax($pageId),
            'totalPages' => $this->pagesNavigator->getTotalPages(),
            'previousPage' => $this->pagesNavigator->getPreviousPage($pageId),
            'nextPage' => $this->pagesNavigator->getNextPage($pageId),
            'search' => $search,
            'count' => count($totalSearch),
        ]);

    }
}
