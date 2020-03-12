<?php
/**
 * Created by PhpStorm.
 * User: chrystelle
 * Date: 2020-03-12
 * Time: 14:36
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    public function index()
    {
        return $this->render('pages/admin.html.twig');
    }
}
