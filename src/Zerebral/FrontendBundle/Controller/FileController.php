<?php

namespace Zerebral\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\SecureParam;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
use Zerebral\BusinessBundle\Model\File\File;

/**
 * @Route("/file")
 */
class FileController extends \Zerebral\CommonBundle\Component\Controller
{
    /**
     * @Route("/download/{id}", name="download_file")
     * @ParamConverter(name="file")
     *
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @SecureParam(name="file", permissions="DOWNLOAD")
     */
    public function downloadAction(File $file)
    {
        $path = $file->getFileStorage()->getWebPath() . DIRECTORY_SEPARATOR . $file->getPath();
        $response = new \Symfony\Component\HttpFoundation\Response();
        //$response->headers->set('X-SendFile', ucfirst($file->getAbsolutePath())); // apache mod_xsendfile
        $response->headers->set('X-Accel-Redirect', $path); // nginx
        $response->headers->set('Content-Type', $file->getMimeType());
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $file->getName() . '"');
        return $response;
    }
}
