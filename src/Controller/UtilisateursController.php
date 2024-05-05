<?php

namespace App\Controller;

use App\Entity\Utilisateurs;
use App\Form\UtilisateursType;
use App\Repository\UtilisateursRepository;
use App\Form\LoginFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
#[Route('/utilisateurs')]
class UtilisateursController extends AbstractController
{
    private $passwordEncoder;
    private $logger;
    

    public function __construct(UserPasswordEncoderInterface $passwordEncoder,LoggerInterface $logger)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->logger = $logger;
    }

    #[Route('/', name: 'app_utilisateurs_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage): Response
    {
        $form = $this->createForm(LoginFormType::class);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $password = md5($form->get('password')->getData());
    
            $user = $entityManager->getRepository(Utilisateurs::class)->findOneBy(['email' => $email]);
    
            if (!$user) {
                return $this->redirectToRoute('app_utilisateurs_index', ['error' => 'Invalid email']);
            }
    
            if ($user->isBlocked()) {
                $this->addFlash('error', 'Your account is blocked.');
                return $this->redirectToRoute('app_utilisateurs_index');
            }
    
            if ($password === $user->getMotDePasse()) {
                $session = $request->getSession();
                $session->set('user', $user);
                $session->set('user_id', $user->getId());
                $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                $tokenStorage->setToken($token);

                if ($user->getRole() === 'admin') {
                    return $this->redirectToRoute('app_utilisateurs_back_index');
                } else {
                    return $this->redirectToRoute('app_home');
                }
            } else {
                return $this->redirectToRoute('app_utilisateurs_index', ['error' => 'Invalid password']);
            }
        }
    
        return $this->render('utilisateurs/login.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/verify', name: 'verify_email', methods: ['GET', 'POST'])]
    public function verifyEmail(Request $request, SessionInterface $session, MailerInterface $mailer, LoggerInterface $logger): Response
    {
        $user = $session->get('new_user');
    
        if (!$user) {
            return $this->redirectToRoute('error_page');
        }

        $verificationCode = $session->get('verification_code');
        if (!$verificationCode) {
            $verificationCode = mt_rand(100000, 999999);
            $session->set('verification_code', $verificationCode);

        }
        $recipientEmail = $user->getEmail();
        $subject = 'Verification Code';
        $message = 'Your verification code is: ' . $verificationCode;
        $this->sendEmail($recipientEmail, $subject, $message);

        if ($request->isMethod('POST')) {

            $submittedCode = $request->request->get('verificationCode');
    
            $logger->info('Submitted code: ' . $submittedCode);
            $logger->info('Verification code: ' . $verificationCode);

            if ($submittedCode == $verificationCode) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
    
                $session->remove('new_user');

                return $this->redirectToRoute('app_utilisateurs_index');
            }
        }
    
        return $this->render('utilisateurs/verify.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(SessionInterface $session): Response
    {
        $session->remove('user');
        return $this->redirectToRoute('app_utilisateurs_index');
    }

    #[Route('/new', name: 'app_utilisateurs_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $utilisateur = new Utilisateurs();
        $form = $this->createForm(UtilisateursType::class, $utilisateur);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Use MD5 encryption for password
            $utilisateur->setMotDePasse(md5($utilisateur->getMotDePasse()));
    
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
    
                try {
                    $imageFile->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle error
                }
    
                $utilisateur->setImage($newFilename);
            }
            $session->set('new_user', $utilisateur);
    
            return $this->redirectToRoute('verify_email');
        }
    
        return $this->renderForm('utilisateurs/new.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }
    function sendEmail($recipientEmail, $subject, $message)
    {
        $transport = new EsmtpTransport('smtp.gmail.com', 587);
        $transport->setUsername("oumaimaamdouni69@gmail.com");
        $transport->setPassword("jsqn nqkp dozd uxla");
    
        $mailer = new Mailer($transport);
    
        $email = (new Email())
            ->from("pidev@gmail.com")
            ->to($recipientEmail)
            ->subject($subject)
            ->text($message);
    
        $mailer->send($email);
    }

    #[Route('/{id}', name: 'app_utilisateurs_show', methods: ['GET'])]
    public function show(Utilisateurs $utilisateur): Response
    {
        return $this->render('utilisateurs/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    #[Route('/forget/password', name: 'app_forget_show', methods: ['GET'])]
    public function forgetEmail(): Response
    {
        return $this->render("utilisateurs/forgetEmail.html.twig");
    }

    #[Route('/forget', name: 'app_utilisateurs_verify', methods: ['POST'])]
    public function forgetUser(Request $request, SessionInterface $session,UtilisateursRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $email = $request->request->get('email');

        if ($session->has('verification_code')) {
            $this->logger->info("Using existing verification code from session: {$session->get('verification_code')}");
        } else {
            $numberOfDigits = 6;
            $minValue = 10 ** ($numberOfDigits - 1);
            $maxValue = (10 ** $numberOfDigits) - 1;
            $verificationCode = random_int($minValue, $maxValue);

            $session->set('verification_code', $verificationCode);
            $this->logger->info("Generated and saved new verification code: {$session->get('verification_code')}");
        }
        $this->sendEmail($email, "verification_code", 'Your verifcation code'.$session->get('verification_code'));
        
        return $this->render('utilisateurs/forget.html.twig', [
            'verificationCode' => $session->get('verification_code'),
            'email' => $email    
        ]);
    }

    function generateRandomPassword(int $length = 12): string
    {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $special = '!@#$%^&*()_-+=[]{}|;:,.<>?';
    
        $allCharacters = $lowercase . $uppercase . $numbers . $special;
        $password = '';
        $charactersLength = strlen($allCharacters);
    
        // Randomly select characters from the combined set to create the password
        for ($i = 0; $i < $length; $i++) {
            $randomIndex = rand(0, $charactersLength - 1);
            $password .= $allCharacters[$randomIndex];
        }
    
        return $password;
    }

    #[Route('/forget/post', name: 'app_utilisateurs_post')]
    public function forgetUser2(Request $request, SessionInterface $session,EntityManagerInterface $entityManager): Response
    {
        $verificationCode = $session->get('verification_code');
        $email = $request->request->get('email');

        $this->logger->error("I AM EMAIL". $email);
        $submittedVerificationCode = $request->request->get('verificationCode');
        $query = $entityManager->createQuery(
            'SELECT u FROM App\Entity\Utilisateurs u WHERE u.email = :email'
        );
        $query->setParameter('email', $email);

        $user = $query->getOneOrNullResult();
        $this->logger->error('I AM USER '.$user);

        if ($user) {
            if($submittedVerificationCode == $verificationCode) {
                $suggestedPassword = $this->generateRandomPassword();
                
                // Optional: send SMS with Twilio
                $twilioSid = 'AC20ae0e1faf415c0fac49503a19380fec';
                $twilioAuthToken = 'fe043a5560a4b59ad91b2eead8e6bba5';
                $twilioPhoneNumber = '+21627229906';
                $twilio = new \Twilio\Rest\Client($twilioSid, $twilioAuthToken);
                $userPhoneNumber = '+21627229906';
                $twilio->messages->create(
                    $userPhoneNumber,
                    [
                        "from" => '+17179678278',
                        "body" => "Your verification code is: {$session->get('verification_code')}"
                    ]
                );
                $this->sendEmail($user->getEmail(), $suggestedPassword, $suggestedPassword);
                $user->setMotDePasse(md5($suggestedPassword));
                $entityManager->flush();
                $session->remove('verification_code');
                $this->addFlash('success', 'Your password has been reset successfully.');
                return $this->redirectToRoute('app_utilisateurs_index');
                }
            }
            else {
                return new JsonResponse(['error' => 'Code Error '. $submittedVerificationCode .' '. $verificationCode], 404); // Return a JSON response with an error message
            }
    }

    #[Route('/{id}/edit', name: 'app_utilisateurs_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Utilisateurs $utilisateur, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UtilisateursType::class, $utilisateur);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $utilisateur->setMotDePasse(md5($utilisateur->getMotDePasse()));
    
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
    
                try {
                    $imageFile->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle error
                }
    
                $utilisateur->setImage($newFilename);
            }
    
            $entityManager->flush();
    
            return $this->redirectToRoute('app_utilisateurs_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('utilisateurs/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_utilisateurs_delete', methods: ['POST'])]
    public function delete(Request $request, Utilisateurs $utilisateur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$utilisateur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($utilisateur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_utilisateurs_index', [], Response::HTTP_SEE_OTHER);
    }
}
