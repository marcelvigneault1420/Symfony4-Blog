<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DevController extends AbstractController
{
    /**
     * @Route("/phpinfo", name="phpinfo")
     */
    public function index()
    {
        ob_start();
        phpinfo();
        $phpinfo = ob_get_contents();
        ob_end_clean();
        return $this->render('phpinfo.html.twig', array(
            'phpinfo' => $phpinfo,
        ));
    }
}
