# Simple POST request without twig in Controller

In this example, we will use Symfony's Maker Bundle command `bin/console make:reset-password` to generate the files we need from this bundle. Read more at [readme](https://github.com/SymfonyCasts/reset-password-bundle/blob/master/README.md).


## Example

The `ResetPasswordController` and the email template is what we needed for this example.

In your `ResetPasswordController` (please modify it base on your needs):

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
     * Your frontend will call this api endpoint (ajax / axios)
     * to make a password reset request.
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
     * Your frontend will call this api endpoint (ajax / axios) 
     * when user submit their new password.
     *
     * @Route("/reset/{token}", name="app_reset_password", methods={"POST"})
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

        /**
         * The token is valid; allow the user to change their password.
         * 
         * A password reset token should be used only once, remove it.
         */
        $this->resetPasswordHelper->removeResetRequest($token);

        // Encode the plain password, and set it.
        $encodedPassword = $passwordEncoder->encodePassword(
            $user,
            $request->request->get('password')
        );

        $user->setPassword($encodedPassword);
        $this->getDoctrine()->getManager()->flush();

        return $this->json('', Response::HTTP_NO_CONTENT);
    }

    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $emailFormData
        ]);

        if(!$user)
        {
            throw $this->createNotFoundException('User not found.');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            throw new BadRequestHttpException(sprintf(
                'There was a problem handling your password reset request - %s',
                $e->getReason()
            ));
        }

        $email = (new TemplatedEmail())
            // change it to your sender
            ->from(new Address('your@example.com', 'Example'))
            ->to($user->getEmail())
            ->subject('Your password reset request')
            // change it to your email template location
            ->htmlTemplate('your_email_template_location')
            ->context([
                'resetToken' => $resetToken,
                'tokenLifetime' => $this->resetPasswordHelper->getTokenLifetime(),
            ])
        ;

        $mailer->send($email);

        return $this->json(['message' => 'A reset password email has been sent.']);
    }
```

In your email template:
```
<h1>Hi!</h1>

<p>
    To reset your password, please visit
    {# 
        Put your frontend reset password page url here and append it with the token.
        This link is where your user will visit after click the link.

        We temporarily hardcode it here, you may change it afterward.
    #}
    <a href="{{ 'https://www.example.com/reset-password/' ~ resetToken.token }}">here</a>
    This link will expire in {{ tokenLifetime|date('g') }} hour(s)..
</p>

<p>
    Cheers!
</p>
```

Now try send a POST request (using Insomnia or Postman) to the route `/reset-password` with `email` parameter and you should receive a new mail in your inbox.

After user clicks a link in their email, it should redirect to your SPA frontend page where user will key in their new password. When submit, your frontend should call api endpoint `/reset-password/reset/{token}` using ajax / axios with password parameter in body.

Congratulation, now you have POST request password reset without twig in your application.





