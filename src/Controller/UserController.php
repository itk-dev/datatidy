<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Controller;

use App\Anonymizer\UserAnonymizer;
use App\Entity\User;
use App\Form\UserType;
use App\ReferenceManager\UserReferenceManager;
use App\Repository\UserRepository;
use Exception;
use FOS\UserBundle\Model\UserManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/user", name="user_")
 * @IsGranted("ROLE_USER_ADMIN")
 */
class UserController extends AbstractController
{
    /** @var UserManagerInterface */
    private $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(Request $request, UserRepository $userRepository, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $userRepository->createQueryBuilder('e');

        $paginator = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1), /*page number*/
            50, /*limit per page*/
            [
                'defaultSortFieldName' => 'e.email',
                'defaultSortDirection' => 'asc',
            ]
        );

        return $this->render('user/index.html.twig', [
            'users' => $paginator,
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     */
    public function new(Request $request, TranslatorInterface $translator): Response
    {
        $user = $this->userManager->createUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userManager->updateUser($user);

            $this->addFlash('success', $translator->trans('User %user% created', ['%user%' => $user->getUsername()]));

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userManager->updateUser($user);

            $this->addFlash('success', $translator->trans('User %user% updated', ['%user%' => $user->getUsername()]));

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete", methods={"GET","DELETE", "UPDATE"})
     */
    public function delete(Request $request, User $user, TranslatorInterface $translator, UserReferenceManager $referenceManager, UserAnonymizer $anonymizer): Response
    {
        $parameters = [
            'user' => $user,
        ];

        $form = $this->createFormBuilder($user)
            ->setMethod('DELETE')
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $referenceManager->delete($user);
                $this->addFlash(
                    'success',
                    $translator->trans('User %user% deleted', [
                        '%user%' => $user->getUsername(),
                    ])
                );
            } catch (Exception $exception) {
                $this->addFlash(
                    'danger',
                    $translator->trans('Error deleting user %user%: %message%', [
                        '%user%' => $user->getUsername(),
                        '%message%' => $exception->getMessage(),
                    ])
                );
            }

            return $this->redirectToRoute('user_index');
        }

        $isAnonymized = $anonymizer->isAnonymized($user);

        $parameters['is_anonymized'] = $isAnonymized;

        if (!$isAnonymized) {
            $anonymizeForm = $this->createFormBuilder($user)
                ->setMethod('UPDATE')
                ->getForm();
            $anonymizeForm->handleRequest($request);
            if ($anonymizeForm->isSubmitted() && $anonymizeForm->isValid()) {
                try {
                    $anonymizer->anonymize($user);

                    $this->addFlash(
                        'success',
                        $translator->trans('User anonymized')
                    );
                } catch (Exception $exception) {
                    $this->addFlash(
                        'danger',
                        $translator->trans('Error anonymizing user %user%: %message%', [
                            '%user%' => $user->getUsername(),
                            '%message%' => $exception->getMessage(),
                        ])
                    );
                }

                return $this->redirectToRoute('user_index');
            }

            $parameters['anonymize_form'] = $anonymizeForm->createView();
        }

        $messages = $referenceManager->getDeleteMessages($user);

        if (!empty($messages)) {
            $parameters['messages'] = $messages;
        } else {
            $parameters['form'] = $form->createView();
        }

        return $this->render('user/delete.html.twig', $parameters);
    }
}
