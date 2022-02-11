<?php

namespace App\Service;

use App\Repository\PaintingRepository;
use App\Repository\PictureRepository;

class CheckingExistingPainting
{
    private $paintingRepository;

    public function __construct(PaintingRepository $paintingRepository)
    {
        $this->paintingRepository = $paintingRepository;
    }

    /**
     * Check if the painting being registered already existed
     * Warning ! The name may be already exist but with an other picture,
     * severals painting can have the same name but being different.
     *
     * @param object $painting  Object Painting to check before save
     * @return array            ['check' => false if doesn't exist/true if already exists,
     *                          'message' => The warning message to flash]
     */
    public function checkPainting(object $painting): array
    {
        $existingTitlePaintings = null;
        if (null != $painting->getTitle()) {
            $existingTitlePaintings = $this->paintingRepository->findBy(['title' => $painting->getTitle()]);
        }
        $existingDbNamePaintings = $this->paintingRepository->findBy(['dbName' => $painting->getDbName()]);

        $check = ['check' => false];

        if (null != $existingDbNamePaintings && $existingDbNamePaintings[0]->getId() != $painting->getId()) {
            $check['check'] = true;
            $check['message'] = 'Il existe peut-être déjà une peinture avec cette photo ! Merci de copier/coller "'.$painting->getDbName().'" dans la barre de recherche en cas de doute !';

            if (null === $painting->getId()) {
                foreach ($existingDbNamePaintings as $paint) {
                    if ($painting->getTitle() === $paint->getTitle()) {
                        $check['message'] = 'Attention une peinture a été trouvé avec le même titre et la même image. En cas de doute merci de rechercher dans la barre de recherche : "'.$painting->getTitle().'" Ou bien "'.$painting->getDbName().'"';

                        return $check;
                    }
                }
            }
        }

        $editTitle = true;

        if (null != $existingTitlePaintings) {
            foreach ($existingTitlePaintings as $value) {
                if ($value->getId() === $painting->getId()) {
                    $editTitle = false;
                }
            }    
        }

        if (null != $existingTitlePaintings && $editTitle) {
            $check['check'] = true;
            $check['message'] = 'Il existe peut-être déjà une peinture avec ce titre ! Merci de copier/coller "'.$painting->getTitle().'" dans la barre de recherche en cas de doute !';
            if (null === $painting->getId()) {
                foreach ($existingTitlePaintings as $paint) {
                    if ($painting->getDbName() === $paint->getDbName()) {
                        $check['message'] = 'Attention une peinture a été trouvé avec le même titre et la même image. En cas de doute merci de rechercher dans la barre de recherche : "'.$painting->getTitle().'" ou bien "'.$painting->getDbName().'" !';
    
                        return $check;
                    }
                }    
            }
        }

        if (null != $existingTitlePaintings && null != $existingDbNamePaintings && null === $painting->getId()) {
            $check['check'] = true;
            $check['message'] = 'Attention plusieurs peintures ont été trouvé avec le même titre ou la même image. En cas de doute merci de rechercher dans la barre de recherche : "'.$painting->getTitle().'" ou bien "'.$painting->getDbName().'" !';
        }

        return $check;
    }
}