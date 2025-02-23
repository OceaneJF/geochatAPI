<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Services\AddressAPIService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/message')]
class MessageController extends AbstractController
{
    #[Route('/', name: 'app_message_index', methods: ['GET'])]
    public function index(MessageRepository $messageRepository): Response
    {
        return $this->render('message/index.html.twig', [
            'messages' => $messageRepository->findAll(),
        ]);
    }

    #[Route('/map', name: 'app_message_map', methods: ['GET'])]
    public function map(MessageRepository $messageRepository): Response
    {
        return $this->render('message/map.html.twig', [
            'messages' => $messageRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_message_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MessageRepository $messageRepository, AddressAPIService $service): Response
    {
        $message = new Message();
        $message->setAddress("1 rue Saite Catherine, Bordeaux");
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
        $errorAddress = false;
        if ($form->isSubmitted() && $form->isValid()) {
            $errorAddress = !$message->fillLngLat($service);
            if (!$errorAddress) {
                $messageRepository->save($message, true);
                return $this->redirectToRoute('app_message_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->renderForm('message/new.html.twig', [
            'message' => $message,
            'form' => $form,
            'errorAddress' => $errorAddress
        ]);
    }

    #[Route('/{id}/edit', name: 'app_message_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Message $message, MessageRepository $messageRepository): Response
    {
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $messageRepository->save($message, true);

            return $this->redirectToRoute('app_message_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('message/edit.html.twig', [
            'message' => $message,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_message_delete', methods: ['POST'])]
    public function delete(Request $request, Message $message, MessageRepository $messageRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $message->getId(), $request->request->get('_token'))) {
            $messageRepository->remove($message, true);
        }

        return $this->redirectToRoute('app_message_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/find_addresses', name: 'app_message_find_addresses')]
    public function completeAddresses(MessageRepository $message, AddressAPIService $service, EntityManagerInterface $manager): Response
    {
        $noAddress = $message->findBy(['address' => ""]);

        if (count($noAddress) > 0) {
            $lnglat = [];
            foreach ($noAddress as $msg) {
                $lnglat[] = [$msg->getLatitude(), $msg->getLongitude()];
            }

            if ($lnglat) {
                $addresses = $service->getAddresses($lnglat);

                for ($i = 0; $i < count($addresses); $i++) {
                    $address = $addresses[$i];
                    $noAddress[$i]->setAddress($address);
                }

                $manager->flush();
            }
        }

        return $this->redirectToRoute('app_message_index', [], Response::HTTP_SEE_OTHER);
    }
}
