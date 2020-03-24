<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ApiController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @return Response
     */
    public function index(): Response
    {

        return $this->render('api/index.html.twig', [

        ]);
    }

    /**
     * @Route("/listeregions", name="listeregions")
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function listeRegions(SerializerInterface $serializer): Response
    {
        $listeRegions = file_get_contents('https://geo.api.gouv.fr/regions');
        $mesRegions = $serializer->deserialize($listeRegions, 'App\Entity\Region[]', 'json');

        return $this->render('api/listeRegions.html.twig', [
            'mesRegions' => $mesRegions,
        ]);
    }

    /**
     * @Route("/departementsparregion", name="departements_Par_Region")
     * @param Request $request
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function departementsParRegion(Request $request, SerializerInterface $serializer): Response
    {
        $codeRegion = $request->query->get('region');
        $listeRegions= file_get_contents('https://geo.api.gouv.fr/regions');
        $mesRegions = $serializer->deserialize($listeRegions, 'App\Entity\Region[]', 'json');

        if ($codeRegion === null || $codeRegion === 'toutes'){
            $mesDepartements = file_get_contents('https://geo.api.gouv.fr/departements');
        }else{
            $mesDepartements = file_get_contents('https://geo.api.gouv.fr/regions/'.$codeRegion.'/departements');
        }
        //décodage du format jsoon en tableau
        $arrayMesDepartements = $serializer->decode($mesDepartements , 'json');

        return $this->render('api/departementsParRegion.html.twig', [
            'mesRegions' => $mesRegions,
            'mesDepartements' =>  $arrayMesDepartements,
        ]);
    }

    /**
     * @Route("/communespardepartement", name="communes_Par_Departement")
     * @param Request $request
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function communesParDepartement(Request $request, SerializerInterface $serializer): Response
    {
        $codeDepartement = $request->query->get('departement');
        $listeDepartement= file_get_contents('https://geo.api.gouv.fr/departements');
        $mesDepartements = $serializer->decode($listeDepartement,'json');

        if ($codeDepartement === null){
            $mesCommunes = file_get_contents('https://geo.api.gouv.fr/departements/01/communes');
        }else{
            $mesCommunes = file_get_contents('https://geo.api.gouv.fr/departements/'.$codeDepartement.'/communes');
        }
        //décodage du format jsoon en tableau
        $arrayMesCommunes = $serializer->decode($mesCommunes , 'json');

        return $this->render('api/communesParDepartement.html.twig', [
            'mesDepartements' => $mesDepartements,
            'mesCommunes' =>  $arrayMesCommunes,
        ]);
    }
}
