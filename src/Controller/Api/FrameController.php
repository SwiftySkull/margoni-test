<?php

namespace App\Controller\Api;

use App\Entity\Frame;
use App\Service\PagesNavigator;
use App\Repository\FrameRepository;
use App\Repository\PaintingRepository;
use App\Repository\PaintingRepositoryWeb;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FrameController extends AbstractController
{
    private $pagesNavigator;

    public function __construct(PagesNavigator $pagesNavigator)
    {
        $this->pagesNavigator = $pagesNavigator;
    }

    /**
     * @Route("/api/frames", name="api_frames_browse", methods={"GET"})
     */
    public function browse(FrameRepository $frameRepository, PaintingRepositoryWeb $prw): Response
    {
        $frames = $frameRepository->findAll();

        $sendFrames = [];

        foreach ($frames as $key => $value) {
            if ([] !== $prw->findByFrame($value)) {
                $sendFrames[] = $value;
            }
        }

        return $this->json($sendFrames, 200, [], ['groups' => 'frames_browse']);
    }

    /**
     * @Route("/api/frame/{id<\d+>}", name="api_frame_read_main", methods={"GET"})
     * @Route("/api/frame/{id<\d+>}/page/{page<\d+>}", name="api_frame_read", methods={"GET"})
     */
    public function read(Frame $frame = null, PaintingRepositoryWeb $paintingRepository, $page = 0)
    {
        if (null === $frame) {
            $message = [
                'status' => Response::HTTP_NOT_FOUND,
                'error' => 'Type d\'encadrement non trouvé.',
            ];

            return $this->json($message, Response::HTTP_NOT_FOUND);
        }

        $this->pagesNavigator->setAllEntries($paintingRepository->countByFrame($frame));

        $total = $paintingRepository->countByFrame($frame);

        $pageId = $this->pagesNavigator->getPageId($page);
        $slice = $this->pagesNavigator->getSlice($pageId);

        $paintings = $paintingRepository->findFrameLimited($frame, $slice);

        $results = [$frame, ['total results' => $total], $paintings];

        return $this->json($results, 200, [], ['groups' => ['paintings_browse', 'frames_browse']]);
    }
}
