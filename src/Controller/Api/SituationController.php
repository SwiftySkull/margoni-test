<?php

namespace App\Controller\Api;

use App\Entity\Situation;
use App\Service\PagesNavigator;
use App\Repository\PaintingRepository;
use App\Repository\SituationRepository;
use App\Repository\PaintingRepositoryWeb;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SituationController extends AbstractController
{
    private $pagesNavigator;

    public function __construct(PagesNavigator $pagesNavigator)
    {
        $this->pagesNavigator = $pagesNavigator;
    }

    /**
     * @Route("/api/situations", name="api_situations_browse", methods={"GET"})
     */
    public function browse(SituationRepository $situationRepository, PaintingRepositoryWeb $prw): Response
    {
        $situations = $situationRepository->findAll();

        $sendsituations = [];

        foreach ($situations as $key => $value) {
            if ([] !== $prw->findBySize($value)) {
                $sendsituations[] = $value;
            }
        }
        
        return $this->json($sendsituations, 200, [], ['groups' => 'situations_browse']);
    }

    /**
     * @Route("/api/situation/{id<\d+>}", name="api_situation_read_main", methods={"GET"})
     * @Route("/api/situation/{id<\d+>}/page/{page<\d+>}", name="api_situation_read", methods={"GET"})
     */
    public function read(Situation $situation = null, PaintingRepositoryWeb $paintingRepository, $page = 0)
    {
        if (null === $situation) {
            $message = [
                'status' => Response::HTTP_NOT_FOUND,
                'error' => 'Collection non trouvée.',
            ];

            return $this->json($message, Response::HTTP_NOT_FOUND);
        }

        $this->pagesNavigator->setAllEntries($paintingRepository->countBySituation($situation));

        $total = $paintingRepository->countBySituation($situation);

        $pageId = $this->pagesNavigator->getPageId($page);
        $slice = $this->pagesNavigator->getSlice($pageId);

        $paintings = $paintingRepository->findSituationLimited($situation, $slice);

        $results = [$situation, ['total results' => $total], $paintings];

        return $this->json($results, 200, [], ['groups' => ['paintings_browse', 'situations_browse']]);
    }
}
