<?php

namespace App\Handler;

use App\Entity\Product;
use App\Tool\DeleteFile;
use App\Tool\EntityManager;
use App\Tool\FileUpload;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class FormProductHandler
{
    /** @var RequestStack */
    private $request;
    /** @var FileUpload */
    private $fileUpload;
    /** @var EntityManager */
    private $entityManager;
    /** @var DeleteFile */
    private $deleteFile;

    public function __construct(RequestStack $request, FileUpload $fileUpload, EntityManager $entityManager, DeleteFile $deleteFile)
    {
        $this->request = $request;
        $this->fileUpload = $fileUpload;
        $this->entityManager = $entityManager;
        $this->deleteFile = $deleteFile;
    }

    public function add(FormInterface $form, Product $product): bool
    {
        $form->handleRequest($this->request->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('image')->getData();
            if ($brochureFile) {
                $brochureFileName = $this->fileUpload->upload($brochureFile);
                $product->setImage($brochureFileName);
            }
            $this->entityManager->Add($product);

            return true;
        }

        return false;
    }

    public function update(FormInterface $form, Product $product): bool
    {
        $form->handleRequest($this->request->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('image')->getData();
            if ($brochureFile) {
                $brochureFileName = $this->fileUpload->upload($brochureFile);
                $product->setImage($brochureFileName);
            }
            $this->entityManager->update();

            return true;
        }

        return false;
    }

    public function delete(Product $product): void
    {
        $this->deleteFile->delete($product->getImage());
        $this->entityManager->remove($product);
    }
}
