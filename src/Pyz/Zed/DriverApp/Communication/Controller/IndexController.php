<?php

namespace Pyz\Zed\DriverApp\Communication\Controller;

use Generated\Shared\Transfer\DriverAppReleaseTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Pyz\Zed\DriverApp\Business\DriverAppFacade getFacade()
 * @method \Pyz\Zed\DriverApp\Communication\DriverAppCommunicationFactory getFactory()
 * @method \Pyz\Zed\DriverApp\Persistence\DriverAppQueryContainer getQueryContainer()
 */
class IndexController extends AbstractController
{
    protected const APK_FILE_NAME_PREFIX = 'durst_driver_app';
    protected const APK_FILE_EXTENSION = 'apk';

    public const PARAM_ID_RELEASE = 'id-release';
    public const URL_DELETE = '/driver-app/index/delete';
    public const URL_INDEX = '/driver-app/index';

    /**
     * @return array
     */
    public function indexAction()
    {
        $table = $this
            ->getFactory()
            ->createReleaseTable();

        return $this->viewResponse([
            'table' => $table->render(),
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function tableAction()
    {
        $table = $this
            ->getFactory()
            ->createReleaseTable();

        return $this->jsonResponse(
            $table->fetchData()
        );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request)
    {
        $idDriverAppRelease = $this
            ->castId($request->get(self::PARAM_ID_RELEASE));

        $this
            ->getFacade()
            ->deleteRelease($idDriverAppRelease);

        $this
            ->addSuccessMessage("Release wurde gelöscht");

        return $this
            ->redirectResponse(self::URL_INDEX);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createAction(Request $request)
    {
        $dataProvider = $this
            ->getFactory()
            ->createReleaseTypeDataProvider();

        $form = $this
            ->getFactory()
            ->createReleaseForm(
                $dataProvider->getData(null),
                $dataProvider->getOptions()
            )
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var \Generated\Shared\Transfer\DriverAppReleaseTransfer $releaseTransfer */
            $releaseTransfer = $form->getData();

            try{
                $this->saveFile($releaseTransfer);
            }catch (FileException $exception){
                $this->addErrorMessage('Beim speichern der APK-Datei ist ein Fehler aufgetreten');
                return $this
                    ->redirectResponse(self::URL_INDEX);
            }

            $releaseTransfer = $this
                ->getFacade()
                ->addRelease($releaseTransfer);

            if($releaseTransfer->getIdDriverAppRelease() !== null){
                $this->addSuccessMessage("Driver App Release wurde erstellt");
                return $this
                    ->redirectResponse(self::URL_INDEX);
            }

            $this
                ->addErrorMessage("Driver App Release konnte nicht erstellt werden");
            return $this
                ->redirectResponse(self::URL_INDEX);
        }

        return $this
            ->viewResponse([
                'form' => $form->createView(),
            ]);
    }

    /**
     * @param \Generated\Shared\Transfer\DriverAppReleaseTransfer $releaseTransfer
     * @return \Generated\Shared\Transfer\DriverAppReleaseTransfer
     * @throws \Symfony\Component\HttpFoundation\File\Exception\FileException
     */
    protected function saveFile(DriverAppReleaseTransfer $releaseTransfer): DriverAppReleaseTransfer
    {
        /** @var File $apkFile */
        $apkFile = $releaseTransfer->getApkFilePath();

        $fileName = sprintf(
            '%s_%s.%s',
            self::APK_FILE_NAME_PREFIX,
            trim(str_replace('.','_',$releaseTransfer->getVersion())),
            self::APK_FILE_EXTENSION
        );

        $apkFile->move(
            $this->getFactory()->getConfig()->getUploadPath(),
            $fileName
        );

        $releaseTransfer->setApkFilePath($fileName);

        return $releaseTransfer;
    }
}
