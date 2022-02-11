<?php

namespace App\Controller\Api;

use App\Service\PagesNavigator;
use App\Repository\PaintingRepository;
use App\Repository\PaintingRepositoryWeb;
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
     * @Route("/api/search/{search}", name="api_search", methods={"GET"})
     */
    public function search($search = null, PaintingRepositoryWeb $paintingRepository)
    {
        if (null === $search) {
            $message = [
                'status' => Response::HTTP_NOT_FOUND,
                'error' => 'Aucune recherche en cours.',
            ];

            return $this->json($message, Response::HTTP_NOT_FOUND);
        }

        $totalSearch = $paintingRepository->findBySearch($search);

        if (null == $totalSearch) {
            $message = [
                'status' => Response::HTTP_NOT_FOUND,
                'error' => 'Aucun résultat trouvé',
                'search' => $search,
            ];

            return $this->json($message, Response::HTTP_NOT_FOUND);
        }

        $this->pagesNavigator->setAllEntries(count($totalSearch));

        $total = count($totalSearch);

        $paintings = $paintingRepository->findBySearchLimited($search);

        $results = [$search, ['total results' => $total], $paintings];

        return $this->json($results, 200, [], ['groups' => ['paintings_browse']]);
    }

    /**
     * @Route("/api/search/{search}/page/{page<\d+>}", name="api_paintings_browse_plus", methods={"GET"})
     */
    public function browsePlus(PaintingRepositoryWeb $paintingRepository, $search, $page)
    {
        $totalSearch = $paintingRepository->findBySearch($search);
        $this->pagesNavigator->setAllEntries(count($totalSearch));

        $pageId = $this->pagesNavigator->getPageId($page);
        $slice = $this->pagesNavigator->getSlice($pageId);

        $paintings = $paintingRepository->findBySearchLimited($search, $slice);

        $total = count($totalSearch);

        $results = [$search, ['total results' => $total], $paintings];

        return $this->json($results, 200, [], ['groups' => 'paintings_browse']);
    }

}
