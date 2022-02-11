<?php

namespace App\Service;

use App\Repository\FrameRepository;
use App\Repository\CategoryRepository;
use App\Repository\SituationRepository;
use App\Repository\TechniqueRepository;

class AutoAdd
{
    private $FrameRepository;
    private $SituationRepository;
    private $CategoryRepository;
    private $TechniqueRepository;
    private $frameInfo = [
        [1, 'Non renseigné'],
        [2, 'Non encadré'],
        [3, 'Cadre seul'],
        [4, 'Cadre + Verre'],
        [5, 'Spécial'],
    ];
    private $situationInfo = [
        [1, 'Non précisé'],
        [2, 'Chez l\'artiste'],
        [3, 'Vendu à un partiuclier'],
        [4, 'Vendu à des professionnels'],
        [5, 'Actuellement exposé'],
    ];
    private $techniqueInfo = [
        [1, 'Gouache'],
        [2, 'Peinture à l\'huile'],
        [3, 'Peinture à l\'eau'],
        [4, 'Collage'],
        [6, 'Crayon'],
    ];
    private $categoryInfo = [
        [1, 'Arbre'],
        [3, 'Machines agricoles'],
        [4, 'Champs'],
        [5, 'Portrait'],
        [6, 'Abbaye'],
        [7, 'Animaux'],
        [8, 'Dessins sur papier'],
        [9, 'Estran'],
        [10, 'Italie'],
        [11, 'Nature Morte'],
        [12, 'Mer'],
        [13, 'Saint Cloud'],
        [14, 'Sentier'],
        [15, 'Isorel'],
        [16, 'Gouache Ancienne'],
        [17, 'Abstrait'],
        [18, 'Ascension'],
        [19, 'Campagne'],
    ];

    public function __construct(FrameRepository $fr, SituationRepository $sr, CategoryRepository $cr, TechniqueRepository $tr)
    {
        $this->FrameRepository = $fr;
        $this->SituationRepository = $sr;
        $this->CategoryRepository = $cr;
        $this->TechniqueRepository = $tr;
    }

    /**
     * Fill some elements manually, only in dev mod, just so the registration of paintings can be faster
     *
     * @param object $painting  Object Painting to check before save
     * @return object $painting With some modifications
     */
    public function autoAdd(object $painting): object
    {
        if (null === $painting->getFrame()) {
            $painting->setFrame($this->FrameRepository->find(2));
        }

        if (null === $painting->getSituation()) {
            $painting->setSituation($this->SituationRepository->find(2));
        }

        $painting->addCategory($this->CategoryRepository->find(19));
        $painting->addTechnique($this->TechniqueRepository->find(1));

        // $painting->setInformation('DM');

        return $painting;
    }
}