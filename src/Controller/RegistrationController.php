<?php

namespace App\Controller;

use App\Entity\Admin\Employed;
use App\Form\RegistrationForm2Type;
use App\Form\RegistrationFormType;
use App\Repository\Admin\EmployedRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/security/register', name: 'op_webapp_security_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Employed();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $user->setIsVerified(1);
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('op_webapp_security_verifyemail', $user,
                (new TemplatedEmail())
                    ->from(new Address('contact@papsimmo40.fr', 'Contact Paps Immo 40'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            return $this->redirectToRoute('op_admin_dashboard_index');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/security/register/prescriber', name: 'op_webapp_security_register_prescriber')]
    public function registerPrescriber(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, EmployedRepository $employedRepository): Response
    {
        $user = new Employed();
        $form = $this->createForm(RegistrationForm2Type::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $code = $form->get('numCollaborator')->getData();

            $collaborateur = $employedRepository->findOneBy(['numCollaborator'=> $code]);
            //dd($collaborateur);
            if($collaborateur){
                // encode the plain password
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $user->setReferent($collaborateur);
                $user->setRoles(['ROLE_PRESCRIBER']);

                $entityManager->persist($user);
                $entityManager->flush();

                // generate a signed url and email it to the user
                $this->emailVerifier->sendEmailConfirmation('op_webapp_security_verifyemail', $user,
                    (new TemplatedEmail())
                        ->from(new Address('contact@papsimmo40.fr', 'Contact Paps Immo 40'))
                        ->to($user->getEmail())
                        ->subject('Please Confirm your Email')
                        ->htmlTemplate('registration/confirmation_email2.html.twig')
                );
                $this->addFlash('success', 'Votre compte est crée. Toutefois, nous controlons si cette inscription est issu d\'un être humain et nom d\'un robot informatique en vous envoyant un e-mail de confirmation à l\'adresse indiquée. L\'inscription sera définitive après validation de ce mail de votre part.');
                // do anything else you need here, like send an email
                return $this->redirectToRoute('op_admin_dashboard_index');
            }else{
                $this->addFlash('error_collaborator', "Le collaborateur n'existe pas.");
                return $this->render('registration/register2.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }
        }

        return $this->render('registration/register2.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/security/verify/email', name: 'op_webapp_security_verifyemail')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Votre adresse a bien été vérifiée.');

        return $this->redirectToRoute('op_webapp_public_homepage');
    }
}
