<?php
declare(strict_types=1);

namespace Sitegeist\FusionForm\FriendlyCaptcha\Validation\Validator;

use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Uri;
use Neos\Flow\Core\Bootstrap;
use Neos\Flow\Http\Client\CurlEngine;
use Neos\Flow\Http\HttpRequestHandlerInterface;
use Neos\Flow\Annotations AS Flow;
use Neos\Flow\Validation\Validator\AbstractValidator;
use Throwable;

class FriendlyCaptchaValidator extends AbstractValidator
{
    /**
     * @Flow\InjectConfiguration(path="siteSecret")
     * @var string
     */
    protected $siteSecret;

    /**
     * @Flow\InjectConfiguration(path="siteKey")
     * @var string
     */
    protected $siteKey;

    /**
     * @Flow\Inject
     * @var Bootstrap
     */
    protected $bootstrap;

    protected $supportedOptions = [
        'siteKey' => [null, 'siteKey', 'string', false],
        'siteSecret' => [null, 'siteSecret', 'string', false]
    ];

    protected $acceptsEmptyValues = false;


    protected function isValid($captcha): void
    {
        $siteKey = $this->options['siteKey'] ?: $this->siteKey;
        $siteSecret = $this->options['siteSecret'] ?: $this->siteSecret;
        $captchaResponse = $captcha ?? false;
        if ($captchaResponse) {
            /** @phpstan-ignore-next-line */
            $client = new CurlEngine();
            $client->setOption(CURLOPT_RETURNTRANSFER, true );
            try {
                $response = $client->sendRequest(
                    new ServerRequest(
                        'POST',
                        new Uri('https://api.friendlycaptcha.com/api/v1/siteverify'),
                        [
                            'Content-Type' => 'application/json',
                        ],
                        json_encode([
                            'secret' => $siteSecret,
                            'solution' => $captchaResponse,
                            'sitekey' => $siteKey
                        ])
                    )
                );
            } catch (Throwable $exception) {
                $this->addError('Captcha is invalid.', 20260811113841);
                return;
            }

            if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
                $this->addError('Captcha is invalid.', 20260811113918);
                return;
            }

            $verificationResult = json_decode($response->getBody()->getContents() ?: '', true);

            if (!is_array($verificationResult) || ($verificationResult['success'] ?? false) !== true) {
                $this->addError('Captcha is invalid.', 20260811113928);
            }
            return;
        }
        $this->addError('Der Request konnte nicht gelesen werden.', 1649869170);
    }

}
