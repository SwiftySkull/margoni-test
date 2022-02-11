<?php

namespace App\Controller\Admin\MainController;

use App\Entity\Painting;
use App\Entity\Picture;
use App\Form\PaintingType;
use App\Repository\FrameRepository;
use App\Repository\PaintingRepository;
use App\Repository\PictureRepository;
use App\Repository\SituationRepository;
use App\Repository\SizeRepository;
use App\Service\CheckingExistingPainting;
use App\Service\FormatConversion;
use App\Service\PagesNavigator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AddEditController extends AbstractController
{
    /**
     * Form to edit the informations of a painting
     * 
     * @Route(
     *      "/paint/edit/{id<\d+>}",
     *      name="paint_edit",
     *      methods={"GET", "POST"},
     * )
     */
    public function edit(Painting $painting = null, $id, Request $request, EntityManagerInterface $em, PictureRepository $pictureRepository, FormatConversion $formatConversion, CheckingExistingPainting $checkingExistingPainting)
    {
        $submittedToken = $request->request->get('token');
        if (!$this->isCsrfTokenValid('add-edit-item', $submittedToken)) {
            throw $this->createAccessDeniedException('Action non autorisée !!!');
        }

        if (null === $painting) {
            throw $this->createNotFoundException('Oups ! Tableau non trouvé.'); 
        }

        $form = $this->createForm(PaintingType::class, $painting);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (null != $request->files->get('painting')['picture']) {
                $actualPicture = $pictureRepository->find($painting->getPicture());
                $pictureTitle = preg_filter('/.(jpg|JPG|PNG|png|JPEG|jpeg)/', '', $request->files->get('painting')['picture']->getClientOriginalName());
                $actualPicture->setTitle($pictureTitle);
                $actualPicture->setPathname($request->files->get('painting')['picture']->getClientOriginalName());
                $actualPicture->setFile(base64_encode(file_get_contents($request->files->get('painting')['picture'])));
                
                $frenchOrientation = $formatConversion->getPictureOrientation($request->files->get('painting')['picture']);
                $actualPicture->setOrientation($frenchOrientation ? 'V' : 'H');
    
                $painting->setPicture($actualPicture);

                $newDbTitle = str_replace(['-', '_'], ' ', $pictureTitle);

                if ($newDbTitle != $painting->getDbName()) {
                    $painting->setDbName($newDbTitle);                
                }    
            }
            // Automatic modification of the size and format
            $painting = $formatConversion->setSizes($painting);

            // Setting warning is there is a size error
            $painting = $formatConversion->setWarningSizeMessage($painting);

            if (null != $painting->getSize()) {
                $checkingConversion = $formatConversion->checkWidthHeightAndFormat($painting->getSize()->getFormat(), $painting->getHeight(), $painting->getWidth());

                if (!$checkingConversion) {
                    $this->addFlash('danger', 'Il y a une différence entre les dimensions et le format mentionné !');
                }    
            }

            $existingPainting = $checkingExistingPainting->checkPainting($painting);
            if ($existingPainting['check']) {
                $this->addFlash('warning', $existingPainting['message']);
            } else {
                $this->addFlash('success', 'Peinture modifiée avec succès !');
            }

            $em->flush();

            return $this->redirectToRoute('read_paint', ['id' => $id]);
        }

        return $this->render('main/edit.html.twig', [
            'painting' => $painting,
            'method' => 'Modification',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/paint/add", name="paint_add", methods={"POST", "GET"})
     */
    public function add(EntityManagerInterface $em, Request $request, FormatConversion $formatConversion, CheckingExistingPainting $checkingExistingPainting)
    {
        $submittedToken = $request->request->get('token');
        if (!$this->isCsrfTokenValid('add-edit-item', $submittedToken)) {
            throw $this->createAccessDeniedException('Action non autorisée !!!');
        }

        $painting = new Painting();

        $form = $this->createForm(PaintingType::class, $painting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Insertion of the picture
            $picture = new Picture();


            $pictureTitle = preg_filter('/.(jpg|JPG|PNG|png|JPEG|jpeg)/', '', $request->files->get('painting')['picture']->getClientOriginalName());
            $picture->setTitle($pictureTitle);
            $picture->setPathname($request->files->get('painting')['picture']->getClientOriginalName());
            $picture->setFile(base64_encode(file_get_contents($request->files->get('painting')['picture'])));

            $frenchOrientation = $formatConversion->getPictureOrientation($request->files->get('painting')['picture']);
            $picture->setOrientation($frenchOrientation ? 'V' : 'H');

            $em->persist($picture);

            if (null == $painting->getDbName()) {
                $dbName = str_replace(['-', '_'], ' ', $pictureTitle);
                $painting->setDbName($dbName);                
            }

            $painting->setPicture($picture);
            // End of picture

            // Automatic modification of the size and format
            $painting = $formatConversion->setSizes($painting);
            // Setting warning is there is a size error
            $painting = $formatConversion->setWarningSizeMessage($painting);

            if (null != $painting->getSize()) {
                $checkingConversion = $formatConversion->checkWidthHeightAndFormat($painting->getSize()->getFormat(), $painting->getHeight(), $painting->getWidth());

                if (!$checkingConversion) {
                    $this->addFlash('danger', 'Il y a une différence entre les dimensions et le format mentionné !');
                }    
            }
            // End of the size and format
            
            $existingPainting = $checkingExistingPainting->checkPainting($painting);
            if ($existingPainting['check']) {
                $this->addFlash('warning', $existingPainting['message']);
            } else {
                $this->addFlash('success', 'Peinture ajoutée avec succès !');
            }

            $em->persist($painting);
            $em->flush();

            return $this->redirectToRoute('read_paint', ['id' => $painting->getId()]);
        }

        return $this->render('main/edit.html.twig', [
            'form' => $form->createView(),
            'method' => 'Création',
        ]);
    }

    /**
     * @Route(
     *      "/paint/display-on-website/{id<\d+>}",
     *      name="display_on_website",
     *      methods={"POST"},
     * )
     */
    public function displayOnWebsite(Painting $painting = null, $id, EntityManagerInterface $em)
    {
        $painting->setWebDisplay(!$painting->getWebDisplay());
        $em->flush();

        return $this->redirectToRoute('read_paint', ['id' => $id]);
    }
}
