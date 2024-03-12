<?php echo "<?php\n"; ?>

namespace <?php echo $namespace; ?>;

<?php echo $use_statements; ?>

#[Route('/reset-password')]
class <?php echo $class_name; ?> extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Display & process form to request a password reset.
     */
    #[Route('', name: 'app_forgot_password_request')]
    public function request(Request $request, MailerInterface $mailer<?php if ($translator_available) { ?>, TranslatorInterface $translator<?php } ?>): Response
    {
        $form = $this->createForm(<?php echo $request_form_type_class_name; ?>::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processSendingPasswordResetEmail(
                $form->get('<?php echo $email_field; ?>')->getData(),
                $mailer<?php if ($translator_available) { ?>,
                $translator<?php } ?><?php echo "\n"; ?>
            );
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    /**
     * Confirmation page after a user has requested a password reset.
     */
    #[Route('/check-email', name: 'app_check_email')]
    public function checkEmail(): Response
    {
        // Generate a fake token if the user does not exist or someone hit this page directly.
        // This prevents exposing whether or not a user was found with the given email address or not
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        return $this->render('reset_password/check_email.html.twig', [
            'resetToken' => $resetToken,
        ]);
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     */
    #[Route('/reset/{token}', name: 'app_reset_password')]
    public function reset(Request $request, UserPasswordHasherInterface $passwordHasher<?php if ($translator_available) { ?>, TranslatorInterface $translator<?php } ?>, string $token = null): Response
    {
        if ($token) {
            // We store the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('app_reset_password');
        }

        $token = $this->getTokenFromSession();
        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('reset_password_error', sprintf(
                '%s - %s',
                <?php if ($translator_available) { ?>$translator->trans(<?php echo $problem_validate_message_or_constant; ?>, [], 'ResetPasswordBundle')<?php } else { ?><?php echo $problem_validate_message_or_constant; ?><?php } ?>,
                <?php if ($translator_available) { ?>$translator->trans($e->getReason(), [], 'ResetPasswordBundle')<?php } else { ?>$e->getReason()<?php } ?><?php echo "\n"; ?>
            ));

            return $this->redirectToRoute('app_forgot_password_request');
        }

        // The token is valid; allow the user to change their password.
        $form = $this->createForm(<?php echo $reset_form_type_class_name; ?>::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // A password reset token should be used only once, remove it.
            $this->resetPasswordHelper->removeResetRequest($token);

            // Encode(hash) the plain password, and set it.
            $encodedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user-><?php echo $password_setter; ?>($encodedPassword);
            $this->entityManager->flush();

            // The session is cleaned up after the password has been changed.
            $this->cleanSessionAfterReset();

            return $this->redirectToRoute('<?php echo $success_redirect_route; ?>');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }

    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer<?php if ($translator_available) { ?>, TranslatorInterface $translator<?php } ?>): RedirectResponse
    {
        $user = $this->entityManager->getRepository(<?php echo $user_class_name; ?>::class)->findOneBy([
            '<?php echo $email_field; ?>' => $emailFormData,
        ]);

        // Do not reveal whether a user account was found or not.
        if (!$user) {
            return $this->redirectToRoute('app_check_email');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            // If you want to tell the user why a reset email was not sent, uncomment
            // the lines below and change the redirect to 'app_forgot_password_request'.
            // Caution: This may reveal if a user is registered or not.
            //
            // $this->addFlash('reset_password_error', sprintf(
            //     '%s - %s',
            //     <?php if ($translator_available) { ?>$translator->trans(<?php echo $problem_handle_message_or_constant; ?>, [], 'ResetPasswordBundle')<?php } else { ?><?php echo $problem_handle_message_or_constant; ?><?php } ?>,
            //     <?php if ($translator_available) { ?>$translator->trans($e->getReason(), [], 'ResetPasswordBundle')<?php } else { ?>$e->getReason()<?php } ?><?php echo "\n"; ?>
            // ));

            return $this->redirectToRoute('app_check_email');
        }

        $email = (new TemplatedEmail())
            ->from(new Address('<?php echo $from_email; ?>', '<?php echo $from_email_name; ?>'))
            ->to($user-><?php echo $email_getter; ?>())
            ->subject('Your password reset request')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ])
        ;

        $mailer->send($email);

        // Store the token object in session for retrieval in check-email route.
        $this->setTokenObjectInSession($resetToken);

        return $this->redirectToRoute('app_check_email');
    }
}
