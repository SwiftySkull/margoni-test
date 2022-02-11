<?php

namespace App\Controller\Api;

use App\Entity\Size;
use App\Service\PagesNavigator;
use App\Repository\SizeRepository;
use App\Repository\PaintingRepository;
use App\Repository\PaintingRepositoryWeb;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SizeController extends AbstractController
{
    private $pagesNavigator;

    public function __construct(PagesNavigator $pagesNavigator)
    {
        $this->pagesNavigator = $pagesNavigator;
    }

    /**
     * @Route("/api/sizes", name="api_sizes_browse", methods={"GET"})
     */
    public function browse(SizeRepository $sizeRepository, PaintingRepositoryWeb $prw): Response
    {
        $sizes = $sizeRepository->findAll();

        $sendSizes = [];

        foreach ($sizes as $key => $value) {
            if ([] !== $prw->findBySize($value)) {
                $sendSizes[] = $value;
            }
        }

        return $this->json($sendSizes, 200, [], ['groups' => 'sizes_browse']);
    }

    /**
     * @Route("/api/size/{id<\d+>}", name="api_size_read_main", methods={"GET"})
     * @Route("/api/size/{id<\d+>}/page/{page<\d+>}", name="api_size_read", methods={"GET"})
     */
    public function read(Size $size = null, PaintingRepositoryWeb $paintingRepository, $page = 0)
    {
        if (null === $size) {
            $message = [
                'status' => Response::HTTP_NOT_FOUND,
                'error' => 'Format non trouvé.',
            ];

            return $this->json($message, Response::HTTP_NOT_FOUND);
        }

        $this->pagesNavigator->setAllEntries($paintingRepository->countBySize($size));

        $total = $paintingRepository->countBySize($size);

        $pageId = $this->pagesNavigator->getPageId($page);
        $slice = $this->pagesNavigator->getSlice($pageId);

        $paintings = $paintingRepository->findSizeLimited($size, $slice);

        $results = [$size, ['total results' => $total], $paintings];

        return $this->json($results, 200, [], ['groups' => ['paintings_browse', 'sizes_browse']]);
    }

    /**
     * @Route("/api/sizebyformat/{format}", name="api_size_by_format", methods={"GET"})
     */
    public function getSizeByFormat(Size $size = null, $format)
    {
        if (null === $size) {
            $message = [
                'status' => Response::HTTP_NOT_FOUND,
                'error' => 'Format non trouvé.',
            ];

            return $this->json($message, Response::HTTP_NOT_FOUND);
        }

        return $this->json($size, 200, [], ['groups' => ['sizes_browse']]);
    }
}
