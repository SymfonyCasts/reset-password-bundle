# Simple POST request without twig in Controller

If your app is fully SPA (Single Page Application) with REST API, certainly you won't be using twig template for pages (except email).

## Get Ready

Run Symfony's Maker Bundle command `bin/console make:reset-password`. It will generate a lots of useful stuff like Form component, Twig Template, Controller, Entity and Repository.

But now, what we only need is the `ResetPasswordController` and the email template which will be sent to user's inbox after request.

## Example

Simple POST request example for `ResetPasswordController` (please modify it base on your needs):
```
// App\Controller\ResetPasswordController

/**
 * @Route("/reset-password")
 */
class ResetPasswordController extends AbstractController
{
    private $resetPasswordHelper;

    public function __construct(ResetPasswordHelperInterface $resetPasswordHelper)
    {
        $this->resetPasswordHelper = $resetPasswordHelper;
    }

    /**
     * Request a password reset.
     *
     * @Route("", name="app_forgot_password_request", methods={"POST"})
     */
    public function request(Request $request, MailerInterface $mailer)
    {
        return $this->processSendingPasswordResetEmail(
            $request->request->get('email'),
            $mailer
        );
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     *
     * @Route("/reset/{token}", name="app_reset_password")
     */
    public function reset(Request $request, UserPasswordEncoderInterface $passwordEncoder, string $token = null)
    {
        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            throw new BadRequestHttpException(sprintf(
                'There was a problem validating your reset request - %s',
                $e->getReason()
            ));
        }

        // The token is valid; allow the user to change their password.
        // A password reset token should be used only once, remove it.
        $this->resetPasswordHelper->removeResetRequest($token);

        // Encode the plain password, and set it.
        $encodedPassword = $passwordEncoder->encodePassword(
            $user,
            $request->request->get('password')
        );

        $user->setPassword($encodedPassword);
        $this->getDoctrine()->getManager()->flush();

        return $this->json(['message' => 'ok']);
    }

    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $emailFormData
        ]);

        if(!$user) // just return blank when user not found
        {
            return $this->json('', Response::HTTP_NO_CONTENT);
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            throw new BadRequestHttpException(sprintf(
                'There was a problem handling your password reset request - %s',
                $e->getReason()
            ));
        }

        // create your TemplateEmail() class and send it.

        return $this->json(['message' => 'A reset password email has been sent to user.']);
    }
}
```



Now try send a POST request (using Insomnia or Postman) to the route `/reset-password` with `email` parameter and you should receive a new mail in your inbox.



