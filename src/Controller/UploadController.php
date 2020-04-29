<?php

namespace App\Controller;


use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class UploadController extends AbstractController
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @Route("/upload", name="upload")
     */
    public function upload(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('file', FileType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $response = $this->client->post('http://84.22.103.13:8001/api/auth/', [
                'form_params' => [
                    'username' => 'user1',
                    'password' => 'ESSp5ZkTp4ph4Px'
                ]
            ]);

            $token = json_decode($response->getBody(), true)['token'];

            $response = $this->client->post('http://84.22.103.13:8001/api/upload/', [
                'headers' => [
                    'Authorization' => 'Token ' . $token
                ],
                'multipart' => [
                    [
                        'name'     => 'file',
                        'contents' => fopen((string) $form->get('file')->getData(), 'rb'),
                    ],
                    [
                        'name'     =>'source_id',
                        'contents' =>'FEWioee123123',
                    ],
                    [
                        'name'     =>'test_type',
                        'contents' =>'VD',
                    ]
                ]
            ]);

            return new JsonResponse(json_decode((string) $response->getBody(), true));
        }

        return $this->render('upload-form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
