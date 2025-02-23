<?php

namespace App\Controller;

use App\Entity\Message;
use App\Repository\MessageRepository;
use App\Services\AddressAPIService;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ApiController extends AbstractController
{

    #[View(serializeGroups: ["message_basic"])]
    #[Route('/messages', name: 'app_api_index', methods: 'GET')]
    public function index(
        MessageRepository $messagesRepository,
        #[MapQueryParameter()]  string $address,
        #[MapQueryParameter()]  ?float $radius = 2000,
        AddressAPIService $apiService
    ) {
        $latLong = $apiService->getLngLat($address);

        $query = $messagesRepository->findClose($latLong[0], $latLong[1], $radius)
            ->setMaxResults(10)->orderBy('m.date', 'DESC');

        $messages = $query->getQuery()->getResult();

        return ["messages" => $messages];
    }


    #[View(serializeGroups: ["message_basic"])]
    #[Route('/message', name: 'app_api_new', methods: 'POST')]
    public function new(
        MessageRepository $messagesRepository,
        AddressAPIService $apiService,
        #[MapRequestPayload(serializationContext: ["groups" => ["message_new"]])] Message $message
    ) {
        $message->fillLngLat($apiService);

        $messagesRepository->save($message, true);

        return ["message" => $message];
    }
}
